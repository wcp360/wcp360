#!/usr/bin/env python3
"""Regression checks for root ignore rules; no application/SQL files are created.

Requires Python 3.9+ and Git. Only a temporary Git repository is modified.
This is a filename-policy check, NOT a content/secret scan or product QA suite.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import os
from pathlib import Path
import platform
import shutil
import subprocess
import sys
import tempfile

# Synthetic path probes, not real secrets, SQL, migrations or product files.
TRACKABLE = (
    "database/migrations/001_initial.sql",
    "database/migrations/tenant/002_scope.sql",
    "tests/fixtures/minimal.sql",
    "tests/fixtures/tenant/minimal.sql",
    "src/runtime/config.go",
    "src/wcpd/runtime/config.go",
    "packaging/build/README.md",
    "tools/build/check.py",
    "documentation/modules/secrets.md",
    "specifications/security/secrets.md",
    ".env.example",
    "src/backend/.env.example",
    "README.md",
    "governance/audits/AUDIT-WCP-0100/INDEX.md",
)
IGNORED = (
    ".env", ".env.production", "src/backend/.env",
    "src/backend/.env.production", "private/server.pem",
    "config/server.key", "certs/client.p12", "certs/client.pfx",
    "config/credentials.json", "config/secrets.json", "secrets.md",
    "documentation/modules/secrets.json", "specifications/security/secrets.key",
    "documentation/modules/private/secrets.md", "secrets/token.txt",
    ".secret/token.txt", "config/local/production.ini", "settings.local",
    "runtime/state.json", "runtime/nested/state.json", "build/output.bin",
    "build/nested/output.bin", "logs/server.log", "tmp/upload.bin",
    "cache/entry.bin", "var/state.bin", "storage/private/object.bin",
    "backups/archive.tar.gz", "dumps/full.sql", "production.sql",
    "database/unreviewed.sql", "tests/fixtures/production.dump.sql",
    "database/migrations/production.backup.sql", "export.sql.gz",
    "node_modules/pkg/index.js", "src/backend/vendor/pkg/file.php",
    "__pycache__/check.pyc", ".DS_Store",
)


def evaluate(ignore_file: Path) -> dict:
    git = shutil.which("git")
    if not git:
        raise RuntimeError("Git is required")
    if ignore_file.is_symlink() or not ignore_file.is_file():
        raise RuntimeError("Ignore file must be a regular non-symlink file")
    rules = ignore_file.read_bytes()
    if len(rules) > 1024 * 1024:
        raise RuntimeError("Ignore file exceeds the 1 MiB checker limit")
    rules.decode("utf-8")
    probes = [(p, False) for p in TRACKABLE] + [(p, True) for p in IGNORED]
    if len({p for p, _ in probes}) != len(probes):
        raise RuntimeError("Duplicate path probe")
    with tempfile.TemporaryDirectory(prefix="wcp360-ignore-") as folder:
        work = Path(folder)
        # Do not inherit Git config, index, worktree, hooks or caller secrets.
        env = {
            "PATH": os.environ.get("PATH", ""), "HOME": str(work),
            "XDG_CONFIG_HOME": str(work / "xdg"), "LC_ALL": "C",
            "GIT_CONFIG_NOSYSTEM": "1", "GIT_CONFIG_GLOBAL": os.devnull,
            "GIT_CONFIG_SYSTEM": os.devnull, "GIT_TERMINAL_PROMPT": "0",
        }
        command = [git, "-c", f"core.excludesFile={os.devnull}",
                   "-c", f"core.hooksPath={os.devnull}"]
        subprocess.run(command + ["init", "--quiet", "."], cwd=work,
                       env=env, capture_output=True, check=True, timeout=10)
        (work / ".gitignore").write_bytes(rules)
        result = subprocess.run(
            command + ["check-ignore", "--no-index", "--verbose",
                       "--non-matching", "-z", "--stdin"],
            input=b"".join(p.encode("utf-8") + b"\0" for p, _ in probes),
            cwd=work, env=env, capture_output=True, timeout=10,
        )
        if result.returncode not in (0, 1):
            raise RuntimeError("git check-ignore failed")
        parts = result.stdout.split(b"\0")
        if not parts or parts[-1] != b"":
            raise RuntimeError("Malformed Git response")
        parts.pop()
        if len(parts) != len(probes) * 4:
            raise RuntimeError("Incomplete Git response")
        cases = []
        for i, (path, expected) in enumerate(probes):
            source, line, pattern, returned_path = (
                item.decode("utf-8") for item in parts[i * 4:i * 4 + 4]
            )
            if returned_path != path:
                raise RuntimeError("Git probe order mismatch")
            # Verbose Git output includes negated matches: these are NOT ignored.
            actual = bool(pattern) and not pattern.startswith("!")
            cases.append({"path": path, "expected_ignored": expected,
                          "actual_ignored": actual, "passed": actual == expected,
                          "pattern": pattern, "source": source, "line": line})
        version = subprocess.run([git, "--version"], cwd=work, env=env,
                                 capture_output=True, text=True, check=True,
                                 timeout=10).stdout.strip()
    passed = sum(case["passed"] for case in cases)
    return {"scope": "root gitignore synthetic path regression only",
            "gitignore_sha256": hashlib.sha256(rules).hexdigest(),
            "gitignore_git_blob_sha1": hashlib.sha1(
                b"blob " + str(len(rules)).encode() + b"\0" + rules).hexdigest(),
            "git_version": version, "python_version": platform.python_version(),
            "total": len(cases), "passed": passed, "failed": len(cases) - passed,
            "application_tests_executed": 0, "secret_content_scan": False,
            "cases": cases}


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--ignore-file", type=Path,
                        default=Path(__file__).resolve().parents[2] / ".gitignore")
    args = parser.parse_args()
    try:
        report = evaluate(args.ignore_file)
    except (OSError, UnicodeError, RuntimeError, subprocess.SubprocessError) as exc:
        print(json.dumps({"status": "ERROR", "reason": str(exc)}, ensure_ascii=True))
        return 2
    print(json.dumps(report, indent=2, ensure_ascii=True))
    return 0 if report["failed"] == 0 else 1


if __name__ == "__main__":
    sys.exit(main())
