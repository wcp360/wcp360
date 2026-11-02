#!/usr/bin/env python3
"""Validate root ignore rules and reject tracked files that those rules ignore.

Synthetic probes prove intended policy behavior. The tracked-file check then applies
Git's actual ignore engine to the committed/staged file set, using --no-index so a
forced git add cannot hide an ignore-policy violation.

No source file bodies are read. Real-path findings are redacted to ordinal + SHA-256.
This is repository filename policy, not a content/secret scan or product QA suite.
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

MAX_TRACKED_FILES = 10000
MAX_LISTING_BYTES = 8 * 1024 * 1024

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


def clean_env(home: Path) -> dict[str, str]:
    return {
        "PATH": os.environ.get("PATH", ""), "HOME": str(home),
        "XDG_CONFIG_HOME": str(home / "xdg"), "LC_ALL": "C",
        "GIT_CONFIG_NOSYSTEM": "1", "GIT_CONFIG_GLOBAL": os.devnull,
        "GIT_CONFIG_SYSTEM": os.devnull, "GIT_TERMINAL_PROMPT": "0",
        "GIT_OPTIONAL_LOCKS": "0",
    }


def command(git: str) -> list[str]:
    return [git, "--no-pager", "--no-replace-objects",
            "-c", f"core.excludesFile={os.devnull}",
            "-c", f"core.hooksPath={os.devnull}",
            "-c", "core.fsmonitor=false"]


def parse_check_ignore(output: bytes, expected_paths: list[bytes]) -> list[tuple[bytes, bytes, bytes, bytes]]:
    parts = output.split(b"\0")
    if not parts or parts[-1] != b"":
        raise RuntimeError("Malformed Git response")
    parts.pop()
    if len(parts) != len(expected_paths) * 4:
        raise RuntimeError("Incomplete Git response")
    rows = []
    for i, expected in enumerate(expected_paths):
        source, line, pattern, returned_path = parts[i * 4:i * 4 + 4]
        if returned_path != expected:
            raise RuntimeError("Git probe order mismatch")
        rows.append((source, line, pattern, returned_path))
    return rows


def evaluate_synthetic(ignore_file: Path, git: str) -> dict:
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
        env = clean_env(work)
        cmd = command(git)
        subprocess.run(cmd + ["init", "--quiet", "."], cwd=work,
                       env=env, capture_output=True, check=True, timeout=10)
        (work / ".gitignore").write_bytes(rules)
        encoded = [p.encode("utf-8") for p, _ in probes]
        result = subprocess.run(
            cmd + ["check-ignore", "--no-index", "--verbose",
                   "--non-matching", "-z", "--stdin"],
            input=b"".join(p + b"\0" for p in encoded),
            cwd=work, env=env, capture_output=True, timeout=10,
        )
        if result.returncode not in (0, 1):
            raise RuntimeError("git check-ignore failed")
        rows = parse_check_ignore(result.stdout, encoded)
        cases = []
        for (path, expected), (source, line, pattern, _) in zip(probes, rows):
            # Verbose Git output includes negated matches: these are NOT ignored.
            actual = bool(pattern) and not pattern.startswith(b"!")
            cases.append({
                "path": path,
                "expected_ignored": expected,
                "actual_ignored": actual,
                "passed": actual == expected,
                "pattern": pattern.decode("utf-8"),
                "source": source.decode("utf-8"),
                "line": line.decode("ascii"),
            })
        version = subprocess.run([git, "--version"], cwd=work, env=env,
                                 capture_output=True, text=True, check=True,
                                 timeout=10).stdout.strip()
    passed = sum(case["passed"] for case in cases)
    return {
        "scope": "root gitignore synthetic path regression",
        "gitignore_sha256": hashlib.sha256(rules).hexdigest(),
        "gitignore_git_blob_sha1": hashlib.sha1(
            b"blob " + str(len(rules)).encode() + b"\0" + rules).hexdigest(),
        "git_version": version,
        "python_version": platform.python_version(),
        "total": len(cases),
        "passed": passed,
        "failed": len(cases) - passed,
        "cases": cases,
    }


def tracked_policy(repo: Path, git: str) -> dict:
    if not repo.is_dir():
        raise RuntimeError("Repository directory is unavailable")
    with tempfile.TemporaryDirectory(prefix="wcp360-ignore-home-") as folder:
        env = clean_env(Path(folder))
        cmd = command(git)
        root = subprocess.run(
            cmd + ["-C", str(repo), "rev-parse", "--show-toplevel"],
            env=env, capture_output=True, check=True, timeout=10,
        ).stdout.rstrip(b"\n")
        if not root:
            raise RuntimeError("Repository root is unavailable")
        root_path = Path(os.fsdecode(root)).resolve()
        listing = subprocess.run(
            cmd + ["-C", str(root_path), "ls-files", "-z", "--"],
            env=env, capture_output=True, check=True, timeout=10,
        ).stdout
        if len(listing) > MAX_LISTING_BYTES:
            raise RuntimeError("Tracked path listing exceeds checker limit")
        paths = listing.split(b"\0")
        if not paths or paths[-1] != b"":
            raise RuntimeError("Malformed tracked path listing")
        paths.pop()
        if len(paths) > MAX_TRACKED_FILES:
            raise RuntimeError("Tracked file count exceeds checker limit")
        if len(set(paths)) != len(paths):
            raise RuntimeError("Duplicate tracked path")
        if not paths:
            return {"tracked_files": 0, "ignored_tracked_count": 0,
                    "ignored_tracked": [], "index_stable_at_recheck": True}
        checked = subprocess.run(
            cmd + ["-C", str(root_path), "check-ignore", "--no-index",
                   "--verbose", "--non-matching", "-z", "--stdin"],
            input=b"".join(p + b"\0" for p in paths),
            env=env, capture_output=True, timeout=20,
        )
        if checked.returncode not in (0, 1):
            raise RuntimeError("git check-ignore failed for tracked paths")
        rows = parse_check_ignore(checked.stdout, paths)
        violations = []
        for i, (_, line, pattern, path) in enumerate(rows, 1):
            ignored = bool(pattern) and not pattern.startswith(b"!")
            if ignored:
                violations.append({
                    "path_index": i,
                    "path_sha256": hashlib.sha256(path).hexdigest(),
                    "rule_line": line.decode("ascii"),
                    "rule_sha256": hashlib.sha256(pattern).hexdigest(),
                })
        # A local index mutation during the check must not yield a reassuring result.
        listing_after = subprocess.run(
            cmd + ["-C", str(root_path), "ls-files", "-z", "--"],
            env=env, capture_output=True, check=True, timeout=10,
        ).stdout
        if listing_after != listing:
            raise RuntimeError("Tracked index changed during ignore-policy check")
    return {
        "tracked_files": len(paths),
        "ignored_tracked_count": len(violations),
        "ignored_tracked": violations,
        "index_stable_at_recheck": True,
        "raw_paths_emitted": False,
    }


def self_test_tracked_policy(git: str) -> dict:
    cases = []
    with tempfile.TemporaryDirectory(prefix="wcp360-ignore-policy-test-") as folder:
        repo = Path(folder)
        env = clean_env(repo)
        cmd = command(git)
        subprocess.run(cmd + ["init", "--quiet", "."], cwd=repo, env=env,
                       capture_output=True, check=True, timeout=10)
        subprocess.run(cmd + ["config", "user.email", "test@example.invalid"],
                       cwd=repo, env=env, capture_output=True, check=True, timeout=10)
        subprocess.run(cmd + ["config", "user.name", "Synthetic validation"],
                       cwd=repo, env=env, capture_output=True, check=True, timeout=10)
        (repo / ".gitignore").write_text(".env\n*.sql\n!database/migrations/**/*.sql\n",
                                         encoding="utf-8")
        (repo / "safe.txt").write_text("safe\n", encoding="utf-8")
        mig = repo / "database/migrations/001.sql"
        mig.parent.mkdir(parents=True)
        mig.write_text("-- synthetic\n", encoding="utf-8")
        subprocess.run(cmd + ["add", ".gitignore", "safe.txt", "database/migrations/001.sql"],
                       cwd=repo, env=env, capture_output=True, check=True, timeout=10)
        clean = tracked_policy(repo, git)
        if clean["ignored_tracked_count"] != 0:
            raise AssertionError("Clean tracked set incorrectly rejected")
        cases.append("clean_tracked_set")

        secret = repo / ".env"
        secret.write_text("SYNTHETIC=1\n", encoding="utf-8")
        subprocess.run(cmd + ["add", "-f", ".env"], cwd=repo, env=env,
                       capture_output=True, check=True, timeout=10)
        bad = tracked_policy(repo, git)
        if bad["ignored_tracked_count"] != 1 or bad["raw_paths_emitted"]:
            raise AssertionError("Forced ignored path was not detected safely")
        serialized = json.dumps(bad)
        if ".env" in serialized:
            raise AssertionError("Real ignored path leaked into report")
        cases.append("forced_ignored_tracked_path_detected_and_redacted")
    return {"passed": len(cases), "cases": cases}


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--ignore-file", type=Path,
                        default=Path(__file__).resolve().parents[2] / ".gitignore")
    parser.add_argument("--repo", type=Path,
                        default=Path(__file__).resolve().parents[2])
    parser.add_argument("--self-test-tracked", action="store_true")
    args = parser.parse_args()
    git = shutil.which("git")
    if not git:
        print(json.dumps({"status": "ERROR", "reason": "Git is required"}))
        return 2
    try:
        if args.self_test_tracked:
            print(json.dumps(self_test_tracked_policy(git), indent=2))
            return 0
        synthetic = evaluate_synthetic(args.ignore_file, git)
        tracked = tracked_policy(args.repo.resolve(), git)
        report = {
            "scope": "gitignore synthetic regression + real tracked-path policy",
            "synthetic": synthetic,
            "tracked_policy": tracked,
            "application_tests_executed": 0,
            "secret_content_scan": False,
        }
    except (OSError, UnicodeError, RuntimeError, AssertionError,
            subprocess.SubprocessError) as exc:
        print(json.dumps({"status": "ERROR", "reason": str(exc)}, ensure_ascii=True))
        return 2
    print(json.dumps(report, indent=2, ensure_ascii=True))
    return 0 if synthetic["failed"] == 0 and tracked["ignored_tracked_count"] == 0 else 1


if __name__ == "__main__":
    sys.exit(main())
