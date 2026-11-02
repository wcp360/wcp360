#!/usr/bin/env python3
"""Prepare bounded historical SC-M01 fixtures outside the development checkout.

No network, source execution, approval or current Core qualification. Historical
bytes come from two immutable Git commits; the current five relocated inputs are
checked separately against the unchanged Oct30 manifest.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import os
from pathlib import Path, PurePosixPath
import shutil
import subprocess
import sys
import tempfile

from sc_m01_preflight import check_manifest, git_blob_sha, inspect

CANONICAL_COMMIT = "580e9ed58286304ade30142caf9415f4b87c0c28"
QA_COMMIT = "a1c7a2743c300f1cc426485267af1be66dcdac3d"
MANIFEST = "governance/verification/SC-M01-CANONICAL-INPUT-FINGERPRINTS-2026-10-30.json"
SNAPSHOTS = (
    MANIFEST,
    "governance/verification/SC-M01-CANONICAL-INPUT-FINGERPRINTS-2026-10-26.json",
    "governance/verification/SC-M01-CONTRACT-TRACEABILITY-2026-10-26.json",
)
CURRENT_IDS = frozenset({"API", "IPC", "PERMISSIONS", "SECURITY", "DATABASE"})
QA_INPUTS = (
    "governance/proposals/WCP360_SC-M01_Test_Baseline_v1.0_SINGLE_FILE_OWNER_SIGNOFF_CANDIDATE.md",
    "governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md",
    "governance/proposals/SC-M01-QA-133-TEST-RECORDS-OWNER-CANDIDATE-v0.1.json",
    "governance/proposals/SC-M01-QA-66-SECURITY-TEST-ANNEX-OWNER-CANDIDATE-v0.1.json",
    "governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json",
    "governance/proposals/SC-M01-QA-A01-A20-OWNER-DECISION-PACKET-v0.1.md",
    "governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md",
    "governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md",
    "governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md",
    "governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md",
    "governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md",
    "governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md",
    "governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md",
)
TOOLS = tuple(
    "tools/governance/" + name + ".py"
    for base in (
        "sc_m01_preflight", "sc_m01_contract_traceability",
        "sc_m01_privileged_mapping_check", "sc_m01_qa_annex_consistency",
        "sc_m01_qa_single_file_check",
    )
    for name in (base, "test_" + base)
)
MAX_BLOB = 700_000
MAX_TOTAL = 8 * 1024 * 1024


class FixtureError(ValueError):
    """Fixed error code; do not forward Git diagnostics or arbitrary paths."""


def safe_file(root: Path, relative: str) -> Path:
    p = PurePosixPath(relative)
    if not relative or p.is_absolute() or "\\" in relative or str(p) != relative or any(
        part in ("", ".", "..") for part in p.parts
    ):
        raise FixtureError("UNSAFE_FIXTURE_PATH")
    candidate = root
    for part in p.parts:
        candidate = candidate / part
        if candidate.is_symlink():
            raise FixtureError("SYMLINKED_FIXTURE_PATH")
    if not candidate.resolve().is_relative_to(root.resolve()):
        raise FixtureError("FIXTURE_PATH_ESCAPE")
    return candidate


def read_current(root: Path, relative: str) -> bytes:
    p = safe_file(root, relative)
    if not p.is_file():
        raise FixtureError("CURRENT_INPUT_MISSING")
    raw = p.read_bytes()
    if len(raw) > MAX_BLOB:
        raise FixtureError("FIXTURE_SIZE_LIMIT")
    return raw


def current_report(repo: Path) -> dict:
    manifest = json.loads(read_current(repo, "documentation/" + MANIFEST))
    if not isinstance(manifest, dict):
        raise FixtureError("INVALID_MANIFEST_OBJECT")
    report = inspect(repo, manifest, input_layout="documentation")
    rows = {row["id"]: row for row in report["artifacts"]}
    if any(rows[name]["status"] != "PIN_MATCH" for name in CURRENT_IDS):
        raise FixtureError("CURRENT_REQUIRED_PIN_MISMATCH")
    if any(row["status"] not in ("PIN_MATCH", "NOT_PRESENT") for row in rows.values()):
        raise FixtureError("CURRENT_INPUT_DRIFT")
    report["scope"] = "CURRENT_CHECKOUT_RELOCATED_HISTORICAL_INPUTS"
    report["required_relocated_ids"] = sorted(CURRENT_IDS)
    report["current_core_compatibility_verified"] = False
    return report


def export(repo: Path, output: Path) -> dict:
    repo = repo.resolve()
    if not repo.is_dir():
        raise FixtureError("REPOSITORY_UNAVAILABLE")
    # A fresh external directory prevents restoring historical files into Core.
    if output.is_symlink() or output.exists() or output.resolve().is_relative_to(repo):
        raise FixtureError("OUTPUT_MUST_BE_FRESH_AND_EXTERNAL")
    if not output.parent.is_dir() or output.parent.is_symlink():
        raise FixtureError("OUTPUT_PARENT_UNAVAILABLE")
    manifest_bytes = read_current(repo, "documentation/" + MANIFEST)
    manifest = json.loads(manifest_bytes)
    if not isinstance(manifest, dict):
        raise FixtureError("INVALID_MANIFEST_OBJECT")
    artifacts = check_manifest(manifest)
    if manifest.get("source_main_sha") != CANONICAL_COMMIT:
        raise FixtureError("HISTORICAL_SOURCE_COMMIT_DRIFT")
    git = shutil.which("git")
    if not git:
        raise FixtureError("GIT_UNAVAILABLE")
    raw_files: list[tuple[str, bytes, str]] = []
    with tempfile.TemporaryDirectory(prefix="wcp-scm01-git-home-") as folder:
        env = {
            "PATH": os.environ.get("PATH", ""), "HOME": folder,
            "XDG_CONFIG_HOME": str(Path(folder) / "xdg"), "LC_ALL": "C",
            "GIT_CONFIG_NOSYSTEM": "1", "GIT_CONFIG_GLOBAL": os.devnull,
            "GIT_CONFIG_SYSTEM": os.devnull, "GIT_TERMINAL_PROMPT": "0",
            "GIT_OPTIONAL_LOCKS": "0", "GIT_NO_LAZY_FETCH": "1",
        }
        prefix = [
            git, "--no-pager", "--no-replace-objects", "-C", str(repo),
            "-c", "protocol.allow=never", "-c", "core.hooksPath=" + os.devnull,
            "-c", "core.fsmonitor=false",
        ]

        def historical(commit: str, path: str) -> bytes:
            result = subprocess.run(prefix + ["show", commit + ":" + path],
                                    env=env, capture_output=True, timeout=20,
                                    check=False)
            if result.returncode != 0:
                raise FixtureError("HISTORICAL_GIT_INPUT_UNAVAILABLE")
            if len(result.stdout) > MAX_BLOB:
                raise FixtureError("FIXTURE_SIZE_LIMIT")
            return result.stdout

        for row in artifacts:
            path = row.get("path")
            pin = row.get("expected_git_blob_sha")
            if not isinstance(path, str) or not isinstance(pin, str):
                raise FixtureError("HISTORICAL_INPUT_UNPINNED")
            raw = historical(CANONICAL_COMMIT, path)
            if git_blob_sha(raw) != pin:
                raise FixtureError("HISTORICAL_PIN_MISMATCH")
            raw_files.append((path, raw, CANONICAL_COMMIT))
        for path in QA_INPUTS:
            raw_files.append((path, historical(QA_COMMIT, path), QA_COMMIT))
    for path in SNAPSHOTS:
        raw_files.append((path, read_current(repo, "documentation/" + path), "CURRENT_CHECKOUT"))
    for path in TOOLS:
        raw_files.append((path, read_current(repo, path), "CURRENT_CHECKOUT"))
    if len({row[0] for row in raw_files}) != len(raw_files):
        raise FixtureError("DUPLICATE_FIXTURE_PATH")
    if sum(len(row[1]) for row in raw_files) > MAX_TOTAL:
        raise FixtureError("TOTAL_FIXTURE_SIZE_LIMIT")
    output.mkdir(mode=0o700)
    records = []
    for relative, raw, source in raw_files:
        target = safe_file(output, relative)
        target.parent.mkdir(mode=0o700, parents=True, exist_ok=True)
        with target.open("xb") as handle:
            handle.write(raw)
        target.chmod(0o600)
        records.append({"path": relative, "source": source, "bytes": len(raw),
                        "git_blob": git_blob_sha(raw),
                        "sha256": hashlib.sha256(raw).hexdigest()})
    report = {
        "schema": "wcp360.scm01-historical-inputs/1",
        "scope": "PRIVATE_HISTORICAL_FIXTURES_ONLY",
        "canonical_source_commit": CANONICAL_COMMIT,
        "qa_proposal_source_commit": QA_COMMIT,
        "canonical_input_count": len(artifacts),
        "historical_qa_input_count": len(QA_INPUTS),
        "owner_approval_verified": False, "e3_independent_review_verified": False,
        "current_core_compatibility_verified": False,
        "implementation_authorization": "NONE", "records": records,
    }
    with (output / "SOURCE-PROVENANCE.json").open("x", encoding="utf-8") as handle:
        handle.write(json.dumps(report, indent=2, sort_keys=True) + "\n")
    return report


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--repo", type=Path, default=Path(__file__).resolve().parents[2])
    mode = parser.add_mutually_exclusive_group(required=True)
    mode.add_argument("--output", type=Path)
    mode.add_argument("--verify-current", action="store_true")
    args = parser.parse_args(argv)
    try:
        report = current_report(args.repo.resolve()) if args.verify_current else export(args.repo, args.output)
        print(json.dumps(report, indent=2, sort_keys=True))
        return 0
    except (FixtureError, OSError, ValueError, UnicodeError, subprocess.SubprocessError):
        print('{"status":"ERROR","error_code":"SCM01_FIXTURE_CHECK_FAILED"}', file=sys.stderr)
        return 2


if __name__ == "__main__":
    raise SystemExit(main())
