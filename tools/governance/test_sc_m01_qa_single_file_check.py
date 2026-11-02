"""Negative review-corpus checks. NO WCP360 application QA is executed."""
from __future__ import annotations

import copy
import json
import unittest
from pathlib import Path

from sc_m01_qa_single_file_check import (
    CANDIDATE, ORIGINAL, API_IPC, NEG_WEB, QA_INDEX, DECISIONS,
    INPUTS, PROPOSED_CASE_SOURCES, ReviewEvidenceError, inspect, verify, _read
)

ROOT = Path(__file__).resolve().parents[2]

class SingleFileQAOwnerCandidateTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.document=(ROOT/CANDIDATE).read_text("utf-8")
        cls.old=(ROOT/ORIGINAL).read_text("utf-8")
        cls.api_ipc=json.loads((ROOT/API_IPC).read_text("utf-8"))
        cls.neg_web=json.loads((ROOT/NEG_WEB).read_text("utf-8"))
        cls.coverage=json.loads((ROOT/QA_INDEX).read_text("utf-8"))
        cls.decisions=(ROOT/DECISIONS).read_text("utf-8")
        cls.hashes={p:_read(ROOT,p,700_000)[1] for p in INPUTS}
        paths={p for _,names in PROPOSED_CASE_SOURCES.values() for p in names}
        cls.scenarios={
            path: (raw.decode("utf-8"), sha)
            for path in sorted(paths)
            for raw,sha in [_read(ROOT,path,700_000)]
        }

    def check(self,doc=None,old=None,api=None,negative=None,coverage=None,
              decisions=None,hashes=None,scenarios=None):
        return verify(
            self.document if doc is None else doc,
            self.old if old is None else old,
            self.api_ipc if api is None else api,
            self.neg_web if negative is None else negative,
            self.coverage if coverage is None else coverage,
            self.decisions if decisions is None else decisions,
            self.hashes if hashes is None else hashes,
            scenario_sources=self.scenarios if scenarios is None else scenarios
        )

    def test_exact_199_and_85_review_inventory_not_approved(self):
        outcome=inspect(ROOT)
        self.assertEqual((outcome["api_records"],outcome["ipc_records"],
                          outcome["negative_labels"],
                          outcome["web_mapping_proposals"],
                          outcome["local_proposal_triage_labels"]),
                         (86,47,26,40,85))
        self.assertEqual(outcome["specialized_qa_owner_approvals"],0)
        self.assertEqual(outcome["executed_product_tests"],0)
        self.assertEqual(outcome["historical_governance_owner_artifact_gate"],"5/8")

    def test_drop_api_record_fails(self):
        text=self.document.replace("### TEST-API-0101 — API-WCP-0101",
                                   "### REMOVED API SOURCE",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"test heading"):
            self.check(doc=text)

    def test_drop_ipc_record_fails(self):
        text=self.document.replace("### TEST-IPC-1001 — IPC-WCP-1001",
                                   "### REMOVED IPC SOURCE",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"test heading"):
            self.check(doc=text)

    def test_api_source_permission_tamper_fails(self):
        doc=self.document.replace("| permission_id | `applications.read` |",
                                  "| permission_id | `root.unbounded` |",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"typed source metadata altered"):
            self.check(doc=doc)

    def test_ipc_source_contract_field_tamper_fails(self):
        doc=self.document.replace("| `origin_contract_id` | `IPC-WCP-1001` |",
                                  "| `origin_contract_id` | `IPC-WCP-9999` |",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"QA contract mapping drift"):
            self.check(doc=doc)

    def test_new_fake_pass_in_api_record_rejected(self):
        doc=self.document.replace(
            "**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`.",
            "**Status:** `PASS`; QA owner decision `APPROVED`.",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"status promoted"):
            self.check(doc=doc)

    def test_case_scenario_omission_detected(self):
        snippet=self.api_ipc["api_records"][0]["proposed_scenarios"][0]
        text=self.document.replace("- "+snippet,
                                   "- an unapproved invented happy path",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"proposed assertion"):
            self.check(doc=text)

    def test_exact_old_candidate_source_modified_fails(self):
        doc=self.document.replace("## 3. Mandatory test-record schema",
                                  "## 3. Incomplete invented schema",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"original owner candidate source"):
            self.check(doc=doc)

    def test_original_source_hash_tamper_fails(self):
        original=self.hashes[ORIGINAL]
        doc=self.document.replace(original,"0"*40,1)
        with self.assertRaisesRegex(ReviewEvidenceError,"provenance Git blob"):
            self.check(doc=doc)

    def test_qa_source_record_fake_execution_fails(self):
        x=copy.deepcopy(self.api_ipc)
        x["api_records"][0]["actual_execution"]={"result":"PASS"}
        with self.assertRaisesRegex(ReviewEvidenceError,"fabricated approval"):
            self.check(api=x)

    def test_qa_ipc_source_fake_approval_fails(self):
        x=copy.deepcopy(self.api_ipc)
        x["ipc_records"][0]["owner_acceptance"]="APPROVED"
        with self.assertRaisesRegex(ReviewEvidenceError,"fabricated approval"):
            self.check(api=x)

    def test_qa_web_source_fake_approval_fails(self):
        x=copy.deepcopy(self.neg_web)
        x["browser_case_mapping_proposal"][0]["owner_approval"]=True
        with self.assertRaisesRegex(ReviewEvidenceError,"WEB source record"):
            self.check(negative=x)

    def test_negative_original_text_changed_fails(self):
        x=self.neg_web["security_negative_catalog"][0]
        doc=self.document.replace(f"### {x['reference_label']} — {x['original_security_label']}",
                                  "### N01 — allow any Unix peer",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"N source wording"):
            self.check(doc=doc)

    def test_web_case_mapping_omission_fails(self):
        x=self.neg_web["browser_case_mapping_proposal"][0]
        doc=self.document.replace(
            f"### {x['reference_id']} — {x['proposed_title']} (PROPOSED mapping)",
            "### untracked browser case",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"WEB headings"):
            self.check(doc=doc)

    def test_85_local_label_removed_fails(self):
        doc=self.document.replace("| `C01` | Authorization | **PENDING QA TRIAGE**",
                                  "| `CXX` | Authorization | **PENDING QA TRIAGE**",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"85 local proposal"):
            self.check(doc=doc)

    def test_85_local_label_claimed_approved_fails(self):
        doc=self.document.replace(
            "| `C01` | Authorization | **PENDING QA TRIAGE**",
            "| `C01` | Authorization | **APPROVED**",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"85 proposal decisions"):
            self.check(doc=doc)

    def test_all_85_historical_source_rows_verbatim(self):
        out=self.check()
        self.assertEqual(out["verbatim_historical_scenario_rows_checked"],85)

    def test_one_historical_source_row_tamper_rejected(self):
        row="| C01 | Session ADMIN privilégiée valide ; parent reload ; cible et révision enregistrées |"
        self.assertIn(row,self.document)
        doc=self.document.replace(row,"| C01 | forged approval |",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"historical scenario row changed"):
            self.check(doc=doc)

    def test_one_historical_source_blob_sha_tamper_rejected(self):
        sha="2722238799c2cdee7d863e28b9d6b9728e510512"
        self.assertIn(sha,self.document)
        # The earlier SOURCE-BLOB table is separate; this source is in annex.
        annex_start=self.document.index("## PART VI-A —")
        doc=(self.document[:annex_start] +
             self.document[annex_start:].replace(sha,"0"*40,1))
        with self.assertRaisesRegex(ReviewEvidenceError,"historical source SHA/path drift"):
            self.check(doc=doc)

    def test_historical_case_fake_owner_approval_rejected(self):
        doc=self.document.replace("**owner approval:** "+chr(96)+"NO"+chr(96),
                                  "**owner approval:** "+chr(96)+"YES"+chr(96),1)
        with self.assertRaisesRegex(ReviewEvidenceError,"historical scenario owner signoff"):
            self.check(doc=doc)

    def test_historical_case_fake_execution_rejected(self):
        doc=self.document.replace("**execution:** "+chr(96)+"NOT_EXECUTED"+chr(96),
                                  "**execution:** "+chr(96)+"PASS"+chr(96),1)
        with self.assertRaisesRegex(ReviewEvidenceError,"historical scenario execution"):
            self.check(doc=doc)

    def test_historical_source_external_row_changed_rejected(self):
        s=copy.deepcopy(self.scenarios)
        target="governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md"
        body,sha=s[target]
        s[target]=(body.replace("| CQ01 | peer Unix non autorisé",
                               "| CQ01 | peer Unix désormais autorisé",1),sha)
        with self.assertRaisesRegex(ReviewEvidenceError,"historical scenario row changed"):
            self.check(scenarios=s)

    def test_missing_historical_source_file_rejected(self):
        s=dict(self.scenarios)
        s.pop("governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md")
        with self.assertRaisesRegex(ReviewEvidenceError,"historical scenario source unavailable"):
            self.check(scenarios=s)

    def test_missing_complete_85_scenario_annex_rejected(self):
        doc=self.document.replace(
            "## PART VI-A — 85 source-exact historical proposal scenario rows",
            "## FORGOTTEN HISTORICAL 85 ANNEX",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"source-exact 85-scenario appendix missing"):
            self.check(doc=doc)

    def test_duplicate_historical_case_heading_rejected(self):
        doc=self.document.replace(
            "### C01 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)",
            "### C02 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"historical QA scenario heading drift"):
            self.check(doc=doc)

    def test_missing_owner_a01_decision_fails(self):
        doc=self.document.replace("| A01 (existing QA checklist)",
                                  "| AXX (existing QA checklist)",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"specialized QA owner decision absent"):
            self.check(doc=doc)

    def test_missing_execution_qi_clause_fails(self):
        doc=self.document.replace("| QI-07 (execution integrity)",
                                  "| QI-XX (execution integrity)",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"specialized QA owner decision absent"):
            self.check(doc=doc)

    def test_owner_final_claim_with_no_evidence_rejected(self):
        doc=self.document.replace("FINAL APPROVED CONTENT = NO",
                                  "FINAL APPROVED CONTENT = YES",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"approval/implementation gate"):
            self.check(doc=doc)

    def test_owner_claim_approved_rejected(self):
        doc=self.document.replace("OWNER PUBLICATION APPROVAL = NOT PRESENT",
                                  "OWNER PUBLICATION APPROVAL = YES",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"approval/implementation gate"):
            self.check(doc=doc)

    def test_fabricated_result_pass_counter_rejected(self):
        doc=self.document.replace("RESULT PASS = 0","RESULT PASS = 199",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"approval/implementation gate"):
            self.check(doc=doc)

    def test_silent_6_of_8_claim_rejected(self):
        doc=self.document.replace("MASTER SC-M01 IMPLEMENTATION GATE = 5/8",
                                  "MASTER SC-M01 IMPLEMENTATION GATE = 6/8",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"approval/implementation gate"):
            self.check(doc=doc)

    def test_code_baseline_fabrication_rejected(self):
        doc=self.document.replace("CODE-BASELINE = NONE",
                                  "CODE-BASELINE = IMPLEMENTED",1)
        # The original baseline source is verbatim and also contains NONE;
        # replacing first may alter its source, or the end gate.
        with self.assertRaisesRegex(ReviewEvidenceError,
                                    "original owner candidate source|approval/implementation gate"):
            self.check(doc=doc)

    def test_missing_qa_owner_signoff_block_fails(self):
        doc=self.document.replace("### Owner publication block (intentionally unsigned)",
                                  "### owner bypassed",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"owner publication boundary"):
            self.check(doc=doc)

    def test_missing_source_scenario_family_fails(self):
        doc=self.document.replace("## PART VI — 85 historical",
                                  "## NO MORE SCENARIO TRIAGE — 85 historical",1)
        with self.assertRaisesRegex(ReviewEvidenceError,"missing, repeated or unordered QA part marker"):
            self.check(doc=doc)

if __name__ == "__main__":
    unittest.main()
