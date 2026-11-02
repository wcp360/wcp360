"""Synthetic checks for scoped historical exports and current pin observations."""
from __future__ import annotations

import json
from pathlib import Path
import tempfile
import unittest
from unittest.mock import patch

from sc_m01_historical_inputs import (
    CURRENT_IDS, MANIFEST, FixtureError, current_report, export, safe_file,
)
from sc_m01_preflight import ALLOWED_PREFIXES, REQUIRED_IDS, git_blob_sha


class HistoricalInputTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory(prefix="wcp-historical-input-tests-")
        self.addCleanup(self.tmp.cleanup)
        self.root = Path(self.tmp.name)
        self.repo = self.root / "checkout"
        self.repo.mkdir()
        self.entries = []
        for name in sorted(REQUIRED_IDS):
            relative = ALLOWED_PREFIXES[name] + "fixture-" + name.lower() + ".md"
            raw = ("synthetic-" + name).encode("ascii")
            self.entries.append({"id": name, "version": "fixture", "path": relative,
                                 "expected_git_blob_sha": git_blob_sha(raw)})
            if name in CURRENT_IDS:
                target = self.repo / "documentation" / relative
                target.parent.mkdir(parents=True, exist_ok=True)
                target.write_bytes(raw)
        self.manifest = {"schema_version": 1, "artifacts": self.entries}
        self.write_manifest()

    def write_manifest(self):
        target = self.repo / "documentation" / MANIFEST
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_text(json.dumps(self.manifest), encoding="utf-8")

    def test_current_five_pins_do_not_claim_complete_or_core_approval(self):
        report = current_report(self.repo)
        self.assertEqual(report["exact_pins_matching"], 5)
        self.assertFalse(report["fingerprint_preflight_complete"])
        self.assertFalse(report["current_core_compatibility_verified"])
        self.assertFalse(report["e3_independent_review_verified"])
        self.assertEqual(report["implementation_authorization"], "NONE")

    def test_required_current_input_tamper_is_rejected(self):
        entry = next(row for row in self.entries if row["id"] == "API")
        (self.repo / "documentation" / entry["path"]).write_bytes(b"tampered")
        with self.assertRaisesRegex(FixtureError, "CURRENT_REQUIRED_PIN_MISMATCH"):
            current_report(self.repo)

    def test_required_current_input_missing_is_rejected(self):
        entry = next(row for row in self.entries if row["id"] == "DATABASE")
        (self.repo / "documentation" / entry["path"]).unlink()
        with self.assertRaisesRegex(FixtureError, "CURRENT_REQUIRED_PIN_MISMATCH"):
            current_report(self.repo)

    def test_optional_input_present_but_wrong_never_silently_passes(self):
        entry = next(row for row in self.entries if row["id"] == "QA")
        target = self.repo / "documentation" / entry["path"]
        target.parent.mkdir(parents=True)
        target.write_bytes(b"not-the-pin")
        with self.assertRaisesRegex(FixtureError, "CURRENT_INPUT_DRIFT"):
            current_report(self.repo)

    def test_output_inside_checkout_refused_before_any_git_execution(self):
        with patch("sc_m01_historical_inputs.subprocess.run") as run:
            with self.assertRaisesRegex(FixtureError, "OUTPUT_MUST_BE_FRESH_AND_EXTERNAL"):
                export(self.repo, self.repo / "restored")
            run.assert_not_called()

    def test_existing_output_refused_before_any_git_execution(self):
        output = self.root / "existing"
        output.mkdir()
        with patch("sc_m01_historical_inputs.subprocess.run") as run:
            with self.assertRaisesRegex(FixtureError, "OUTPUT_MUST_BE_FRESH_AND_EXTERNAL"):
                export(self.repo, output)
            run.assert_not_called()

    def test_unsafe_and_noncanonical_relative_paths_refused(self):
        for path in ("../outside", "/outside", "one\\two", "a//b", "a/./b"):
            with self.subTest(path=path), self.assertRaises(FixtureError):
                safe_file(self.repo, path)

    def test_nested_symlink_refused(self):
        (self.repo / "alias").symlink_to(self.root, target_is_directory=True)
        with self.assertRaisesRegex(FixtureError, "SYMLINKED_FIXTURE_PATH"):
            safe_file(self.repo, "alias/outside")


if __name__ == "__main__":
    unittest.main()
