#!/usr/bin/env python3
"""Read-only secret-pattern check of a committed snapshot or the staged index.

No network requests, external scanner, source execution, suppressions or excerpts.
Exit 0: no selected rule matched; 1: findings; 2: incomplete/error. Not a complete
secret audit: deleted history, archive interiors and unknown formats are outside
coverage. Raw bytes are inspected even when they are not UTF-8 text.
--staged reads the index, not the working tree; no hook is installed automatically.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import os
from pathlib import Path
import re
import shutil
import subprocess
import tempfile
import time
from typing import Any

MAX_FILES = 5000
MAX_BLOB = 8 * 1024 * 1024
MAX_TOTAL = 64 * 1024 * 1024
MAX_FINDINGS = 1000
MAX_SECONDS = 90
OID = re.compile(r"[0-9a-f]{40}\Z")
# Pattern signatures, not example credentials. No validity checks against a provider.
CONTENT_RULES = (
    ("REPO-SECRET-GITHUB-APP-JWT", re.compile(
        rb"(?<![A-Za-z0-9_])ghs_[A-Za-z0-9]{1,32}_"
        rb"[A-Za-z0-9_-]{10,4096}\.[A-Za-z0-9_-]{10,16384}\."
        rb"[A-Za-z0-9_-]{10,4096}(?![A-Za-z0-9_.-])")),
    ("REPO-SECRET-PRIVATE-KEY", re.compile(
        rb"-----BEGIN (?:RSA |EC |DSA |OPENSSH |ENCRYPTED )?PRIVATE KEY-----")),
    ("REPO-SECRET-GITHUB-TOKEN", re.compile(
        rb"(?<![A-Za-z0-9_])(?:gh[pousr]_[A-Za-z0-9]{30,255}|"
        rb"github_pat_[A-Za-z0-9_]{40,255})(?![A-Za-z0-9_])")),
)
SENSITIVE_SUFFIXES = (b".key", b".p12", b".pfx", b".jks", b".keystore",
                      b".dump", b".backup", b".dump.sql", b".backup.sql",
                      b".sql.gz", b".sql.bz2", b".sql.xz")


class CheckError(Exception):
    """Only fixed, non-sensitive error codes may be emitted."""


def path_is_sensitive(path: bytes) -> bool:
    parts = path.lower().split(b"/")
    name = parts[-1]
    return (any(p in {b".ssh", b".aws", b".secret", b"backups", b"dumps"}
                for p in parts[:-1])
            or (name == b".env" or name.startswith(b".env.")) and name != b".env.example"
            or name in {b"id_rsa", b"id_ecdsa", b"id_ed25519"}
            or name.endswith(SENSITIVE_SUFFIXES))


def blob_findings(path: bytes, data: bytes, index: int) -> list[dict[str, Any]]:
    """No raw path, matched value, content digest or source line is returned."""
    location = {"path_index": index, "path_sha256": hashlib.sha256(path).hexdigest()}
    found: list[dict[str, Any]] = []
    if path_is_sensitive(path):
        found.append(dict(location, rule="REPO-SECRET-SENSITIVE-PATH", line=None))
    for rule, pattern in CONTENT_RULES:
        # One finding per rule/file: bounded and sufficient to block the check.
        match = pattern.search(data)
        if match:
            found.append(dict(location, rule=rule, line=data.count(b"\n", 0, match.start()) + 1))
    return found


def inspect(repo: Path, ref: str = "HEAD", *, staged: bool = False,
            index_file: Path | None = None) -> dict[str, Any]:
    if ref != "HEAD" and not OID.fullmatch(ref):
        raise CheckError("INVALID_REF")
    if index_file is not None and not staged:
        raise CheckError("INDEX_REQUIRES_STAGED")
    if staged and ref != "HEAD":
        raise CheckError("CONFLICTING_SNAPSHOT_SELECTION")
    if staged and os.environ.get("GIT_INDEX_FILE") and index_file is None:
        # Never silently scan the default index when Git prepared another one.
        raise CheckError("ALTERNATE_INDEX_NOT_SELECTED")
    if index_file is not None:
        if index_file.is_symlink() or not index_file.is_file():
            raise CheckError("INVALID_INDEX_FILE")
        index_file = index_file.resolve()
    executable = shutil.which("git")
    if not executable:
        raise CheckError("GIT_UNAVAILABLE")
    deadline = time.monotonic() + MAX_SECONDS
    with tempfile.TemporaryDirectory(prefix="wcp-repo-check-") as home:
        env = {"PATH": os.environ.get("PATH", ""), "HOME": home, "LC_ALL": "C",
               "XDG_CONFIG_HOME": home, "GIT_CONFIG_NOSYSTEM": "1",
               "GIT_CONFIG_GLOBAL": os.devnull, "GIT_CONFIG_SYSTEM": os.devnull,
               "GIT_TERMINAL_PROMPT": "0", "GIT_NO_LAZY_FETCH": "1",
               "GIT_OPTIONAL_LOCKS": "0"}
        if index_file is not None:
            env["GIT_INDEX_FILE"] = str(index_file)

        def git(*args: str) -> bytes:
            remaining = deadline - time.monotonic()
            if remaining <= 0:
                raise CheckError("TIME_LIMIT")
            try:
                result = subprocess.run(
                    [executable, "--no-pager", "--no-replace-objects", "-C", str(repo),
                     "-c", "core.hooksPath=" + os.devnull, "-c", "core.fsmonitor=false",
                     "-c", "protocol.allow=never", *args], env=env, capture_output=True,
                    timeout=min(10, remaining), check=False,
                )
            except subprocess.TimeoutExpired:
                raise CheckError("GIT_TIMEOUT") from None
            if result.returncode != 0:
                raise CheckError("GIT_READ_FAILED")
            return result.stdout

        # Top-anchored paths ensure scans from subdirectories cover the whole repo.
        index_command = ("ls-files", "--cached", "--full-name", "-z",
                         "--format=%(objectmode) %(objecttype) %(objectname) "
                         "%(objectsize) %(stage)%x09%(path)", "--", ":/")
        if staged:
            commit = None  # An index is not a commit, including before the first commit.
            listing = git(*index_command)
        else:
            commit = git("rev-parse", "--verify", ref + "^{commit}").decode("ascii").strip()
            if not OID.fullmatch(commit):
                raise CheckError("UNSUPPORTED_OBJECT_FORMAT")
            listing = git("ls-tree", "--full-tree", "-r", "-l", "-z", commit)
        if len(listing) > 4 * 1024 * 1024:
            raise CheckError("TREE_SIZE_LIMIT")
        rows = listing.split(b"\0")
        if rows[-1] != b"":
            raise CheckError("INVALID_TREE")
        entries = []
        for row in rows[:-1]:
            try:
                meta, path = row.split(b"\t", 1)
                if staged:
                    mode, kind, oid, size, stage = meta.split()
                    if stage != b"0":
                        raise CheckError("UNMERGED_INDEX")
                else:
                    mode, kind, oid, size = meta.split()
            except ValueError:
                raise CheckError("INVALID_TREE") from None
            if mode not in (b"100644", b"100755") or kind != b"blob":
                # Do not follow symlinks or silently skip submodules.
                raise CheckError("UNSUPPORTED_TREE_ENTRY")
            if not path or not re.fullmatch(rb"[0-9a-f]{40}", oid) or not size.isdigit():
                raise CheckError("INVALID_TREE")
            entries.append((path, oid.decode("ascii"), int(size)))
        if len({path for path, _, _ in entries}) != len(entries):
            raise CheckError("DUPLICATE_PATH")
        if len(entries) > MAX_FILES:
            raise CheckError("FILE_COUNT_LIMIT")
        if any(size > MAX_BLOB for _, _, size in entries):
            raise CheckError("BLOB_SIZE_LIMIT")
        if sum(size for _, _, size in entries) > MAX_TOTAL:
            raise CheckError("TOTAL_SIZE_LIMIT")
        findings: list[dict[str, Any]] = []
        binary_count = archive_count = 0
        for index, (path, oid, size) in enumerate(entries, 1):
            data = git("cat-file", "blob", oid)
            if len(data) != size:
                raise CheckError("BLOB_SIZE_MISMATCH")
            if hashlib.sha1(b"blob " + str(size).encode() + b"\0" + data).hexdigest() != oid:
                raise CheckError("BLOB_IDENTITY_MISMATCH")
            binary_count += b"\0" in data
            archive_count += path.lower().endswith((b".zip", b".gz", b".xz", b".bz2", b".7z", b".tar"))
            findings.extend(blob_findings(path, data, index))
            if len(findings) > MAX_FINDINGS:
                raise CheckError("FINDING_COUNT_LIMIT")
        if staged and git(*index_command) != listing:
            raise CheckError("INDEX_CHANGED_DURING_SCAN")
    return {"format": "WCP360-REPOSITORY-SECRET-CHECK/2", "commit": commit,
            "snapshot_kind": "index" if staged else "commit",
            "index_snapshot_sha256": hashlib.sha256(listing).hexdigest() if staged else None,
            "index_stable_at_recheck": True if staged else None,
            "status": "FINDINGS" if findings else "NO_KNOWN_PATTERN_DETECTED",
            "scope": ("complete staged index" if staged else "one complete committed snapshot")
                     + "; selected byte signatures and sensitive paths",
            "files_scanned": len(entries), "bytes_scanned": sum(e[2] for e in entries),
            "binary_files_scanned_as_raw_bytes": binary_count,
            "archive_files_not_decompressed": archive_count,
            "history_scanned": False, "complete_secret_audit": False,
            "application_tests_executed": 0,
            "rules": ["REPO-SECRET-SENSITIVE-PATH"] + [r for r, _ in CONTENT_RULES],
            "finding_count": len(findings), "findings": findings}


class SafeParser(argparse.ArgumentParser):
    def error(self, message: str) -> None:
        raise CheckError("INVALID_ARGUMENTS")


def main(argv: list[str] | None = None) -> int:
    parser = SafeParser(description=__doc__)
    parser.add_argument("--repo", type=Path, default=Path.cwd())
    selection = parser.add_mutually_exclusive_group()
    selection.add_argument("--ref", default=None)
    selection.add_argument("--staged", action="store_true")
    parser.add_argument("--index-file", type=Path)
    parser.add_argument("--output", type=Path)
    args = None
    try:
        args = parser.parse_args(argv)
        report = inspect(args.repo.resolve(), args.ref if args.ref is not None else "HEAD",
                         staged=args.staged,
                         index_file=args.index_file)
        code = 1 if report["findings"] else 0
    except CheckError as exc:
        report, code = {"status": "ERROR", "error_code": str(exc), "scan_complete": False}, 2
    except (OSError, UnicodeError, ValueError, subprocess.SubprocessError):
        # Never emit a raw exception: Git stderr or filenames may contain a secret.
        report, code = {"status": "ERROR", "error_code": "CHECK_FAILED", "scan_complete": False}, 2
    payload = json.dumps(report, indent=2, ensure_ascii=True) + "\n"
    if args is not None and args.output:
        try:
            args.output.parent.mkdir(parents=True, exist_ok=True)
            # Exclusive creation: never truncate an existing file or follow its symlink.
            with args.output.open("x", encoding="utf-8") as handle:
                handle.write(payload)
        except OSError:
            print('{"status":"ERROR","error_code":"OUTPUT_WRITE_FAILED"}')
            return 2
    print(payload, end="")
    return code


if __name__ == "__main__":
    raise SystemExit(main())
