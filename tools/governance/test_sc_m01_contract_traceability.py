"""SC-M01 read-only canonical identifier-reference tests (not product QA)."""
from __future__ import annotations

import copy
import json
import tempfile
import unittest
from pathlib import Path

from sc_m01_contract_traceability import (
    FILES,
    SNAPSHOT,
    build,
    check,
    git_sha,
    parse_table,
    read_canonical,
    validate,
)


REPO = Path(__file__).resolve().parents[2]


class CanonicalReferenceTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls) -> None:
        _, cls.rows = read_canonical(REPO)
        cls.snapshot = build(REPO)

    def mutable(self):
        return tuple(
            copy.deepcopy(self.rows[k]) for k in ("api", "ipc", "permissions")
        )

    def test_actual_sources_and_snapshot_match(self) -> None:
        self.assertEqual(check(REPO), self.snapshot)
        self.assertEqual(len(self.rows["api"]), 86)
        self.assertEqual(len(self.rows["ipc"]), 47)
        self.assertEqual(len(self.rows["permissions"]), 82)
        self.assertEqual(self.snapshot["test_results"]["executed"], 0)
        self.assertEqual(self.snapshot["owner_approval"], "NOT_GRANTED_BY_THIS_ARTIFACT")

    def test_all_api_test_suffixes_match_contract_id(self) -> None:
        for a in self.rows["api"]:
            self.assertEqual(
                a["test_id"], a["contract_id"].replace("API-WCP-", "TEST-API-")
            )

    def test_all_ipc_test_suffixes_match_contract_id(self) -> None:
        for i in self.rows["ipc"]:
            self.assertEqual(
                i["test_id"], i["contract_id"].replace("IPC-WCP-", "TEST-IPC-")
            )

    def test_unknown_api_permission_fails(self) -> None:
        a, i, p = self.mutable()
        a[0]["permission_id"] = "fake.root"
        with self.assertRaisesRegex(ValueError, "unknown permission"):
            validate(a, i, p)

    def test_duplicate_api_contract_fails(self) -> None:
        a, i, p = self.mutable()
        a[1]["contract_id"] = a[0]["contract_id"]
        with self.assertRaisesRegex(ValueError, "duplicate contract_id"):
            validate(a, i, p)

    def test_duplicate_ipc_operation_fails(self) -> None:
        a, i, p = self.mutable()
        i[1]["ipc_operation_id"] = i[0]["ipc_operation_id"]
        with self.assertRaisesRegex(ValueError, "duplicate ipc_operation_id"):
            validate(a, i, p)

    def test_missing_active_permission_fails(self) -> None:
        a, i, p = self.mutable()
        p[0]["permission_id"] = "unused.new.permission"
        with self.assertRaisesRegex(ValueError, "unknown permission|not referenced"):
            validate(a, i, p)

    def test_permission_surface_substitution_fails(self) -> None:
        a, i, p = self.mutable()
        a[0]["surface"] = "WCP-ADMIN"
        with self.assertRaisesRegex(ValueError, "surface mismatch"):
            validate(a, i, p)

    def test_forged_browser_auth_profile_fails(self) -> None:
        a, i, p = self.mutable()
        a[0]["authentication"] = "AUTH-PRIV"
        with self.assertRaisesRegex(ValueError, "authentication/surface"):
            validate(a, i, p)

    def test_mismatched_api_test_id_fails(self) -> None:
        a, i, p = self.mutable()
        a[0]["test_id"] = "TEST-API-9999"
        with self.assertRaisesRegex(ValueError, "TEST-API binding"):
            validate(a, i, p)

    def test_mismatched_ipc_test_id_fails(self) -> None:
        a, i, p = self.mutable()
        i[0]["test_id"] = "TEST-IPC-9999"
        with self.assertRaisesRegex(ValueError, "TEST-IPC binding"):
            validate(a, i, p)

    def test_silent_privileged_shell_fails(self) -> None:
        a, i, p = self.mutable()
        i[0]["bounded_primitive"] = "shell.exec"
        with self.assertRaisesRegex(ValueError, "unbounded primitive"):
            validate(a, i, p)

    def test_arbitrary_systemctl_proxy_fails(self) -> None:
        a, i, p = self.mutable()
        i[0]["bounded_primitive"] = "generic.systemctl"
        with self.assertRaisesRegex(ValueError, "unbounded primitive"):
            validate(a, i, p)

    def test_invalid_retry_idempotency_combination_fails(self) -> None:
        a, i, p = self.mutable()
        a[0]["retry"] = "RN"
        a[0]["idempotency"] = "none"
        with self.assertRaisesRegex(ValueError, "retry/idempotency"):
            validate(a, i, p)

    def test_changed_api_table_heading_fails(self) -> None:
        text = (REPO / FILES["api"]).read_text(encoding="utf-8")
        text = text.replace(
            "## 8. API Contract Registry", "## 8. Changed API Registry"
        )
        with self.assertRaisesRegex(ValueError, "not found exactly once"):
            parse_table(text, "api")

    def test_changed_table_column_count_fails(self) -> None:
        text = (REPO / FILES["ipc"]).read_text(encoding="utf-8")
        text = text.replace(
            "| contract_id | action_id | job_type |",
            "| contract_id | action_id |",
            1,
        )
        with self.assertRaisesRegex(ValueError, "expected 12 cells"):
            parse_table(text, "ipc")

    def test_snapshot_tamper_is_not_accepted(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            root = Path(tmp)
            for src in FILES.values():
                target = root / src
                target.parent.mkdir(parents=True, exist_ok=True)
                target.write_bytes((REPO / src).read_bytes())
            out = root / SNAPSHOT
            out.parent.mkdir(parents=True, exist_ok=True)
            tampered = copy.deepcopy(self.snapshot)
            tampered["api_test_bindings"][0]["permission_id"] = "malicious"
            out.write_text(json.dumps(tampered), encoding="utf-8")
            with self.assertRaisesRegex(ValueError, "snapshot does not match"):
                check(root)

    def test_source_file_change_causes_snapshot_drift(self) -> None:
        with tempfile.TemporaryDirectory() as tmp:
            root = Path(tmp)
            for src in FILES.values():
                target = root / src
                target.parent.mkdir(parents=True, exist_ok=True)
                target.write_bytes((REPO / src).read_bytes())
            out = root / SNAPSHOT
            out.parent.mkdir(parents=True, exist_ok=True)
            out.write_text(json.dumps(self.snapshot), encoding="utf-8")
            with (root / FILES["api"]).open("ab") as stream:
                stream.write(b"\n")
            with self.assertRaisesRegex(ValueError, "snapshot does not match"):
                check(root)

    def test_git_blob_hash_is_not_plain_sha1(self) -> None:
        self.assertEqual(git_sha(b""), "e69de29bb2d1d6434b8b29ae775ad8c2e48c5391")

    def test_manifest_never_claims_implementation_go(self) -> None:
        self.assertTrue(all(
            r["execution_state"] == "NOT_EXECUTED"
            for r in (
                self.snapshot["api_test_bindings"] +
                self.snapshot["ipc_test_bindings"]
            )
        ))
        self.assertEqual(self.snapshot["implementation_gate"],
                         "SC-M01_PAUSED_CODE_BASELINE_NONE")


if __name__ == "__main__":
    unittest.main()
