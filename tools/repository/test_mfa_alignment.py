#!/usr/bin/env python3
"""Offline tests for review-ledger validation, not application authentication."""
from __future__ import annotations

import copy
import importlib.util
import json
from pathlib import Path
import tempfile
import unittest
from unittest.mock import patch

HERE = Path(__file__).resolve().parent
SPEC = importlib.util.spec_from_file_location("mfa_alignment_checker", HERE / "check_mfa_alignment.py")
assert SPEC is not None and SPEC.loader is not None
subject = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(subject)


def ledger() -> dict:
    return {
        "schema_version": 1, "source_commit": subject.SOURCE_COMMIT, "status": "REVIEW",
        "policy": dict(subject.POLICY), "data": dict(subject.DATA),
        "sources": {key: {"path": value[0], "git_blob": value[1]} for key, value in subject.SOURCE_PINS.items()},
        "sensitive_api_profiles": dict(subject.API_PROFILES),
        "qa": [{"packet_scenario": number,
                "test_files": [subject.TEST_PREFIX + name for name in names],
                "evidence": "SOURCE_MAPPED_NOT_EXECUTED_BY_THIS_CHECK"}
               for number, names in subject.QA_SOURCES.items()],
        "remaining_gates": {key: "OPEN" for key in subject.GAPS},
        "runtime_qualification": "NOT_ESTABLISHED",
    }


def api_row(identifier: str, profile: str) -> str:
    return "| " + " | ".join([f"`{identifier}`", "1.0", "A", "WCP-ADMIN", profile] + ["fixture"] * 14) + " |\n"


def fixture(root: Path) -> dict:
    content = {key: "synthetic non-secret source\n" for key in subject.SOURCE_PINS}
    content["security"] = "| MFA at login | policy/account dependent | mandatory |\n"
    content["api"] = "".join(api_row(key, value) for key, value in subject.API_PROFILES.items())
    content["database"] = "### wcp_iam.user_sessions\n### wcp_iam.admin_sessions\n"
    pins = {}
    for key, (name, _) in subject.SOURCE_PINS.items():
        path = root / name
        path.parent.mkdir(parents=True, exist_ok=True)
        raw = content[key].encode("utf-8")
        path.write_bytes(raw)
        pins[key] = (name, subject.blob_sha(raw))
    for names in subject.QA_SOURCES.values():
        for name in names:
            path = root / subject.TEST_PREFIX / name
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_text("<?php // synthetic source-presence fixture\n")
    return pins


class MfaAlignmentTests(unittest.TestCase):
    def test_valid_review_ledger_is_not_runtime_acceptance(self) -> None:
        subject.validate_matrix(ledger())

    def test_unknown_or_missing_top_level_fields_are_rejected(self) -> None:
        for data in ({**ledger(), "approved": True}, {key: value for key, value in ledger().items() if key != "status"}):
            with self.subTest(keys=list(data)), self.assertRaises(subject.AlignmentError):
                subject.validate_matrix(data)

    def test_non_integer_or_wrong_schema_versions_are_rejected(self) -> None:
        for value in [True, "1", 1.0, 0, 2, None]:
            with self.subTest(value=value), self.assertRaises(subject.AlignmentError):
                subject.validate_matrix({**ledger(), "schema_version": value})

    def test_policy_relaxations_and_obligatory_enrollment_are_rejected(self) -> None:
        for key in subject.POLICY:
            for value in [not subject.POLICY[key], int(subject.POLICY[key]), "false"]:
                data = ledger()
                data["policy"][key] = value
                with self.subTest(key=key, value=value), self.assertRaises(subject.AlignmentError):
                    subject.validate_matrix(data)

    def test_false_acceptance_claims_are_rejected(self) -> None:
        for key, value in [("status", "APPROVED"), ("runtime_qualification", "PASS"), ("source_commit", "0" * 40)]:
            with self.subTest(key=key), self.assertRaises(subject.AlignmentError):
                subject.validate_matrix({**ledger(), key: value})
        data = ledger()
        data["data"]["atomic_upgrade_implemented"] = True
        with self.assertRaises(subject.AlignmentError):
            subject.validate_matrix(data)

    def test_all_open_gates_must_remain_explicit(self) -> None:
        for key in subject.GAPS:
            data = ledger()
            data["remaining_gates"][key] = "CLOSED"
            with self.subTest(gate=key), self.assertRaises(subject.AlignmentError):
                subject.validate_matrix(data)
        data = ledger()
        data["remaining_gates"].pop(next(iter(subject.GAPS)))
        with self.assertRaises(subject.AlignmentError):
            subject.validate_matrix(data)

    def test_source_paths_and_hashes_cannot_be_silently_replaced(self) -> None:
        for key in subject.SOURCE_PINS:
            for field, value in [("path", "../../outside"), ("git_blob", "0" * 40)]:
                data = ledger()
                data["sources"][key][field] = value
                with self.subTest(source=key, field=field), self.assertRaises(subject.AlignmentError):
                    subject.validate_matrix(data)

    def test_each_sensitive_api_retains_its_actual_profile(self) -> None:
        for key in subject.API_PROFILES:
            data = ledger()
            data["sensitive_api_profiles"][key] = "AUTH-A"
            with self.subTest(contract=key), self.assertRaises(subject.AlignmentError):
                subject.validate_matrix(data)
        self.assertEqual("AUTH-P", subject.API_PROFILES["API-WCP-0706"])
        self.assertEqual("AUTH-E", subject.API_PROFILES["API-WCP-0708"])

    def test_qa_mapping_is_not_an_execution_report(self) -> None:
        mutations = [("packet_scenario", True), ("packet_scenario", 8),
                     ("test_files", ["../../file"]), ("evidence", "PASS")]
        for field, value in mutations:
            data = ledger()
            data["qa"][0][field] = value
            with self.subTest(field=field, value=value), self.assertRaises(subject.AlignmentError):
                subject.validate_matrix(data)
        data = ledger()
        data["qa"][1] = copy.deepcopy(data["qa"][0])
        with self.assertRaises(subject.AlignmentError):
            subject.validate_matrix(data)

    def test_duplicate_keys_and_non_json_objects_are_rejected(self) -> None:
        for text in ['{"key":1,"key":2}', '{"key":{"a":1,"a":2}}', '[]', 'null', '{"a":NaN}', '{']:
            with self.subTest(text=text), self.assertRaises(subject.AlignmentError):
                subject.parse_json(text)
        self.assertEqual(ledger(), subject.parse_json(json.dumps(ledger())))

    def test_api_parser_rejects_ambiguous_or_truncated_rows(self) -> None:
        row = api_row("API-WCP-0706", "AUTH-P")
        self.assertEqual({"API-WCP-0706": "AUTH-P"}, subject.read_api_profiles(row))
        for text in [row + row, "| API-WCP-0706 | AUTH-P |"]:
            with self.subTest(text=text), self.assertRaises(subject.AlignmentError):
                subject.read_api_profiles(text)

    def test_full_synthetic_source_check_keeps_qualification_unestablished(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            root = Path(tmp)
            pins = fixture(root)
            with patch.object(subject, "SOURCE_PINS", pins):
                report = subject.verify_sources(root, ledger())
            self.assertEqual("PASS_WITH_DECLARED_GAPS", report["result"])
            self.assertFalse(report["application_tests_executed"])
            self.assertFalse(report["canonical_amendment_approved"])
            self.assertEqual(15, report["sensitive_api_profiles_checked"])
            self.assertEqual(8, report["test_sources_present"])
            self.assertEqual(6, len(report["remaining_gates"]))

    def test_source_drift_and_missing_test_file_are_failures(self) -> None:
        for mode in ["drift", "missing-test"]:
            with self.subTest(mode=mode), tempfile.TemporaryDirectory() as tmp:
                root = Path(tmp)
                pins = fixture(root)
                with patch.object(subject, "SOURCE_PINS", pins):
                    if mode == "drift":
                        (root / pins["factor"][0]).write_text("changed source\n")
                    else:
                        (root / subject.TEST_PREFIX / subject.QA_SOURCES[1][0]).unlink()
                    with self.assertRaises(subject.AlignmentError):
                        subject.verify_sources(root, ledger())

    def test_mismatched_profile_fails_even_with_a_matching_fixture_hash(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            root = Path(tmp)
            pins = fixture(root)
            path = root / pins["api"][0]
            raw = path.read_bytes().replace(b"AUTH-E", b"AUTH-A")
            path.write_bytes(raw)
            pins["api"] = (pins["api"][0], subject.blob_sha(raw))
            with patch.object(subject, "SOURCE_PINS", pins), self.assertRaises(subject.AlignmentError):
                subject.verify_sources(root, ledger())

    def test_unsafe_paths_symlinks_and_oversized_files_are_refused(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            root = Path(tmp)
            (root / "valid").write_text("ok")
            (root / "link").symlink_to(root / "valid")
            (root / "large").write_bytes(b"x" * 2_000_001)
            for path in ["/etc/passwd", "../outside", "link", "missing", "large"]:
                with self.subTest(path=path), self.assertRaises(subject.AlignmentError):
                    subject.safe_bytes(root, path)
            self.assertEqual(b"ok", subject.safe_bytes(root, "valid"))


if __name__ == "__main__":
    unittest.main(verbosity=2)
