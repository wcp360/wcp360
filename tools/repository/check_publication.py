#!/usr/bin/env python3
"""Inventory a committed Git snapshot and verify the WCP360 mirror manifest.

Read-only Git inspection; no network or source execution. The checker is anchored
to an explicit repository path, strips inherited Git configuration/environment,
disables replacement objects/protocol access and refuses gitlinks so an inventory
cannot silently omit external repository content.

Results certify repository content against the committed manifest, not unseen
source archives, owner approvals, semantic correctness or secret safety.
"""
from __future__ import annotations

import argparse
from collections import Counter
import hashlib
import json
import os
from pathlib import Path, PurePosixPath
import re
import shutil
import subprocess
import sys
import tempfile

MIRROR = "documentation/library-mirror/"
MANIFEST = MIRROR + "MANIFEST.md"
ROW = re.compile(
    r"^\|\s*(\d+)\s*\|\s*\x60([^\x60]+)\x60\s*\|\s*\x60([0-9a-f]{40})\x60\s*\|\s*(\d+)\s*\|$",
    re.M,
)
OID = re.compile(r"[0-9a-f]{40}\Z")
MAX_FILES = 10000
MAX_BYTES = 256 * 1024 * 1024
MAX_BLOB = 32 * 1024 * 1024
MAX_TREE_LISTING = 16 * 1024 * 1024


class InventoryError(Exception):
    """Fixed checker error without forwarding Git stderr/stdout."""


def _env(home: Path) -> dict[str, str]:
    return {
        "PATH": os.environ.get("PATH", ""),
        "HOME": str(home),
        "XDG_CONFIG_HOME": str(home / "xdg"),
        "LC_ALL": "C",
        "GIT_CONFIG_NOSYSTEM": "1",
        "GIT_CONFIG_GLOBAL": os.devnull,
        "GIT_CONFIG_SYSTEM": os.devnull,
        "GIT_TERMINAL_PROMPT": "0",
        "GIT_OPTIONAL_LOCKS": "0",
        "GIT_NO_LAZY_FETCH": "1",
    }


def inspect(repo: Path, ref: str, *, inventory_only: bool = False) -> dict:
    if ref != "HEAD" and not OID.fullmatch(ref):
        raise InventoryError("INVALID_REF")
    git = shutil.which("git")
    if not git:
        raise InventoryError("GIT_UNAVAILABLE")
    repo = repo.resolve()
    if not repo.is_dir():
        raise InventoryError("REPOSITORY_UNAVAILABLE")

    with tempfile.TemporaryDirectory(prefix="wcp-publication-home-") as folder:
        env = _env(Path(folder))
        prefix = [
            git, "--no-pager", "--no-replace-objects", "-C", str(repo),
            "-c", f"core.hooksPath={os.devnull}",
            "-c", f"core.excludesFile={os.devnull}",
            "-c", "core.fsmonitor=false",
            "-c", "protocol.allow=never",
        ]

        def run(*args: str, timeout: int = 30) -> bytes:
            try:
                result = subprocess.run(
                    prefix + list(args), env=env, capture_output=True,
                    check=False, timeout=timeout,
                )
            except subprocess.TimeoutExpired:
                raise InventoryError("GIT_TIMEOUT") from None
            if result.returncode != 0:
                raise InventoryError("GIT_READ_FAILED")
            return result.stdout

        root = run("rev-parse", "--show-toplevel").rstrip(b"\n")
        if not root:
            raise InventoryError("REPOSITORY_ROOT_UNAVAILABLE")
        repo_root = Path(os.fsdecode(root)).resolve()
        prefix[4] = str(repo_root)

        commit = run("rev-parse", "--verify", ref + "^{commit}").decode("ascii").strip()
        if not OID.fullmatch(commit):
            raise InventoryError("UNSUPPORTED_OBJECT_FORMAT")
        tree = run("rev-parse", "--verify", commit + "^{tree}").decode("ascii").strip()
        if not OID.fullmatch(tree):
            raise InventoryError("UNSUPPORTED_OBJECT_FORMAT")

        listing = run("ls-tree", "--full-tree", "-r", "-l", "-z", commit)
        if len(listing) > MAX_TREE_LISTING:
            raise InventoryError("TREE_LISTING_LIMIT")
        parts = listing.split(b"\0")
        if not parts or parts[-1] != b"":
            raise InventoryError("INVALID_TREE")
        parts.pop()

        records = []
        for entry in parts:
            try:
                meta, raw_path = entry.split(b"\t", 1)
                mode, kind, oid, size = meta.split()
            except ValueError:
                raise InventoryError("INVALID_TREE") from None
            if kind != b"blob":
                raise InventoryError("UNSUPPORTED_TREE_ENTRY")
            if mode not in (b"100644", b"100755", b"120000"):
                raise InventoryError("UNSUPPORTED_TREE_ENTRY")
            if not re.fullmatch(rb"[0-9a-f]{40}", oid) or not size.isdigit():
                raise InventoryError("INVALID_TREE")
            try:
                path = raw_path.decode("utf-8")
            except UnicodeDecodeError:
                raise InventoryError("NON_UTF8_PATH") from None
            records.append({
                "path": path,
                "mode": mode.decode("ascii"),
                "type": "symlink" if mode == b"120000" else "blob",
                "git_blob": oid.decode("ascii"),
                "bytes": int(size),
            })

        if len(records) > MAX_FILES:
            raise InventoryError("FILE_COUNT_LIMIT")
        total_bytes = sum(r["bytes"] for r in records)
        if total_bytes > MAX_BYTES:
            raise InventoryError("TOTAL_SIZE_LIMIT")
        if any(r["bytes"] > MAX_BLOB for r in records):
            raise InventoryError("BLOB_SIZE_LIMIT")

        content: dict[str, bytes] = {}
        for rec in records:
            oid = rec["git_blob"]
            if oid not in content:
                content[oid] = run("cat-file", "blob", oid)
            data = content[oid]
            if len(data) != rec["bytes"]:
                raise InventoryError("BLOB_SIZE_MISMATCH")
            actual = hashlib.sha1(
                b"blob " + str(len(data)).encode("ascii") + b"\0" + data
            ).hexdigest()
            if actual != oid:
                raise InventoryError("BLOB_IDENTITY_MISMATCH")
            rec["sha256"] = hashlib.sha256(data).hexdigest()

        by_path = {r["path"]: r for r in records}
        if len(by_path) != len(records):
            raise InventoryError("DUPLICATE_PATH")
        if inventory_only:
            return {
                "format": "WCP360-PUBLICATION-INVENTORY/3",
                "commit": commit, "tree": tree,
                "scope": "complete committed Git metadata inventory only",
                "overall_project_publication": "NOT_CERTIFIED_COMPLETE",
                "source_library_byte_equivalence": "NOT_APPLICABLE",
                "application_tests_executed": 0, "secret_scan": False,
                "file_count": len(records),
                "regular_file_count": sum(r["type"] == "blob" for r in records),
                "symlink_count": sum(r["type"] == "symlink" for r in records),
                "total_bytes": total_bytes,
                "mirror": {
                    "status": "NOT_APPLICABLE",
                    "reason": "EXPLICIT_METADATA_INVENTORY_ONLY_MODE",
                    "verified_against_manifest": False,
                },
                "audit": {
                    "status": "NOT_APPLICABLE",
                    "reason": "HISTORICAL_AUDIT_NOT_CHECKED_IN_INVENTORY_MODE",
                    "missing": [],
                },
                "records": sorted(records, key=lambda r: r["path"]),
            }
        if MANIFEST not in by_path or by_path[MANIFEST]["type"] != "blob":
            raise InventoryError("MISSING_MIRROR_MANIFEST")

        try:
            text = content[by_path[MANIFEST]["git_blob"]].decode("utf-8")
        except UnicodeDecodeError:
            raise InventoryError("INVALID_MANIFEST_ENCODING") from None
        rows = ROW.findall(text)
        count = re.search(
            r"^SOURCE MATERIALIZED MARKDOWN FILES\s*=\s*(\d+)\s*$", text, re.M
        )
        total = re.search(r"^TOTAL SOURCE BYTES\s*=\s*(\d+)\s*$", text, re.M)
        if not count or not total or not rows or len(rows) != int(count[1]):
            raise InventoryError("INVALID_MANIFEST_COUNT")

        expected: dict[str, dict[str, int | str]] = {}
        matches: list[str] = []
        missing: list[str] = []
        changed: list[dict] = []
        for number, rel, oid, size in rows:
            if int(number) != len(expected) + 1:
                raise InventoryError("INVALID_MANIFEST_NUMBERING")
            p = PurePosixPath(rel)
            if p.is_absolute() or ".." in p.parts or str(p) != rel or "\\" in rel:
                raise InventoryError("UNSAFE_MANIFEST_PATH")
            path = MIRROR + rel
            if path in expected:
                raise InventoryError("DUPLICATE_MANIFEST_PATH")
            expected[path] = {"git_blob": oid, "bytes": int(size)}
            actual = by_path.get(path)
            if actual is None:
                missing.append(path)
            elif (
                actual["type"] != "blob"
                or actual["mode"] not in ("100644", "100755")
                or actual["git_blob"] != oid
                or actual["bytes"] != int(size)
            ):
                changed.append({"path": path, "expected": expected[path], "actual": actual})
            else:
                matches.append(path)

        if sum(int(v["bytes"]) for v in expected.values()) != int(total[1]):
            raise InventoryError("INVALID_MANIFEST_BYTES")

        metadata = {MIRROR + "README.md", MANIFEST}
        extras = sorted(
            p for p in by_path
            if p.startswith(MIRROR) and p not in expected and p not in metadata
        )

        audit_paths = [
            "governance/audits/AUDIT-WCP-0100/" + p
            for p in ("README.md", "INDEX.md", "COMPLEMENT-2026-10-02.md",
                      "gitignore-reproduction.json")
        ]
        canonical = {
            "API": "specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md",
            "IPC": "specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md",
            "Permission": "specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md",
            "Security": "specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md",
            "DevOps": "specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md",
            "Database": "specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md",
            "Engineering": "specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md",
        }
        statuses = {k: {"path": p, "present": p in by_path} for k, p in canonical.items()}
        statuses["QA"] = {
            "path": None, "present": None,
            "status": "FINAL_OWNER_FILENAME_NOT_IDENTIFIED",
            "directory_candidates": sorted(
                p for p in by_path if p.startswith("specifications/tests/")
            ),
        }

        prefixes = [
            "documentation/design-system/V2.5.3.36.4/",
            "documentation/snapshots/2026-09-27/",
        ]
        groups = {
            prefix_name: {
                "files": sum(r["path"].startswith(prefix_name) for r in records),
                "bytes": sum(r["bytes"] for r in records if r["path"].startswith(prefix_name)),
            }
            for prefix_name in prefixes
        }
        design_prefix = prefixes[0]
        source_prefix = MIRROR + "_GITHUB-STAGING-DESIGN-FULL-V2.5.3.36.4/"
        design_projection = []
        for path, rec in by_path.items():
            if path.startswith(source_prefix):
                target = design_prefix + path[len(source_prefix):]
                other = by_path.get(target)
                design_projection.append({
                    "source": path, "target": target,
                    "blob_equal": bool(other and rec["git_blob"] == other["git_blob"]),
                })

        return {
            "format": "WCP360-PUBLICATION-INVENTORY/2",
            "commit": commit, "tree": tree,
            "scope": "complete Git blob/symlink snapshot + committed mirror manifest",
            "overall_project_publication": "NOT_CERTIFIED_COMPLETE",
            "source_library_byte_equivalence": "NOT_CHECKED_BY_THIS_SCRIPT",
            "application_tests_executed": 0, "secret_scan": False,
            "file_count": len(records),
            "regular_file_count": sum(r["type"] == "blob" for r in records),
            "symlink_count": sum(r["type"] == "symlink" for r in records),
            "total_bytes": total_bytes,
            "extension_counts": dict(sorted(Counter(
                Path(r["path"]).suffix.lower() or "(none)" for r in records
            ).items())),
            "top_level_counts": dict(sorted(Counter(
                r["path"].split("/")[0] for r in records
            ).items())),
            "mirror": {
                "expected": len(expected), "exact_matches": len(matches),
                "manifest_bytes": int(total[1]), "missing": missing,
                "changed": changed, "unlisted": extras,
                "verified_against_manifest": not (missing or changed or extras),
            },
            "audit": {
                "expected": len(audit_paths),
                "present": sum(p in by_path for p in audit_paths),
                "missing": [p for p in audit_paths if p not in by_path],
            },
            "canonical_presence": statuses,
            "document_groups": groups,
            "design_mirror_projection": design_projection,
            "records": sorted(records, key=lambda r: r["path"]),
        }


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--repo", type=Path, default=Path.cwd())
    parser.add_argument("--ref", default="HEAD")
    parser.add_argument("--output", type=Path, required=True)
    parser.add_argument("--inventory-only", action="store_true",
                        help="metadata inventory only; never verifies historical mirror integrity")
    args = parser.parse_args(argv)
    try:
        report = inspect(args.repo, args.ref, inventory_only=args.inventory_only)
        args.output.parent.mkdir(parents=True, exist_ok=True)
        with args.output.open("x", encoding="utf-8") as handle:
            handle.write(json.dumps(report, indent=2, ensure_ascii=False) + "\n")
        print(json.dumps(
            {k: report[k] for k in ("commit", "file_count", "total_bytes", "mirror", "audit")},
            ensure_ascii=True,
        ))
        return 0 if args.inventory_only or (report["mirror"]["verified_against_manifest"] and not report["audit"]["missing"]) else 1
    except (OSError, InventoryError, UnicodeError, subprocess.SubprocessError) as exc:
        code = str(exc) if isinstance(exc, InventoryError) else "CHECK_FAILED"
        print(json.dumps({"status": "ERROR", "error_code": code}, ensure_ascii=True),
              file=sys.stderr)
        return 2


if __name__ == "__main__":
    sys.exit(main())
