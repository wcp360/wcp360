# WCP360 — SC-M01 derived API / IPC / permission test-reference index

**Date:** 2026-10-26  
**Status:** DERIVED EVIDENCE / NON-CANONICAL — NOT OWNER QA TEST BASELINE  
**Reference main:** e1fd356f5aa6bc89e1a9316442f6ed033e90ed14  
**Issues:** #7, #13, #41

## Purpose

This source-derived snapshot is a structured, reviewable mapping of:

- 86 canonical API 1.0 contract IDs → TEST-API references and their declared surfaces, auth profiles, permissions, actions, retries and idempotency classes;
- 47 canonical IPC-B/1 contract IDs → TEST-IPC references and their typed bounded primitives, operation IDs, reconciliation labels and declared inputs;
- 82 active permission IDs with their canonical declared surfaces.

The snapshot is derived mechanically from three exact owner-final Markdown exports currently in main. It does **not** invent endpoint routes, HTTP methods, approval criteria, error DTOs, database columns, session handling, test fixtures, runnable assertions or any privileged operation.

### Commands

From repository root with Python 3.9+ standard library only:

    python3 -m unittest discover -s tools/governance -p 'test_sc_m01_contract_traceability.py' -v
    python3 tools/governance/sc_m01_contract_traceability.py --mode check
    python3 tools/governance/sc_m01_contract_traceability.py --mode summary

To preview the candidate serialized snapshot on stdout without writing:

    python3 tools/governance/sc_m01_contract_traceability.py --mode print-json

Use the last command as a **review input**, not an automatic replacement of tracked owner sources.

## What the checks establish

1. The three exact canonical Markdown tables exist and match the expected schema.
2. Identifiers and source row counts are complete and unique.
3. API test references and IPC test references retain the matching canonical suffix.
4. Every active permission has an API contract binding; no API contract names an unknown permission.
5. Source-declared USER/ADMIN/POLICY/EMERGENCY surfaces and authorization profiles match the published permission classifications.
6. Read-only vs key-protected mutation retry/idempotency references remain coherent.
7. IPC operation IDs remain unique and no obviously generic privileged primitive is substituted.
8. A change to any source byte, reference mapping or the derived evidence file makes the snapshot drift check fail.
9. Negative mutation unit tests cover missing identifiers, privilege-surface substitution, arbitrary shell/systemctl primitives and tampering.

## What the checks **do not** establish

- **No executable application test** was performed. All 133 contract-linked references are recorded as NOT_EXECUTED.
- They are not complete test specifications and lack QA-owner approved scenarios/expected outcomes.
- They do not prove API route/method/DTO completeness or end-to-end authorization.
- They cannot verify tenant-aware DB constraints, idempotency durability, audit transactions or daemon safety.
- They do not establish that WCP360 is ready for code, deployable, secure, accessible or feature complete.
- They do not satisfy the missing SC-M01 QA owner artifact under specifications/tests/.
- Automated checks are E1, not independent E3 approval, and do not waive branch protections.

## Source provenance

The data file records the Git blob SHA and exact repository path of each of the three canonical owners' exports; it is pinned to the inspected main SHA. To change it, update the authoritative source through its owner first, review that change, regenerate the derived snapshot, run tests and commit the result with a documented delta. Do not silently rewrite authority.

## Requirements for the TESTS / QA owner (#7)

Use these references as input for a *separate owner-approved* SC-M01 Test Baseline. Complete the A01..A20 acceptance checklist: expected outcomes, negative auth tests, fixtures, failure injection, DB/observability assertions, deterministic CI evidence, coverage gaps and statuses. No PASS/FAIL is inferred from a contract ID or from this CI job.

## Mandatory gate

SC-M01 = PAUSED — TARGETED EVIDENCE  
SC-M02 = NOT AUTHORIZED  
CODE-BASELINE = NONE  
5 of 8 owner artifacts are published; Database #6, QA #7, Engineering #9 remain missing.
