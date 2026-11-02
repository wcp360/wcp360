#!/usr/bin/env python3
"""Regression tests for the publication inventory checker.

All tests use synthetic disposable Git repositories. No WCP360 application code,
credentials, network access or production data are involved.
"""
from __future__ import annotations

from contextlib import redirect_stderr, redirect_stdout
import hashlib
import importlib.util
import io
import json
import os
from pathlib import Path
import shutil
import subprocess
import tempfile
import unittest
from unittest.mock import patch

HERE = Path(__file__).resolve().parent
SPEC = importlib.util.spec_from_file_location(
    "publication_checker", HERE / "check_publication.py"
)
if SPEC is None or SPEC.loader is None:
    raise RuntimeError("Cannot load publication checker")
checker = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(checker)


class Fixture(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory(prefix="wcp-publication-tests-")
        self.addCleanup(self.tmp.cleanup)
        self.repo = Path(self.tmp.name)
        self.env = {
            "PATH": os.environ.get("PATH", ""),
            "HOME": str(self.repo),
            "GIT_CONFIG_NOSYSTEM": "1",
            "GIT_CONFIG_GLOBAL": os.devnull,
            "GIT_CONFIG_SYSTEM": os.devnull,
            "GIT_TERMINAL_PROMPT": "0",
            "LC_ALL": "C",
        }
        self.git("init", "-q")
        self.git("config", "user.email", "test@example.invalid")
        self.git("config", "user.name", "Synthetic validation")
        self.data = b"# Synthetic\n"
        self.oid = hashlib.sha1(
            b"blob " + str(len(self.data)).encode() + b"\0" + self.data
        ).hexdigest()
        mirror = self.repo / checker.MIRROR
        mirror.mkdir(parents=True)
        (mirror / "sample.md").write_bytes(self.data)
        self.write_manifest(1)
        for name in (
            "README.md",
            "INDEX.md",
            "COMPLEMENT-2026-10-02.md",
            "gitignore-reproduction.json",
        ):
            out = self.repo / "governance/audits/AUDIT-WCP-0100" / name
            out.parent.mkdir(parents=True, exist_ok=True)
            out.write_text("synthetic test", encoding="utf-8")
        self.commit()

    def git(self, *args, check=True, input_data=None, extra_env=None):
        env = dict(self.env, **(extra_env or {}))
        return subprocess.run(
            ["git", "-C", str(self.repo), "-c", "core.hooksPath=" + os.devnull, *args],
            capture_output=True, check=check, input=input_data, timeout=10, env=env
        )

    def commit(self):
        self.git("add", "-A")
        self.git("commit", "-qm", "Synthetic fixture only")

    def write_manifest(self, declared_count):
        mirror = self.repo / checker.MIRROR
        row = f"| 1 | `sample.md` | `{self.oid}` | {len(self.data)} |\n"
        (mirror / "MANIFEST.md").write_text(
            f"SOURCE MATERIALIZED MARKDOWN FILES = {declared_count}\n"
            f"TOTAL SOURCE BYTES = {len(self.data)}\n" + row,
            encoding="utf-8",
        )

    def run_main(self, *args):
        stdout, stderr = io.StringIO(), io.StringIO()
        with redirect_stdout(stdout), redirect_stderr(stderr):
            code = checker.main(["--repo", str(self.repo), *args])
        return code, stdout.getvalue(), stderr.getvalue()


class PublicationTests(Fixture):
    def test_valid_inventory(self):
        report = checker.inspect(self.repo, "HEAD")
        self.assertEqual(report["mirror"]["exact_matches"], 1)
        self.assertEqual(report["file_count"], 6)
        self.assertEqual(report["audit"]["present"], 4)

    def test_changed_blob_detected(self):
        (self.repo / checker.MIRROR / "sample.md").write_text("changed", encoding="utf-8")
        self.commit()
        self.assertEqual(len(checker.inspect(self.repo, "HEAD")["mirror"]["changed"]), 1)

    def test_missing_file_detected(self):
        (self.repo / checker.MIRROR / "sample.md").unlink()
        self.commit()
        self.assertEqual(
            checker.inspect(self.repo, "HEAD")["mirror"]["missing"],
            [checker.MIRROR + "sample.md"],
        )

    def test_unlisted_file_detected(self):
        (self.repo / checker.MIRROR / "extra.md").write_text("extra", encoding="utf-8")
        self.commit()
        self.assertEqual(
            checker.inspect(self.repo, "HEAD")["mirror"]["unlisted"],
            [checker.MIRROR + "extra.md"],
        )

    def test_unsafe_ref_rejected(self):
        with self.assertRaises(checker.InventoryError) as ctx:
            checker.inspect(self.repo, "--help")
        self.assertEqual(str(ctx.exception), "INVALID_REF")

    def test_invalid_manifest_count_rejected(self):
        self.write_manifest(2)
        self.commit()
        with self.assertRaises(checker.InventoryError) as ctx:
            checker.inspect(self.repo, "HEAD")
        self.assertEqual(str(ctx.exception), "INVALID_MANIFEST_COUNT")

    def test_subdirectory_inventory_matches_root(self):
        sub = self.repo / "nested/deeper"
        sub.mkdir(parents=True)
        root = checker.inspect(self.repo, "HEAD")
        nested = checker.inspect(sub, "HEAD")
        self.assertEqual(nested, root)

    def test_inherited_git_environment_cannot_redirect_repository(self):
        other = self.repo / "other"
        other.mkdir()
        subprocess.run(["git", "init", "-q", str(other)], check=True, capture_output=True)
        poisoned = {
            "GIT_DIR": str(other / ".git"),
            "GIT_WORK_TREE": str(other),
            "GIT_OBJECT_DIRECTORY": str(other / ".git/objects"),
            "GIT_CONFIG_COUNT": "1",
            "GIT_CONFIG_KEY_0": "alias.cat-file",
            "GIT_CONFIG_VALUE_0": "!false",
        }
        with patch.dict(os.environ, poisoned):
            report = checker.inspect(self.repo, "HEAD")
        self.assertEqual(report["mirror"]["exact_matches"], 1)
        self.assertEqual(report["commit"], self.git("rev-parse", "HEAD").stdout.decode().strip())

    def test_gitlink_is_rejected_not_silently_omitted(self):
        head = self.git("rev-parse", "HEAD").stdout.decode().strip()
        self.git("update-index", "--add", "--cacheinfo", "160000," + head + ",external")
        self.git("commit", "-qm", "synthetic gitlink")
        with self.assertRaises(checker.InventoryError) as ctx:
            checker.inspect(self.repo, "HEAD")
        self.assertEqual(str(ctx.exception), "UNSUPPORTED_TREE_ENTRY")

    def test_existing_report_is_not_overwritten(self):
        output = self.repo / "report.json"
        output.write_text("original", encoding="utf-8")
        code, _, _ = self.run_main("--output", str(output))
        self.assertEqual(code, 2)
        self.assertEqual(output.read_text(encoding="utf-8"), "original")

    def test_output_symlink_is_not_followed(self):
        target = self.repo / "keep.txt"
        target.write_text("original", encoding="utf-8")
        link = self.repo / "report.json"
        link.symlink_to(target)
        code, _, _ = self.run_main("--output", str(link))
        self.assertEqual(code, 2)
        self.assertEqual(target.read_text(encoding="utf-8"), "original")


if __name__ == "__main__":
    unittest.main(verbosity=2)
