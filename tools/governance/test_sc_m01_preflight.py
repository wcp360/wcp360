"""Deterministic no-network tests of SC-M01 *worktree-only* artifact preflight."""
from __future__ import annotations

import json
import tempfile
import unittest
from pathlib import Path

from sc_m01_preflight import (
    REQUIRED_IDS, check_manifest, git_blob_sha, inspect, main,
)


class PreflightTests(unittest.TestCase):
    def setUp(self) -> None:
        self.tmp = tempfile.TemporaryDirectory()
        self.repo = Path(self.tmp.name)
        self.entries = []
        for name in sorted(REQUIRED_IDS):
            path = f"specifications/{name.lower()}.md"
            target = self.repo / path
            target.parent.mkdir(parents=True, exist_ok=True)
            content = f"sample {name}\n".encode("utf-8")
            target.write_bytes(content)
            self.entries.append({
                "id": name,
                "version": "test fixture",
                "path": path,
                "expected_git_blob_sha": git_blob_sha(content),
            })
        self.manifest = {"schema_version": 1, "artifacts": self.entries}

    def tearDown(self) -> None:
        self.tmp.cleanup()

    def test_exact_eight_pins_do_not_claim_approval(self) -> None:
        report = inspect(self.repo, self.manifest)
        self.assertEqual(report["exact_pins_matching"], 8)
        self.assertTrue(report["fingerprint_preflight_complete"])
        self.assertFalse(report["owner_approval_verified"])
        self.assertFalse(report["e3_independent_review_verified"])
        self.assertFalse(report["source_code_gate_released"])

    def test_five_pins_and_three_missing_do_not_release_gate(self) -> None:
        for entry in self.entries[5:]:
            (self.repo / entry["path"]).unlink()
        result = inspect(self.repo, self.manifest)
        self.assertEqual(result["exact_pins_matching"], 5)
        self.assertEqual(result["physically_present_in_worktree"], 5)
        self.assertFalse(result["fingerprint_preflight_complete"])
        self.assertEqual(
            sum(row["status"] == "NOT_PRESENT" for row in result["artifacts"]), 3
        )

    def test_tamper_is_hash_mismatch(self) -> None:
        target = self.repo / self.entries[0]["path"]
        target.write_text("tampered", encoding="utf-8")
        report = inspect(self.repo, self.manifest)
        self.assertEqual(report["exact_pins_matching"], 7)
        self.assertEqual(report["artifacts"][0]["status"], "HASH_MISMATCH")

    def test_missing_owner_pin_never_counts_as_ready(self) -> None:
        self.entries[0]["expected_git_blob_sha"] = None
        report = inspect(self.repo, self.manifest)
        self.assertEqual(report["artifacts"][0]["status"], "NOT_PINNED")
        self.assertEqual(report["exact_pins_matching"], 7)

    def test_undefined_owner_path_is_explicit(self) -> None:
        self.entries[0]["path"] = None
        self.entries[0]["expected_git_blob_sha"] = None
        report = inspect(self.repo, self.manifest)
        self.assertEqual(report["artifacts"][0]["status"], "PATH_NOT_DEFINED")

    def test_parent_traversal_is_rejected(self) -> None:
        self.entries[0]["path"] = "../outside.md"
        with self.assertRaisesRegex(ValueError, "unsafe repository path"):
            inspect(self.repo, self.manifest)

    def test_duplicated_or_missing_artifact_id_rejected(self) -> None:
        self.entries[0]["id"] = self.entries[1]["id"]
        with self.assertRaisesRegex(ValueError, "artifact set"):
            check_manifest(self.manifest)

    def test_invalid_sha_rejected(self) -> None:
        self.entries[0]["expected_git_blob_sha"] = "123"
        with self.assertRaisesRegex(ValueError, "40-character"):
            check_manifest(self.manifest)

    def test_symlink_to_external_path_is_rejected(self) -> None:
        target = self.repo / self.entries[0]["path"]
        target.unlink()
        target.symlink_to(self.repo.parent)
        with self.assertRaisesRegex(ValueError, "symlink"):
            inspect(self.repo, self.manifest)

    def test_require_all_pinned_is_fail_closed(self) -> None:
        self.entries[0]["expected_git_blob_sha"] = None
        (self.repo / "manifest.json").write_text(
            json.dumps(self.manifest), encoding="utf-8"
        )
        self.assertEqual(
            main([
                "--repo", str(self.repo),
                "--manifest", "manifest.json",
                "--format", "json",
                "--require-all-pinned",
            ]), 2
        )


if __name__ == "__main__":
    unittest.main()
