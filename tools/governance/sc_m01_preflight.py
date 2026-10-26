#!/usr/bin/env python3
"""Read-only SC-M01 canonical input *fingerprint preflight* (not an approval gate).

Checks files in the given WORKTREE, not necessarily GitHub main. Exact byte
matches to pinned Git blob SHAs are necessary evidence, never sufficient for
owner approval, independent review, or authorization to implement SC-M01.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import re
import sys
from pathlib import Path
from typing import Any

REQUIRED_IDS = frozenset({
    "API", "IPC", "PERMISSIONS", "SECURITY",
    "DATABASE", "QA", "DEVOPS", "ENGINEERING",
})
PIN = re.compile(r"^[0-9a-f]{40}$")
ALLOWED_PREFIXES = {
    "API": "specifications/api/",
    "IPC": "specifications/ipc/",
    "PERMISSIONS": "specifications/security/",
    "SECURITY": "specifications/security/",
    "DATABASE": "specifications/database/",
    "QA": "specifications/tests/",
    "DEVOPS": "specifications/devops/",
    "ENGINEERING": "specifications/engineering/",
}
DEFAULT_MANIFEST = (
    "governance/verification/"
    "SC-M01-CANONICAL-INPUT-FINGERPRINTS-2026-10-26.json"
)


def git_blob_sha(content: bytes) -> str:
    """Return SHA-1 in the Git blob object namespace, not plain file SHA-1."""
    prefix = b"blob " + str(len(content)).encode("ascii") + b"\0"
    return hashlib.sha1(prefix + content).hexdigest()


def _safe_repo_file(repo: Path, relative: str) -> Path:
    if (
        not isinstance(relative, str)
        or not relative
        or relative.startswith("/")
        or "\\" in relative
        or any(part in ("", ".", "..") for part in relative.split("/"))
    ):
        raise ValueError("unsafe repository path")
    root = repo.resolve()
    target = root / relative
    current = root
    for component in Path(relative).parts:
        current = current / component
        if current.is_symlink():
            raise ValueError("symlink artifact path is forbidden")
    resolved = target.resolve(strict=False)
    if not resolved.is_relative_to(root):
        raise ValueError("artifact path escapes repository")
    return target


def check_manifest(data: dict[str, Any]) -> list[dict[str, Any]]:
    if data.get("schema_version") != 1:
        raise ValueError("unsupported manifest schema_version")
    artifacts = data.get("artifacts")
    if not isinstance(artifacts, list) or len(artifacts) != 8:
        raise ValueError("expected exactly eight artifact entries")
    ids = [a.get("id") if isinstance(a, dict) else None for a in artifacts]
    if any(not isinstance(artifact_id, str) for artifact_id in ids):
        raise ValueError("artifact id must be a string")
    if len(set(ids)) != 8 or set(ids) != REQUIRED_IDS:
        raise ValueError("artifact set differs from SC-M01 required 8")
    for item in artifacts:
        if not isinstance(item.get("version"), str) or not item["version"]:
            raise ValueError("every artifact requires a version label")
        path = item.get("path")
        if path is not None and not isinstance(path, str):
            raise ValueError("artifact path must be a string or null")
        if path is not None and not path.startswith(ALLOWED_PREFIXES[item["id"]]):
            raise ValueError("artifact path outside canonical owner directory")
        expected = item.get("expected_git_blob_sha")
        if expected is not None and (
            not isinstance(expected, str) or not PIN.fullmatch(expected)
        ):
            raise ValueError("expected_git_blob_sha must be a 40-character hex SHA")
        if expected is not None and path is None:
            raise ValueError("pinned artifact cannot have an unset path")
    return artifacts


def inspect(repo: Path, data: dict[str, Any]) -> dict[str, Any]:
    artifacts = check_manifest(data)
    rows = []
    matched = 0
    physically_present = 0

    for artifact in artifacts:
        name = artifact["id"]
        relative = artifact.get("path")
        expected = artifact.get("expected_git_blob_sha")
        row: dict[str, Any] = {
            "id": name,
            "path": relative,
            "expected_git_blob_sha": expected,
        }

        if relative is None:
            row["status"] = "PATH_NOT_DEFINED"
        else:
            path = _safe_repo_file(repo, relative)
            if not path.is_file():
                row["status"] = "NOT_PRESENT"
            else:
                physically_present += 1
                actual = git_blob_sha(path.read_bytes())
                row["actual_git_blob_sha"] = actual
                if expected is None:
                    row["status"] = "NOT_PINNED"
                elif actual != expected:
                    row["status"] = "HASH_MISMATCH"
                else:
                    row["status"] = "PIN_MATCH"
                    matched += 1
        rows.append(row)

    return {
        "scope": "LOCAL_WORKTREE_ONLY",
        "publication_authority": "NONE",
        "implementation_authorization": "NONE",
        "manifest_source_main_sha": data.get("source_main_sha"),
        "expected_artifacts": 8,
        "physically_present_in_worktree": physically_present,
        "exact_pins_matching": matched,
        "fingerprint_preflight_complete": matched == 8,
        "owner_approval_verified": False,
        "e3_independent_review_verified": False,
        "source_code_gate_released": False,
        "artifacts": rows,
    }


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--repo", type=Path, default=Path(__file__).resolve().parents[2])
    parser.add_argument("--manifest", default=DEFAULT_MANIFEST)
    parser.add_argument("--format", choices=("text", "json"), default="text")
    parser.add_argument(
        "--require-all-pinned", action="store_true",
        help="return exit 2 unless all eight local artifacts match an approved pin",
    )
    args = parser.parse_args(argv)

    try:
        manifest_path = _safe_repo_file(args.repo, args.manifest)
        manifest = json.loads(manifest_path.read_text(encoding="utf-8"))
        if not isinstance(manifest, dict):
            raise ValueError("manifest root must be an object")
        report = inspect(args.repo, manifest)
    except (ValueError, OSError, json.JSONDecodeError) as exc:
        print("SC-M01 preflight error: " + str(exc), file=sys.stderr)
        return 3

    if args.format == "json":
        print(json.dumps(report, indent=2, sort_keys=True))
    else:
        print("SC-M01 LOCAL WORKTREE FINGERPRINT PREFLIGHT (NOT OWNER APPROVAL)")
        for row in report["artifacts"]:
            print(f"{row['id']:13s} {row['status']:17s} {row['path'] or '(owner path pending)'}")
        print(
            f"Exact pinned: {report['exact_pins_matching']}/8; "
            f"physical files: {report['physically_present_in_worktree']}/8"
        )
        print("Owner approval/E3/implementation authorization: NOT VERIFIED")
    if args.require_all_pinned and not report["fingerprint_preflight_complete"]:
        return 2
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
