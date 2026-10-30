#!/usr/bin/env python3
"""Validate historical proposed QA references, never execute product tests.

This tool does not approve mappings or release SC-M01 implementation.
Current final QA v1.0 validation is outside this historical proposal check.
"""
from __future__ import annotations

import argparse
import json
import re
import sys
from pathlib import Path

from sc_m01_contract_traceability import git_sha

INDEX = "governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json"
QA133 = "governance/proposals/SC-M01-QA-133-TEST-RECORDS-OWNER-CANDIDATE-v0.1.json"
QA66 = "governance/proposals/SC-M01-QA-66-SECURITY-TEST-ANNEX-OWNER-CANDIDATE-v0.1.json"
SECURITY = "specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md"

def load(root: Path, path: str, limit: int) -> tuple[dict, str]:
    target = root / path
    if not target.is_file() or target.is_symlink():
        raise ValueError("missing/symlink source " + path)
    raw = target.read_bytes()
    if len(raw) > limit:
        raise ValueError("unbounded source " + path)
    data = json.loads(raw.decode("utf-8"))
    if not isinstance(data, dict):
        raise ValueError("invalid source object")
    return data, git_sha(raw)

def verify(index: dict, index_sha: str, q133: dict, q66: dict,
           security_text: str, security_sha: str) -> dict:
    if index.get("status") != "DERIVED_EVIDENCE_ONLY_NOT_QA_BASELINE":
        raise ValueError("source index authority drift")
    api, ipc, perms = (index.get("api_contracts"),
                       index.get("ipc_contracts"), index.get("permissions"))
    if not all(isinstance(x, list) for x in (api, ipc, perms)):
        raise ValueError("source index structure missing")
    if (len(api), len(ipc), len(perms)) != (86, 47, 82):
        raise ValueError("canonical 86/47/82 count drift")
    if q133.get("source", {}).get("source_cover_index_git_blob_sha") != index_sha:
        raise ValueError("QA 133 source index fingerprint drift")
    if q133.get("status") != "QA_OWNER_REVIEW_CANDIDATE_NONCANONICAL":
        raise ValueError("QA 133 candidate authority drift")
    qa_api, qa_ipc = q133.get("api_records"), q133.get("ipc_records")
    if not isinstance(qa_api, list) or not isinstance(qa_ipc, list) or (
        len(qa_api), len(qa_ipc)
    ) != (86, 47):
        raise ValueError("QA 133 candidate size drift")
    api_by_test = {r["test_id"]: r for r in api}
    ipc_by_test = {r["test_id"]: r for r in ipc}
    if len(api_by_test) != 86 or len(ipc_by_test) != 47:
        raise ValueError("canonical test references not unique")
    for row in qa_api:
        source = api_by_test.get(row.get("test_id"))
        if source is None or row.get("origin_contract_id") != source.get("contract_id"):
            raise ValueError("unknown or wrong QA API test reference")
        mapping = row.get("source_reference", {})
        for own, field in (
            ("permission_id", "permission_id"), ("action_id", "action_id"),
            ("surface", "surface"), ("auth", "authentication"),
            ("logical_request", "request_schema"),
            ("logical_response", "response_schema"), ("error_profile", "errors"),
            ("timeout", "timeout"), ("idempotency", "idempotency"),
        ):
            if mapping.get(own) != source.get(field):
                raise ValueError("QA API source reference mismatch: " + field)
    for row in qa_ipc:
        source = ipc_by_test.get(row.get("test_id"))
        if source is None or row.get("origin_contract_id") != source.get("contract_id"):
            raise ValueError("unknown or wrong QA IPC test reference")
        mapping = row.get("source_reference", {})
        for key in ("ipc_operation_id", "validated_input_schema", "bounded_primitive",
                    "action_id", "job_type", "resource_identifier",
                    "timeout", "idempotency"):
            if mapping.get(key) != source.get(key):
                raise ValueError("QA IPC source reference mismatch: " + key)
    def source_record_is_pending(row: dict) -> bool:
        return (row.get("design_state") == "BLOCKED"
                and row.get("execution_state") == "NOT_EXECUTED"
                and row.get("actual_execution") is None
                and row.get("owner_acceptance") == "PENDING_TESTS_QA")
    if not all(source_record_is_pending(r) for r in qa_api + qa_ipc):
        raise ValueError("QA 133 result or owner approval fabricated")
    if {r["test_id"] for r in qa_api} != set(api_by_test) or (
        {r["test_id"] for r in qa_ipc} != set(ipc_by_test)
    ):
        raise ValueError("QA 133 reference inventory missing IDs")
    if q133.get("counts", {}).get("actual_executions") != 0:
        raise ValueError("QA 133 actual executions nonzero")

    if q66.get("source", {}).get("git_blob_sha") != security_sha:
        raise ValueError("QA 66 Security source fingerprint drift")
    if q66.get("status") != (
        "P1_CANDIDATE_NONCANONICAL_QA_OWNER_REVIEW_NOT_EXECUTED"
    ):
        raise ValueError("QA 66 candidate authority drift")
    negative = q66.get("security_negative_catalog")
    browser = q66.get("browser_case_mapping_proposal")
    if not isinstance(negative, list) or not isinstance(browser, list) or (
        len(negative), len(browser)
    ) != (26, 40):
        raise ValueError("QA 66 candidate inventory changed")
    start = security_text.find("## 14. Negative security test traceability")
    if start < 0:
        raise ValueError("canonical security negative catalog absent")
    source_n = {}
    for line in security_text[start:].splitlines():
        if re.match(r"^N[0-9]{2} ", line):
            source_n[line[:3]] = line[4:]
    wanted_n = {"N" + str(i).zfill(2) for i in range(1, 27)}
    if set(source_n) != wanted_n:
        raise ValueError("canonical N01..N26 catalog drift")
    seen_n = set()
    for row in negative:
        label = row.get("reference_label")
        if label not in wanted_n or label in seen_n:
            raise ValueError("unrecognized/duplicate N reference")
        seen_n.add(label)
        if row.get("original_security_label") != source_n[label]:
            raise ValueError("N source text was altered")
        if not pending(row):
            raise ValueError("N status/approval falsely promoted")
        if not row.get("forbidden_side_effects") or not row.get("planned_evidence"):
            raise ValueError("N evidence assertion missing")
    wanted_web = {"TEST-SEC-WEB-" + str(i).zfill(3) for i in range(1, 41)}
    seen_web = set()
    for row in browser:
        label = row.get("reference_id")
        if label not in wanted_web or label in seen_web:
            raise ValueError("unrecognized/duplicate WEB test reference")
        seen_web.add(label)
        if not pending(row):
            raise ValueError("WEB status/approval falsely promoted")
        if "NEW_PROPOSAL" not in row.get("id_publication_status", ""):
            raise ValueError("WEB per-ID mapping falsely presented as canonical")
        if not row.get("forbidden_side_effects") or not row.get("planned_evidence"):
            raise ValueError("WEB evidence assertion missing")
    if seen_web != wanted_web or seen_n != wanted_n:
        raise ValueError("QA security reference coverage incomplete")
    all_ids = [r["test_id"] for r in qa_api + qa_ipc] + sorted(seen_web) + sorted(seen_n)
    if len(all_ids) != 199 or len(set(all_ids)) != 199:
        raise ValueError("combined review reference IDs not 199 unique")
    if q66.get("registry_counts", {}).get("product_tests_executed") != 0:
        raise ValueError("invented QA product execution")
    return {
        "evidence_scope": "HISTORICAL_PROPOSAL_CORPUS_ONLY",
        "current_final_qa_validation": "NOT_PERFORMED",
        "status": "P1_QA_REFERENCE_INTEGRITY_VERIFIED_NOT_PRODUCT_QA",
        "source_reference_count": 199,
        "api":86, "ipc":47, "security_negative":26, "browser_mapping_proposal":40,
        "qa_owner_approvals":0, "product_test_executions":0,
        "sc_m01_authorized":False, "code_baseline":"NONE",
        "input_qa_index_sha":index_sha, "security_sha":security_sha,
    }

def pending(row: dict) -> bool:
    return (row.get("status") == "BLOCKED"
            and row.get("execution_state") == "NOT_EXECUTED"
            and row.get("owner_approval") is False)

def inspect(repo: Path) -> dict:
    index, srcsha = load(repo, INDEX, 300000)
    q133, _ = load(repo, QA133, 700000)
    q66, _ = load(repo, QA66, 300000)
    secpath = repo / SECURITY
    if not secpath.is_file() or secpath.is_symlink():
        raise ValueError("missing Security source")
    raw = secpath.read_bytes()
    if len(raw) > 500000:
        raise ValueError("oversized Security source")
    return verify(index, srcsha, q133, q66, raw.decode("utf-8"), git_sha(raw))

def main(argv: list[str] | None = None) -> int:
    p = argparse.ArgumentParser(description=__doc__)
    p.add_argument("--repo", type=Path, default=Path(__file__).resolve().parents[2])
    p.add_argument("--json", action="store_true")
    args = p.parse_args(argv)
    try:
        result=inspect(args.repo.resolve())
    except (ValueError, OSError, UnicodeError, KeyError, TypeError,
            json.JSONDecodeError) as e:
        print("QA candidate source review FAIL: " + str(e), file=sys.stderr)
        return 2
    if args.json:
        print(json.dumps(result,indent=2,sort_keys=True))
    else:
        print("199 source references consistent; zero tests executed; "
              "not owner approval, not SC-M01 code authorization.")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
