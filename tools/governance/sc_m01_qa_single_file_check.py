#!/usr/bin/env python3
"""Read-only historical QA P1 source-completeness check (NOT QA approval).

Validates derived source relationships and owner-pending flags. It cannot
determine whether proposed assertions are correct, approved or executable.
This is repository tooling, not WCP360 product application code. The final
QA v1.0 owner export is outside this historical proposal check.
"""
from __future__ import annotations

import argparse
import json
import re
import sys
from pathlib import Path
from typing import Any

from sc_m01_contract_traceability import git_sha

CANDIDATE = (
    "governance/proposals/"
    "WCP360_SC-M01_Test_Baseline_v1.0_SINGLE_FILE_OWNER_SIGNOFF_CANDIDATE.md"
)
ORIGINAL = (
    "governance/proposals/"
    "SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md"
)
API_IPC = (
    "governance/proposals/"
    "SC-M01-QA-133-TEST-RECORDS-OWNER-CANDIDATE-v0.1.json"
)
NEG_WEB = (
    "governance/proposals/"
    "SC-M01-QA-66-SECURITY-TEST-ANNEX-OWNER-CANDIDATE-v0.1.json"
)
QA_INDEX = "governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json"
DECISIONS = (
    "governance/proposals/"
    "SC-M01-QA-A01-A20-OWNER-DECISION-PACKET-v0.1.md"
)
INPUTS = [ORIGINAL, API_IPC, NEG_WEB, QA_INDEX, DECISIONS]
PROPOSED_CASE_SOURCES = {
    "C": (17, (
        "governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md",
        "governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md"
    )),
    "TQ": (10, ("governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md",)),
    "CQ": (12, ("governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md",)),
    "HQ": (15, ("governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md",)),
    "SQ": (16, ("governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md",)),
    "FQ": (15, ("governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md",))
}

MAX_DOC_BYTES = 700_000
MAX_INPUT_BYTES = 700_000

class ReviewEvidenceError(ValueError):
    """An exact-source or classification check failed."""

def _read(root: Path, name: str, max_bytes: int) -> tuple[bytes, str]:
    p = root / name
    if p.is_symlink() or not p.is_file():
        raise ReviewEvidenceError("missing or symlinked input")
    raw = p.read_bytes()
    if len(raw) > max_bytes:
        raise ReviewEvidenceError("bounded review input exceeds permitted size")
    return raw, git_sha(raw)

def _part(doc: str, start: str, end: str) -> str:
    a = doc.find(start)
    b = doc.find(end, a+len(start))
    if a < 0 or b <= a or doc.count(start) != 1:
        raise ReviewEvidenceError("missing, repeated or unordered QA part marker")
    return doc[a:b]

def _validate_refs(text: str, prefix: str, records: list[dict],
                   expected: int, kind: str) -> None:
    if len(records) != expected:
        raise ReviewEvidenceError("QA source review records count drifted")
    found_ids = re.findall(r"^### "+re.escape(prefix)+r"([0-9]{4}) — ", text, re.M)
    if len(found_ids) != expected or len(set(found_ids)) != expected:
        raise ReviewEvidenceError("QA candidate test heading count/uniqueness drift")
    for rec in records:
        test_id = rec.get("test_id")
        contract_id = rec.get("origin_contract_id")
        if not isinstance(test_id, str) or not isinstance(contract_id, str):
            raise ReviewEvidenceError("review input missing source TEST identity")
        marker = f"### {test_id} — {contract_id}"
        start = text.find(marker)
        if start < 0 or text.count(marker) != 1:
            raise ReviewEvidenceError("source-linked TEST identity missing")
        stop = text.find("\n### ", start+len(marker))
        entry = text[start:stop if stop >= 0 else None]
        if "**Status:** `BLOCKED / NOT_EXECUTED`" not in entry:
            raise ReviewEvidenceError("QA review test status promoted or lost")
        if "**Actual execution:** `NONE` — no PASS/FAIL." not in entry:
            raise ReviewEvidenceError("QA review execution evidence falsely promoted")
        if f"| `origin_contract_id` | `{contract_id}` |" not in entry:
            raise ReviewEvidenceError("QA contract mapping drift")
        for key, v in rec["source_reference"].items():
            value = str(v) if v is not None else "NOT_SPECIFIED_IN_SOURCE"
            value = value.replace("`", "'")
            expected_row = f"| {key} | `{value}` |"
            if expected_row not in entry:
                raise ReviewEvidenceError("QA typed source metadata altered")
        for scenario in rec.get("proposed_scenarios", []):
            if f"- {scenario}" not in entry:
                raise ReviewEvidenceError("QA proposed assertion missing or modified")
        for item in rec.get("blocked_on", []):
            if f"- {item}" not in entry:
                raise ReviewEvidenceError("QA missing exact pending dependency")
        if not rec.get("forbidden_side_effects"):
            raise ReviewEvidenceError("QA input missing negative no-effect assertions")
        if rec.get("actual_execution") is not None or (
            rec.get("execution_state") != "NOT_EXECUTED"
            or rec.get("design_state") != "BLOCKED"
            or rec.get("owner_acceptance") != "PENDING_TESTS_QA"
        ):
            raise ReviewEvidenceError("source QA record contained fabricated approval")
    expected_ids = {x["test_id"] for x in records}
    if {prefix+x for x in found_ids} != expected_ids:
        raise ReviewEvidenceError("QA source-linked TEST-ID set mismatch")

def _verify_85_sourced_proposals(
    part_vi: str,
    scenario_sources: dict[str, tuple[str, str]],
) -> int:
    """Prove 85 historical proposal descriptors are copied verbatim, not final."""
    marker = "## PART VI-A — 85 source-exact historical proposal scenario rows"
    if marker not in part_vi or part_vi.count(marker) != 1:
        raise ReviewEvidenceError("source-exact 85-scenario appendix missing")
    annex = part_vi.split(marker, 1)[1]
    tick = chr(96)
    seen: set[str] = set()
    for prefix, (expected, paths) in PROPOSED_CASE_SOURCES.items():
        matching: dict[str, tuple[str, str, str]] = {}
        pattern = re.compile(
            r"^\|\s*(" + re.escape(prefix) +
            r"[0-9]{2})(?:\s*—[^|]*)?\s*\|"
        )
        for path in paths:
            if path not in scenario_sources:
                raise ReviewEvidenceError("historical scenario source unavailable")
            source_text, source_sha = scenario_sources[path]
            # The annex's *provenance index*, not just each local case, must
            # pin the correct source Git blob; a tampered header is material.
            expected_index = "| " + tick + path + tick + " | " + tick + source_sha + tick + " |"
            if expected_index not in annex:
                raise ReviewEvidenceError("historical source SHA/path drift")
            for line in source_text.splitlines():
                match = pattern.match(line)
                if not match:
                    continue
                scenario_id = match.group(1)
                if scenario_id in matching:
                    raise ReviewEvidenceError("duplicate historical scenario source ID")
                matching[scenario_id] = (line, path, source_sha)
        if len(matching) != expected:
            raise ReviewEvidenceError("historical proposal source inventory changed")
        for i in range(1, expected + 1):
            scenario_id = f"{prefix}{i:02d}"
            if scenario_id not in matching:
                raise ReviewEvidenceError("historical scenario source ID missing")
            line, path, sha = matching[scenario_id]
            heading = "### " + scenario_id + " — "
            if len(re.findall(
                "^" + re.escape(heading), annex, flags=re.M
            )) != 1:
                raise ReviewEvidenceError("historical QA scenario heading drift")
            provenance = (
                "**Source:** " + tick + path + tick +
                " — blob " + tick + sha + tick + "."
            )
            if provenance not in annex:
                raise ReviewEvidenceError("historical source SHA/path drift")
            verbatim = tick * 3 + "text\n" + line + "\n" + tick * 3
            if annex.count(verbatim) != 1:
                raise ReviewEvidenceError("historical scenario row changed")
            seen.add(scenario_id)
    if len(seen) != 85 or annex.count("**Owner triage:** " + tick + "PENDING" + tick) != 85:
        raise ReviewEvidenceError("85 historical proposals incorrectly approved")
    if annex.count("**execution:** " + tick + "NOT_EXECUTED" + tick) != 85:
        raise ReviewEvidenceError("historical scenario execution fabricated")
    if annex.count("**owner approval:** " + tick + "NO" + tick) != 85:
        raise ReviewEvidenceError("historical scenario owner signoff fabricated")
    return 85

def verify(source: str, original: str, api_ipc: dict[str, Any],
           neg_web: dict[str, Any], coverage: dict[str, Any],
           decision_text: str, source_shas: dict[str, str],
           scenario_sources: dict[str, tuple[str, str]] | None = None) -> dict[str, Any]:
    if len(source.encode("utf-8")) > MAX_DOC_BYTES:
        raise ReviewEvidenceError("QA single-file exceeds size guardrail")
    head = source.split("## PART I —",1)[0]
    if "P1 NON-CANONICAL / QA OWNER ACCEPTANCE REQUIRED — NOT FINAL" not in head:
        raise ReviewEvidenceError("QA candidate no longer clearly noncanonical")
    if "DO NOT COUNT THIS FILE IN 8/8" not in head:
        raise ReviewEvidenceError("QA 5/8 owner boundary warning missing")
    for p in INPUTS:
        if f"`{p}` | `{source_shas[p]}`" not in source:
            raise ReviewEvidenceError("source provenance Git blob mismatch")
    if "<!-- BEGIN VERBATIM QA OWNER CANDIDATE SOURCE;" not in source:
        raise ReviewEvidenceError("original QA baseline source block missing")
    if original not in source:
        raise ReviewEvidenceError("original owner candidate source was modified")

    p_api = _part(source,"## PART II —","## PART III —")
    p_ipc = _part(source,"## PART III —","## PART IV —")
    p_neg = _part(source,"## PART IV —","## PART V —")
    p_web = _part(source,"## PART V —","## PART VI —")
    p_85 = _part(source,"## PART VI —","## PART VII —")
    signoff = source[source.index("## PART VII —"):]

    if (len(coverage.get("api_contracts", [])),len(coverage.get("ipc_contracts",[])),
        len(coverage.get("permissions",[]))) != (86,47,82):
        raise ReviewEvidenceError("canonical source-derived 86/47/82 count drift")
    if (len(api_ipc.get("api_records",[])),
        len(api_ipc.get("ipc_records",[]))) != (86,47):
        raise ReviewEvidenceError("QA input API/IPC counts drift")
    if (len(neg_web.get("security_negative_catalog",[])),
        len(neg_web.get("browser_case_mapping_proposal",[]))) != (26,40):
        raise ReviewEvidenceError("QA source-negative/browser counts drift")
    if (api_ipc.get("counts",{}).get("actual_executions") != 0
        or neg_web.get("registry_counts",{}).get("product_tests_executed") != 0):
        raise ReviewEvidenceError("QA source input misrepresents executions")
    _validate_refs(p_api, "TEST-API-",api_ipc["api_records"],86,"API")
    _validate_refs(p_ipc, "TEST-IPC-",api_ipc["ipc_records"],47,"IPC")
    n_refs=set()
    n_headers=re.findall(r"^### (N[0-9]{2}) — ",p_neg,re.M)
    if len(n_headers)!=26 or len(set(n_headers))!=26:
        raise ReviewEvidenceError("N01–N26 headings drift")
    for rec in neg_web["security_negative_catalog"]:
        label=rec["reference_label"]
        n_refs.add(label)
        marker=f"### {label} — {rec['original_security_label']}"
        if p_neg.count(marker)!=1 or rec["expected_behavior_proposal"] not in p_neg:
            raise ReviewEvidenceError("N source wording or proposed expectation drift")
        if rec.get("owner_approval") is not False or rec.get("execution_state")!="NOT_EXECUTED":
            raise ReviewEvidenceError("N source record falsely owner-approved")
    if set(n_headers)!=n_refs:
        raise ReviewEvidenceError("N reference set drift")
    web_ids=set()
    web_headers=re.findall(r"^### (TEST-SEC-WEB-[0-9]{3}) — ",p_web,re.M)
    if len(web_headers)!=40 or len(set(web_headers))!=40:
        raise ReviewEvidenceError("WEB headings count/duplicates changed")
    for rec in neg_web["browser_case_mapping_proposal"]:
        label=rec["reference_id"]
        web_ids.add(label)
        marker=f"### {label} — {rec['proposed_title']} (PROPOSED mapping)"
        if p_web.count(marker)!=1 or rec["proposed_expected_behavior"] not in p_web:
            raise ReviewEvidenceError("WEB per-ID proposed scenario altered or lost")
        if rec.get("owner_approval") is not False or rec.get("execution_state")!="NOT_EXECUTED":
            raise ReviewEvidenceError("WEB source record falsely approved")
    if set(web_headers)!=web_ids:
        raise ReviewEvidenceError("WEB reference set drift")
    families={"C":17,"TQ":10,"CQ":12,"HQ":15,"SQ":16,"FQ":15}
    count_85=0
    for prefix,n in families.items():
        for i in range(1,n+1):
            label=f"`{prefix}{i:02d}`"
            if p_85.count("| "+label+" |") != 1:
                raise ReviewEvidenceError("85 local proposal intake entries drift")
            count_85 += 1
    if count_85 != 85 or p_85.count("**PENDING QA TRIAGE**") != 85:
        raise ReviewEvidenceError("85 proposal decisions were incorrectly closed")
    if scenario_sources is not None:
        _verify_85_sourced_proposals(p_85, scenario_sources)
    for prefix,n in (("A",20),("QI-",7)):
        for i in range(1,n+1):
            label=f"{prefix}{i:02d}"
            if signoff.count("| "+label+" (") != 1:
                raise ReviewEvidenceError("specialized QA owner decision absent")
    finalblock=signoff.split("### Owner publication block",1)
    if len(finalblock) != 2:
        raise ReviewEvidenceError("QA owner publication boundary missing")
    final=finalblock[1].split("### Required next owner action",1)[0]
    expected_lines=[
        "ARTIFACT CLASSIFICATION = P1 OWNER REVIEW CANDIDATE — NOT FINAL",
        "FINAL APPROVED CONTENT = NO",
        "OWNER PUBLICATION APPROVAL = NOT PRESENT",
        "OWNER EXACT QA DECISIONS = PENDING",
        "PRODUCT TESTS ACTUALLY EXECUTED = 0",
        "RESULT PASS = 0",
        "RESULT FAIL = 0",
        "FINAL SPECIFICATIONS/TESTS/ ARTIFACT = NOT PUBLISHED",
        "QA ISSUE #7 = OPEN",
        "MASTER SC-M01 IMPLEMENTATION GATE = 5/8",
        "SC-M01 = PAUSED — TARGETED EVIDENCE",
        "SC-M02 = NOT AUTHORIZED",
        "CODE-BASELINE = NONE"
    ]
    for line in expected_lines:
        if line not in final:
            raise ReviewEvidenceError("QA final approval/implementation gate falsely altered")
    if "P1 / P2 / P3" not in decision_text:
        raise ReviewEvidenceError("QA owner publication provenance options absent")
    return {
        "evidence_scope":"HISTORICAL_PROPOSAL_CORPUS_ONLY",
        "evidence_as_of":"2026-10-26",
        "current_final_qa_validation":"NOT_PERFORMED",
        "status":"PASS_SOURCE_COMPLETENESS_ONLY_NONCANONICAL",
        "api_records":86,"ipc_records":47,"negative_labels":26,
        "web_mapping_proposals":40,"local_proposal_triage_labels":85,
        "permission_registry_references":82,
        "specialized_qa_owner_approvals":0,
        "verbatim_historical_scenario_rows_checked":85 if scenario_sources is not None else 0,
        "executed_product_tests":0,
        "historical_governance_owner_artifact_gate":"5/8",
        "historical_sc_m01":"PAUSED_TARGETED_EVIDENCE",
        "code_baseline":"NONE"
    }

def inspect(root: Path) -> dict[str, Any]:
    candidate,_ = _read(root,CANDIDATE,MAX_DOC_BYTES)
    files = {p:_read(root,p,MAX_INPUT_BYTES) for p in INPUTS}
    extra_paths = {
        name for _,paths in PROPOSED_CASE_SOURCES.values() for name in paths
    }
    scenarios = {
        name: (raw.decode("utf-8"), sha)
        for name in sorted(extra_paths)
        for raw,sha in [_read(root,name,MAX_INPUT_BYTES)]
    }
    return verify(
        candidate.decode("utf-8"),
        files[ORIGINAL][0].decode("utf-8"),
        json.loads(files[API_IPC][0].decode("utf-8")),
        json.loads(files[NEG_WEB][0].decode("utf-8")),
        json.loads(files[QA_INDEX][0].decode("utf-8")),
        files[DECISIONS][0].decode("utf-8"),
        {p:blobsha for p,(_,blobsha) in files.items()},
        scenario_sources=scenarios
    )

def main(argv: list[str] | None = None) -> int:
    parser=argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--repo",type=Path,default=Path(__file__).resolve().parents[2])
    parser.add_argument("--json",action="store_true")
    args=parser.parse_args(argv)
    try:
        report=inspect(args.repo.resolve())
    except (ValueError,OSError,UnicodeError,KeyError,TypeError,json.JSONDecodeError) as exc:
        print("QA P1 single-file integrity FAIL: "+str(exc),file=sys.stderr)
        return 2
    if args.json:
        print(json.dumps(report,indent=2,sort_keys=True))
    else:
        print("HISTORICAL QA P1 proposal source-consistent: 199 case records, "
              "85 triage labels, zero owner approvals, zero executed product tests; "
              "HISTORICAL 5/8 ONLY; CURRENT FINAL QA NOT VALIDATED.")
    return 0

if __name__=="__main__":
    raise SystemExit(main())
