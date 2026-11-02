"""Adversarial checks on noncanonical 199-case QA review set (no product tests)."""
from __future__ import annotations

import copy
import unittest
from pathlib import Path

from sc_m01_qa_annex_consistency import (
    INDEX, QA133, QA66, SECURITY, inspect, load, verify
)
from sc_m01_contract_traceability import git_sha

ROOT = Path(__file__).resolve().parents[2]

class QAReviewReferenceTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls) -> None:
        cls.index, cls.sha = load(ROOT, INDEX, 300000)
        cls.q133, _ = load(ROOT, QA133, 700000)
        cls.q66, _ = load(ROOT, QA66, 300000)
        raw = (ROOT / SECURITY).read_bytes()
        cls.sec = raw.decode("utf-8")
        cls.secsha = git_sha(raw)

    def data(self):
        return (copy.deepcopy(self.index), copy.deepcopy(self.q133),
                copy.deepcopy(self.q66))

    def run_verify(self, index, a, b, security=None, sha=None):
        return verify(index, self.sha, a, b,
                      self.sec if security is None else security,
                      self.secsha if sha is None else sha)

    def test_real_source_linkage_all_199_without_owner_or_execution(self):
        result = inspect(ROOT)
        self.assertEqual(result["source_reference_count"], 199)
        self.assertEqual((result["api"],result["ipc"],
                          result["security_negative"],
                          result["browser_mapping_proposal"]), (86,47,26,40))
        self.assertEqual(result["qa_owner_approvals"], 0)
        self.assertEqual(result["product_test_executions"], 0)
        self.assertFalse(result["sc_m01_authorized"])
        self.assertEqual(result["code_baseline"], "NONE")

    def test_tamper_api_base_permission_fails(self):
        idx, a, b = self.data()
        a["api_records"][0]["source_reference"]["permission_id"]="privilege.elevate"
        with self.assertRaisesRegex(ValueError,"API source reference mismatch"):
            self.run_verify(idx,a,b)

    def test_tamper_api_response_fails(self):
        idx,a,b=self.data()
        a["api_records"][0]["source_reference"]["logical_response"]="FakeSuccess"
        with self.assertRaisesRegex(ValueError,"API source reference mismatch"):
            self.run_verify(idx,a,b)

    def test_unknown_test_api_reference_fails(self):
        idx,a,b=self.data()
        a["api_records"][0]["test_id"]="TEST-API-9999"
        with self.assertRaisesRegex(ValueError,"unknown or wrong QA API"):
            self.run_verify(idx,a,b)

    def test_tamper_privileged_ipc_primitive_fails(self):
        idx,a,b=self.data()
        a["ipc_records"][0]["source_reference"]["bounded_primitive"]="shell.exec"
        with self.assertRaisesRegex(ValueError,"QA IPC source reference mismatch"):
            self.run_verify(idx,a,b)

    def test_unknown_ipc_test_reference_fails(self):
        idx,a,b=self.data()
        a["ipc_records"][0]["test_id"]="TEST-IPC-9999"
        with self.assertRaisesRegex(ValueError,"unknown or wrong QA IPC"):
            self.run_verify(idx,a,b)

    def test_api_fake_pass_rejected(self):
        idx,a,b=self.data()
        a["api_records"][0]["execution_state"]="PASS"
        with self.assertRaisesRegex(ValueError,"QA 133 result"):
            self.run_verify(idx,a,b)

    def test_ipc_fake_execution_rejected(self):
        idx,a,b=self.data()
        a["ipc_records"][0]["actual_execution"]={"result":"PASS"}
        with self.assertRaisesRegex(ValueError,"QA 133 result"):
            self.run_verify(idx,a,b)

    def test_133_source_index_sha_tamper_rejected(self):
        idx,a,b=self.data()
        a["source"]["source_cover_index_git_blob_sha"]="0"*40
        with self.assertRaisesRegex(ValueError,"fingerprint drift"):
            self.run_verify(idx,a,b)

    def test_66_security_sha_tamper_rejected(self):
        idx,a,b=self.data()
        b["source"]["git_blob_sha"]="0"*40
        with self.assertRaisesRegex(ValueError,"Security source fingerprint"):
            self.run_verify(idx,a,b)

    def test_negative_original_source_wording_drift_rejected(self):
        idx,a,b=self.data()
        b["security_negative_catalog"][0]["original_security_label"]="shell.exec allowed"
        with self.assertRaisesRegex(ValueError,"N source text"):
            self.run_verify(idx,a,b)

    def test_security_negative_missing_case_rejected(self):
        idx,a,b=self.data()
        b["security_negative_catalog"].pop()
        with self.assertRaisesRegex(ValueError,"candidate inventory changed"):
            self.run_verify(idx,a,b)

    def test_web_duplicate_reference_rejected(self):
        idx,a,b=self.data()
        b["browser_case_mapping_proposal"][0]["reference_id"]=(
            b["browser_case_mapping_proposal"][1]["reference_id"])
        with self.assertRaisesRegex(ValueError,"unrecognized/duplicate WEB"):
            self.run_verify(idx,a,b)

    def test_web_per_id_mapping_falsely_final_rejected(self):
        idx,a,b=self.data()
        b["browser_case_mapping_proposal"][0]["id_publication_status"]="CANONICAL"
        with self.assertRaisesRegex(ValueError,"falsely presented as canonical"):
            self.run_verify(idx,a,b)

    def test_web_case_falsely_approved_rejected(self):
        idx,a,b=self.data()
        b["browser_case_mapping_proposal"][0]["owner_approval"]=True
        with self.assertRaisesRegex(ValueError,"WEB status/approval"):
            self.run_verify(idx,a,b)

    def test_web_case_falsely_executed_rejected(self):
        idx,a,b=self.data()
        b["browser_case_mapping_proposal"][0]["execution_state"]="PASS"
        with self.assertRaisesRegex(ValueError,"WEB status/approval"):
            self.run_verify(idx,a,b)

    def test_negative_evidence_missing_rejected(self):
        idx,a,b=self.data()
        b["security_negative_catalog"][0]["forbidden_side_effects"]=[]
        with self.assertRaisesRegex(ValueError,"N evidence assertion missing"):
            self.run_verify(idx,a,b)

    def test_executed_count_counter_tamper_rejected(self):
        idx,a,b=self.data()
        a["counts"]["actual_executions"]=1
        with self.assertRaisesRegex(ValueError,"executions nonzero"):
            self.run_verify(idx,a,b)

    def test_security_source_negative_label_change_rejected(self):
        idx,a,b=self.data()
        bad_sec=self.sec.replace("N01 unauthorized Unix peer",
                                 "N01 authorized Unix peer")
        with self.assertRaisesRegex(ValueError,"N source text"):
            self.run_verify(idx,a,b,security=bad_sec)

    def test_exact_security_source_hash_lifetime(self):
        result=self.run_verify(*self.data())
        self.assertEqual(result["security_sha"],self.secsha)

if __name__ == "__main__":
    unittest.main()
