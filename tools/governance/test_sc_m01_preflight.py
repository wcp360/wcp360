"""Deterministic no-network tests of SC-M01 *worktree-only* artifact preflight."""
from __future__ import annotations

import contextlib
import io
import json
import tempfile
import unittest
from pathlib import Path

from sc_m01_preflight import (
    ALLOWED_PREFIXES, DEFAULT_MANIFEST, REQUIRED_IDS, check_manifest,
    git_blob_sha, inspect, main,
)


class PreflightTests(unittest.TestCase):
    def setUp(self) -> None:
        self.tmp = tempfile.TemporaryDirectory()
        self.repo = Path(self.tmp.name)
        self.entries = []
        for name in sorted(REQUIRED_IDS):
            path = f"{ALLOWED_PREFIXES[name]}fixture-{name.lower()}.md"
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
        self.entries[0]["path"] = (
            ALLOWED_PREFIXES[self.entries[0]["id"]] + "../../outside.md"
        )
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

    def test_wrong_owner_directory_is_rejected(self) -> None:
        self.entries[0]["path"] = "governance/proposals/not-canonical.md"
        with self.assertRaisesRegex(ValueError, "outside canonical"):
            check_manifest(self.manifest)

    def test_non_string_artifact_id_is_rejected(self) -> None:
        self.entries[0]["id"] = {"unexpected": "object"}
        with self.assertRaisesRegex(ValueError, "artifact id"):
            check_manifest(self.manifest)

    def test_nested_symlink_is_rejected(self) -> None:
        entry = self.entries[0]
        original = self.repo / entry["path"]
        alias = original.parent / "alias"
        alias.symlink_to(original.parent, target_is_directory=True)
        entry["path"] = (
            ALLOWED_PREFIXES[entry["id"]] + "alias/" + original.name
        )
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


    def test_documentation_layout_checks_same_bytes_without_rewriting_manifest(self) -> None:
        original = json.dumps(self.manifest, sort_keys=True)
        for entry in self.entries:
            old = self.repo / entry["path"]
            relocated = self.repo / "documentation" / entry["path"]
            relocated.parent.mkdir(parents=True, exist_ok=True)
            old.rename(relocated)
        report = inspect(self.repo, self.manifest, input_layout="documentation")
        self.assertEqual(report["exact_pins_matching"], 8)
        self.assertEqual(report["input_layout"], "documentation")
        self.assertEqual(json.dumps(self.manifest, sort_keys=True), original)
        self.assertEqual(inspect(self.repo, self.manifest)["exact_pins_matching"], 0)

    def test_unknown_input_layout_is_refused(self) -> None:
        with self.assertRaisesRegex(ValueError, "unsupported input layout"):
            inspect(self.repo, self.manifest, input_layout="../outside")

class CurrentSnapshotTests(unittest.TestCase):
    """Exercise published canonical bytes and strict CLI failure semantics."""

    def setUp(self) -> None:
        self.source_repo = Path(__file__).resolve().parents[2]
        self.data = json.loads(
            (self.source_repo / DEFAULT_MANIFEST).read_text(encoding="utf-8")
        )
        self.tmp = tempfile.TemporaryDirectory()
        self.repo = Path(self.tmp.name)

    def tearDown(self) -> None:
        self.tmp.cleanup()

    def copy_sources(self) -> None:
        for entry in check_manifest(self.data):
            source = (self.source_repo / entry["path"]).resolve()
            target = (self.repo / entry["path"]).resolve()
            self.assertTrue(source.is_relative_to(self.source_repo))
            self.assertTrue(target.is_relative_to(self.repo))
            target.parent.mkdir(parents=True, exist_ok=True)
            target.write_bytes(source.read_bytes())
        manifest = self.repo / DEFAULT_MANIFEST
        manifest.parent.mkdir(parents=True, exist_ok=True)
        manifest.write_text(json.dumps(self.data), encoding="utf-8")

    def strict_report(self, repo: Path) -> tuple[int, dict]:
        output = io.StringIO()
        with contextlib.redirect_stdout(output):
            code = main([
                "--repo", str(repo), "--format", "json", "--require-all-pinned",
            ])
        return code, json.loads(output.getvalue())

    def assert_no_authority_claim(self, report: dict) -> None:
        for field in (
            "owner_approval_verified", "e3_independent_review_verified",
            "source_code_gate_released",
        ):
            self.assertFalse(report[field], field)
        self.assertEqual(report["implementation_authorization"], "NONE")

    def test_default_current_snapshot_matches_eight_without_approval(self) -> None:
        code, report = self.strict_report(self.source_repo)
        self.assertEqual(code, 0)
        self.assertEqual(report["exact_pins_matching"], 8)
        self.assertEqual(report["physically_present_in_worktree"], 8)
        self.assertTrue(report["fingerprint_preflight_complete"])
        self.assertEqual(
            report["manifest_source_main_sha"],
            "580e9ed58286304ade30142caf9415f4b87c0c28",
        )
        self.assert_no_authority_claim(report)

    def test_modified_final_database_fails_strict_cli(self) -> None:
        self.copy_sources()
        entry = next(e for e in self.data["artifacts"] if e["id"] == "DATABASE")
        target = self.repo / entry["path"]
        target.write_bytes(target.read_bytes() + b"\n")
        code, report = self.strict_report(self.repo)
        self.assertEqual(code, 2)
        self.assertEqual(report["exact_pins_matching"], 7)
        database = next(r for r in report["artifacts"] if r["id"] == "DATABASE")
        self.assertEqual(database["status"], "HASH_MISMATCH")
        self.assert_no_authority_claim(report)

    def test_missing_final_qa_fails_strict_cli(self) -> None:
        self.copy_sources()
        entry = next(e for e in self.data["artifacts"] if e["id"] == "QA")
        (self.repo / entry["path"]).unlink()
        code, report = self.strict_report(self.repo)
        self.assertEqual(code, 2)
        self.assertEqual(report["exact_pins_matching"], 7)
        qa = next(r for r in report["artifacts"] if r["id"] == "QA")
        self.assertEqual(qa["status"], "NOT_PRESENT")
        self.assert_no_authority_claim(report)

    def test_historical_snapshot_still_has_only_five_configured_pins(self) -> None:
        old = self.source_repo / (
            "governance/verification/"
            "SC-M01-CANONICAL-INPUT-FINGERPRINTS-2026-10-26.json"
        )
        report = inspect(
            self.source_repo, json.loads(old.read_text(encoding="utf-8"))
        )
        self.assertEqual(report["exact_pins_matching"], 5)
        self.assertFalse(report["fingerprint_preflight_complete"])
        self.assert_no_authority_claim(report)


if __name__ == "__main__":
    unittest.main()
