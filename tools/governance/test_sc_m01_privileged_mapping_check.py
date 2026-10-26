"""Adversarial tests for Security / IPC-B owner-binding inventory.

Checks are reference tests, never execution or owner-approval assertions.
"""
from __future__ import annotations
import copy
import unittest
from pathlib import Path

from sc_m01_contract_traceability import FILES, parse_table
from sc_m01_privileged_mapping_check import (
    PARENT_BINDING_OPEN,
    SECURITY_PATH,
    examine,
    parse_security,
    validate,
)

ROOT = Path(__file__).resolve().parents[2]


class SecurityIpcMappingTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.source = parse_security((ROOT / SECURITY_PATH).read_text("utf-8"))
        cls.ipc = parse_table((ROOT / FILES["ipc"]).read_text("utf-8"), "ipc")
        cls.perms = parse_table((ROOT / FILES["permissions"]).read_text("utf-8"), "permissions")

    def source_copy(self):
        return copy.deepcopy(self.source), copy.deepcopy(self.ipc), copy.deepcopy(self.perms)

    def test_published_security_ipc_map_inventory(self):
        report = examine(ROOT)
        self.assertEqual(report["security_operations"], 47)
        self.assertEqual(report["ipc_operations"], 47)
        self.assertEqual(report["active_permissions"], 82)
        self.assertEqual(report["audit_event_type_references"], 49)
        self.assertEqual(report["direct_security_permission_expressions"], 35)
        self.assertEqual(report["parent_binding_owner_decisions_required"], 12)
        self.assertFalse(report["execution_permission_granted"])
        self.assertFalse(report["all_other_contracts_approved"])

    def test_12_open_parent_bindings_are_exactly_identified(self):
        report = examine(ROOT)
        self.assertEqual(
            {r["ipc_operation_id"] for r in report["open_parent_bindings"]},
            PARENT_BINDING_OPEN,
        )

    def test_missing_ipc_operation_is_rejected(self):
        s, i, p = self.source_copy()
        i[0]["ipc_operation_id"] = "madeup.operation"
        with self.assertRaisesRegex(ValueError, "operation IDs diverge"):
            validate(s, i, p)

    def test_extra_security_operation_is_rejected(self):
        s, i, p = self.source_copy()
        s[0]["ipc_operation_id"] = "fake.operation"
        with self.assertRaisesRegex(ValueError, "IDs diverge"):
            validate(s, i, p)

    def test_duplicate_security_operation_is_rejected(self):
        s, i, p = self.source_copy()
        s[1]["ipc_operation_id"] = s[0]["ipc_operation_id"]
        with self.assertRaisesRegex(ValueError, "duplicate IPC"):
            validate(s, i, p)

    def test_unknown_direct_permission_must_not_be_silently_approved(self):
        s, i, p = self.source_copy()
        s[0]["permission_expression"] = "unknown.root"
        with self.assertRaisesRegex(ValueError, "new unratified abstract"):
            validate(s, i, p)

    def test_approved_parent_does_not_turn_into_direct_permission(self):
        s, i, p = self.source_copy()
        candidate = next(r for r in s if r["ipc_operation_id"] == "nginx.reload")
        candidate["permission_expression"] = "servers.services.manage"
        with self.assertRaisesRegex(ValueError, "previously abstract binding"):
            validate(s, i, p)

    def test_parent_mapping_without_approved_binding_fails(self):
        s, i, p = self.source_copy()
        candidate = next(r for r in s if r["ipc_operation_id"] == "nginx.reload")
        candidate["origin_action_expression"] = "servers.reboot"
        with self.assertRaisesRegex(ValueError, "abstract permission"):
            validate(s, i, p)

    def test_parent_mapping_with_unrecognized_expression_fails(self):
        s, i, p = self.source_copy()
        candidate = next(r for r in s if r["ipc_operation_id"] == "nginx.reload")
        candidate["permission_expression"] = "unknown.root"
        with self.assertRaisesRegex(ValueError, "abstract permission"):
            validate(s, i, p)

    def test_reboot_two_event_lifecycle_is_explicit(self):
        report = examine(ROOT)
        self.assertEqual(
            report["reboot_audit_lifecycle"],
            "server.reboot.requested + server.reboot.completed",
        )

    def test_shutdown_two_event_lifecycle_is_explicit(self):
        report = examine(ROOT)
        self.assertEqual(
            report["shutdown_audit_lifecycle"],
            "server.shutdown.requested + server.shutdown.completed",
        )

    def test_unknown_multi_event_audit_expression_is_rejected(self):
        s, i, p = self.source_copy()
        s[0]["audit_event_id"] = "some.event + second.event"
        with self.assertRaisesRegex(ValueError, "invalid audit_event_id"):
            validate(s, i, p)

    def test_duplicate_audit_event_identity_is_rejected(self):
        s, i, p = self.source_copy()
        s[1]["audit_event_id"] = s[0]["audit_event_id"]
        with self.assertRaisesRegex(ValueError, "semantic IDs collide"):
            validate(s, i, p)

    def test_bad_audit_event_id_is_rejected(self):
        s, i, p = self.source_copy()
        s[0]["audit_event_id"] = "DROP TABLE"
        with self.assertRaisesRegex(ValueError, "invalid audit_event"):
            validate(s, i, p)

    def test_unknown_failure_class_is_rejected(self):
        s, i, p = self.source_copy()
        s[0]["failure_class"] = "E100"
        with self.assertRaisesRegex(ValueError, "unknown failure"):
            validate(s, i, p)

    def test_wrong_security_scope_is_rejected(self):
        s, i, p = self.source_copy()
        s[0]["scope"] = "ROOT"
        with self.assertRaisesRegex(ValueError, "unexpected Security scope"):
            validate(s, i, p)

    def test_truncated_security_table_is_rejected(self):
        txt = (ROOT / SECURITY_PATH).read_text("utf-8")
        txt = txt.replace("## 9. Privileged Operation Security Matrix — 47 ACTIVE operations",
                          "## 9. Renamed privileged matrix")
        with self.assertRaisesRegex(ValueError, "missing or duplicated"):
            parse_security(txt)

    def test_removed_row_is_rejected(self):
        s, i, p = self.source_copy()
        s.pop()
        with self.assertRaisesRegex(ValueError, "count mismatch"):
            validate(s, i, p)

    def test_source_order_does_not_imply_approval(self):
        report = examine(ROOT)
        self.assertTrue(report["security_ipc_operations_match"])
        self.assertEqual(report["code_baseline"], "NONE")
        self.assertFalse(report["execution_permission_granted"])


if __name__ == "__main__":
    unittest.main()
