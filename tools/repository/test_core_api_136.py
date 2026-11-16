#!/usr/bin/env python3
"""Mutation tests for the #136 declaration-drift checker (not canonical QA)."""

from __future__ import annotations

import importlib.util
import json
import shutil
import tempfile
import unittest
from pathlib import Path
from types import ModuleType


ROOT = Path(__file__).resolve().parents[2]
CHECKER_FILE = Path(__file__).with_name("check_core_api_136.py")
SPEC = importlib.util.spec_from_file_location("check_core_api_136", CHECKER_FILE)
if SPEC is None or SPEC.loader is None:
    raise RuntimeError("Checker import unavailable")
checker = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(checker)


class CoreApi136DriftTest(unittest.TestCase):
    def setUp(self) -> None:
        self.temp = tempfile.TemporaryDirectory(prefix="wcp-api-136-test-")
        self.addCleanup(self.temp.cleanup)
        self.fixture = Path(self.temp.name)
        for source in checker.SOURCE_FILES.values():
            target = self.fixture / source
            target.parent.mkdir(parents=True, exist_ok=True)
            shutil.copyfile(ROOT / source, target)

    def mutate(self, source: str, list_name: str, id_key: str, identity: str, key: str, value: object) -> None:
        target = self.fixture / checker.SOURCE_FILES[source]
        contents = json.loads(target.read_text(encoding="utf-8"))
        rows = [row for row in contents[list_name] if row.get(id_key) == identity]
        self.assertEqual(len(rows), 1)
        rows[0][key] = value
        target.write_text(json.dumps(contents, indent=2) + "\n", encoding="utf-8")

    def test_current_source_has_exact_five_reviewed_declarations_without_pass_credit(self) -> None:
        evidence = checker.verify(self.fixture)
        self.assertEqual(evidence["contracts_checked"], 5)
        self.assertEqual(evidence["status"], "DECLARATION_DRIFT_CHECK_ONLY")
        self.assertFalse(evidence["canonical_implementation_qualified"])
        self.assertEqual(evidence["canonical_test_api_passes"], 0)
        self.assertFalse(evidence["authorizes_permission_expansion"])
        self.assertFalse(evidence["authorizes_merge_or_release"])

    def test_jobs_read_permission_alias_mutation_is_denied(self) -> None:
        self.mutate("api", "contracts", "contract_id", "API-WCP-0004", "permission_id", "candidate.hosting.plan")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_session_mutation_idempotency_downgrade_is_denied(self) -> None:
        self.mutate("api", "contracts", "contract_id", "API-WCP-0701", "idempotency", "none")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_policy_audit_auth_downgrade_in_daemon_is_denied(self) -> None:
        self.mutate("daemon", "apis", "id", "API-WCP-0006", "authentication", "AUTH-A")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_servers_admin_surface_downgrade_in_action_catalogue_is_denied(self) -> None:
        self.mutate("actions", "actions", "contract_id", "API-WCP-0801", "surface", "BOTH")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_iam_management_permission_inactive_is_denied(self) -> None:
        self.mutate("permissions", "permissions", "id", "iam.users.manage", "status", "INACTIVE")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_missing_source_is_denied(self) -> None:
        (self.fixture / checker.SOURCE_FILES["daemon"]).unlink()
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_candidate_permission_cannot_enter_canonical_catalogue(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["permissions"]
        contents = json.loads(path.read_text(encoding="utf-8"))
        contents["permissions"].append({
            "id": "candidate.hosting.plan",
            "surface": "WCP-USER",
            "status": "ACTIVE",
        })
        path.write_text(json.dumps(contents, indent=2) + "\n", encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_duplicate_contract_ids_are_denied(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["api"]
        contents = json.loads(path.read_text(encoding="utf-8"))
        contents["contracts"].append(dict(contents["contracts"][0]))
        path.write_text(json.dumps(contents, indent=2) + "\n", encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_duplicate_top_level_json_property_is_rejected(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["api"]
        original = path.read_text(encoding="utf-8")
        tampered = original.replace('"count": 86,', '"count": 86,\n  "count": 86,', 1)
        self.assertNotEqual(tampered, original)
        path.write_text(tampered, encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_duplicate_nested_contract_property_is_rejected_even_outside_five_rows(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["api"]
        original = path.read_text(encoding="utf-8")
        tampered = original.replace(
            '"permission_id": "applications.read",',
            '"permission_id": "applications.read", "permission_id": "jobs.read",',
            1,
        )
        self.assertNotEqual(tampered, original)
        path.write_text(tampered, encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_unicode_escaped_duplicate_property_is_rejected(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["api"]
        original = path.read_text(encoding="utf-8")
        tampered = original.replace(
            '"permission_id": "applications.read",',
            '"permission_id": "applications.read", "perm\\u0069ssion_id": "jobs.read",',
            1,
        )
        self.assertNotEqual(tampered, original)
        path.write_text(tampered, encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_api_array_cardinality_must_match_reviewed_metadata(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["api"]
        contents = json.loads(path.read_text(encoding="utf-8"))
        contents["contracts"].pop()
        path.write_text(json.dumps(contents, indent=2) + "\n", encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_action_array_cardinality_must_match_reviewed_metadata(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["actions"]
        contents = json.loads(path.read_text(encoding="utf-8"))
        contents["actions"].pop()
        path.write_text(json.dumps(contents, indent=2) + "\n", encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_permission_array_cardinality_must_match_reviewed_metadata(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["permissions"]
        contents = json.loads(path.read_text(encoding="utf-8"))
        contents["permissions"].pop()
        path.write_text(json.dumps(contents, indent=2) + "\n", encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)



    def test_daemon_array_cardinality_must_match_reviewed_declaration(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["daemon"]
        contents = json.loads(path.read_text(encoding="utf-8"))
        self.assertEqual(len(contents["apis"]), 86)
        contents["apis"].pop()
        path.write_text(json.dumps(contents, indent=2) + "\n", encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_duplicate_daemon_top_level_metadata_is_denied(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["daemon"]
        original = path.read_text(encoding="utf-8")
        tampered = original.replace(
            '"schema_version": 1,',
            '"schema_version": 1, "schema_version": 1,',
            1,
        )
        self.assertNotEqual(tampered, original)
        path.write_text(tampered, encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_duplicate_permission_key_outside_five_reviewed_rows_is_denied(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["permissions"]
        original = path.read_text(encoding="utf-8")
        tampered = original.replace(
            '"id": "api.tokens.manage",',
            '"id": "api.tokens.manage", "id": "jobs.read",',
            1,
        )
        self.assertNotEqual(tampered, original)
        path.write_text(tampered, encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_nonfinite_json_values_are_denied(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["actions"]
        original = path.read_text(encoding="utf-8")
        self.assertTrue(original.lstrip().startswith("{"))
        for constant in ("NaN", "Infinity", "-Infinity"):
            with self.subTest(constant=constant):
                # Python's standard decoder accepts these unless explicitly forbidden.
                tampered = original.replace("{", '{"invalid_extra": ' + constant + ", ", 1)
                path.write_text(tampered, encoding="utf-8")
                with self.assertRaises(checker.ContractDrift):
                    checker.verify(self.fixture)


    def test_unreviewed_api_identity_replacement_with_same_count_is_denied(self) -> None:
        self.mutate("api", "contracts", "contract_id", "API-WCP-0101",
                    "contract_id", "API-WCP-9999")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_unreviewed_action_permission_drift_with_same_count_is_denied(self) -> None:
        self.mutate("actions", "actions", "contract_id", "API-WCP-0101",
                    "permission_id", "jobs.read")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_unreviewed_daemon_authentication_drift_is_denied(self) -> None:
        self.mutate("daemon", "apis", "id", "API-WCP-0101",
                    "authentication", "AUTH-A")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_unreviewed_api_response_schema_drift_is_denied(self) -> None:
        self.mutate("api", "contracts", "contract_id", "API-WCP-0101",
                    "response_schema", "UnreviewedResponse")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_unreviewed_action_domain_drift_is_denied(self) -> None:
        self.mutate("actions", "actions", "contract_id", "API-WCP-0101",
                    "domain", "jobs")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_unreviewed_permission_reference_drift_across_three_sources_is_denied(self) -> None:
        # Even three agreeing source registries cannot invent a permission.
        self.mutate("api", "contracts", "contract_id", "API-WCP-0101",
                    "permission_id", "not.a.canonical.permission")
        self.mutate("actions", "actions", "contract_id", "API-WCP-0101",
                    "permission_id", "not.a.canonical.permission")
        self.mutate("daemon", "apis", "id", "API-WCP-0101",
                    "permission", "not.a.canonical.permission")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)


    def test_missing_daemon_permission_mirror_row_is_denied(self) -> None:
        path = self.fixture / checker.SOURCE_FILES["daemon"]
        contents = json.loads(path.read_text(encoding="utf-8"))
        self.assertEqual(len(contents["permissions"]), 82)
        contents["permissions"].pop()
        path.write_text(json.dumps(contents, indent=2) + "\n", encoding="utf-8")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_daemon_permission_mirror_identity_swap_with_same_count_is_denied(self) -> None:
        self.mutate("daemon", "permissions", "id", "api.tokens.manage",
                    "id", "api.tokens.unreviewed")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_daemon_permission_mirror_surface_downgrade_is_denied(self) -> None:
        self.mutate("daemon", "permissions", "id", "api.tokens.manage",
                    "surface", "WCP-USER")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_daemon_permission_mirror_status_drift_is_denied(self) -> None:
        self.mutate("daemon", "permissions", "id", "api.tokens.manage",
                    "status", "INACTIVE")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_daemon_permission_mirror_duplicate_identity_is_denied(self) -> None:
        self.mutate("daemon", "permissions", "id", "applications.create",
                    "id", "api.tokens.manage")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)

    def test_candidate_permission_cannot_hide_inside_daemon_mirror(self) -> None:
        self.mutate("daemon", "permissions", "id", "api.tokens.manage",
                    "id", "candidate.hosting.plan")
        with self.assertRaises(checker.ContractDrift):
            checker.verify(self.fixture)


if __name__ == "__main__":
    unittest.main(verbosity=2)
