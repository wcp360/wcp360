# WCP360 — SC-M01 derived API / IPC / permission test-reference index

> Historical integration note (2026-11-02): this dated branch document is retained
> as development-source evidence. Its original decisions, pending states, path
> literals and execution claims describe the recorded snapshot; they do not
> override the current Core implementation or grant owner approval, independent
> E3 review, a CODE-BASELINE, deployment or production qualification. See the
> [tooling integration guide](../maintenance/BRANCH-TOOLING-INTEGRATION-2026-11-02.md)
> for current paths and separate current/historical CI scopes.
**Historical snapshot date:** 2026-10-26
**Applicability clarified:** 2026-10-30
**Status:** DERIVED EVIDENCE / NON-CANONICAL — NOT OWNER QA TEST BASELINE
**Reference main:** e1fd356f5aa6bc89e1a9316442f6ed033e90ed14
**Issues:** #7, #13, #41

## Purpose

This source-derived snapshot is a structured, reviewable mapping of:

- 86 canonical API 1.0 contract IDs → TEST-API references and their declared surfaces, auth profiles, permissions, actions, retries and idempotency classes;
- 47 canonical IPC-B/1 contract IDs → TEST-IPC references and their typed bounded primitives, operation IDs, reconciliation labels and declared inputs;
- 82 active permission IDs with their canonical declared surfaces.

The snapshot is derived mechanically from three exact owner-final Markdown exports at the reference main above. It does **not** invent endpoint routes, HTTP methods, approval criteria, error DTOs, database columns, session handling, test fixtures, runnable assertions or any privileged operation.

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
- They do not validate the final SC-M01 QA owner artifact under specifications/tests/; the historical candidate checks described below have a separate scope.
- Automated checks are E1, not independent E3 approval, and do not waive branch protections.

## Source provenance

The data file records the Git blob SHA and exact repository path of each of the three canonical owners' exports; it is pinned to the inspected main SHA. To change it, update the authoritative source through its owner first, review that change, regenerate the derived snapshot, run tests and commit the result with a documented delta. Do not silently rewrite authority.

## Historical requirements for the TESTS / QA owner (#7)

At the 2026-10-26 snapshot, these references were inputs for a *separate owner-approved* SC-M01 Test Baseline, including the A01..A20 acceptance checklist. The proposal files and their pending/NOT_EXECUTED assertions remain historical evidence. No PASS/FAIL is inferred from a contract ID or from this CI job.

## Historical gate snapshot — not the current MASTER state

SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
5 of 8 owner artifacts are published; Database #6, QA #7, Engineering #9 remain missing.

The preceding block records only the 2026-10-26 proposal-candidate state. It must not be used as the current gate declaration. In particular, the historical single-file checker must keep its original 5/8, zero specialized owner approvals and zero product executions; changing those historical assertions would fabricate provenance.

## Current final QA applicability boundary

At consolidated main `580e9ed58286304ade30142caf9415f4b87c0c28`, TESTS / QA has published the final v1.0 owner export:

`specifications/tests/WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md`

Git blob: `e0cdf924c5d7353624c069552f921f587cad8561`.

Reports date the historical scope with `evidence_as_of=2026-10-26`. The single-file report uses `historical_governance_owner_artifact_gate` and `historical_sc_m01`; it publishes no unqualified current gate field. The workflow continues to check current API/IPC/permission source consistency and historical QA corpus integrity only.

This publication supersedes the old QA-missing condition as a current statement. It does not rewrite the historical proposal files. The annex and single-file checkers validate only their historical proposal corpus, including its 199 reference records and 85 proposal rows. Their JSON and workflow labels explicitly identify that scope.

**Final QA v1.0 source-integrity, semantic validation, acceptance and product execution are outside these historical checkers.** The final path/blob above is a provenance reference, not an executed integrity result. This PR does not infer a current 8/8 decision, owner approval, MASTER GO or product PASS from the historical reports. Current authority must be read from the approved owner artifacts and MASTER decisions at the integration SHA.

The repository-tool regression suite is separate from the 199 WCP360 product tests. Product executions reported by the historical checks remain zero. E3 remains required by the review/merge policy; CODE-BASELINE remains NONE and SC-M02 remains NOT AUTHORIZED.
