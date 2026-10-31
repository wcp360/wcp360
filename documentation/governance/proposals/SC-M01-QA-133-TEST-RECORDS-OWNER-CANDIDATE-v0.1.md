# WCP360 — 133 source-derived QA records: acceptance-ready owner candidate

**Date:** 2026-10-27  
**Authority:** `WCP360 — TESTS / QA` (owner approval REQUIRED)  
**Status:** REVIEW CANDIDATE — NON-CANONICAL — BLOCKED / NOT EXECUTED  
**Baseline:** WCP360-BM-0001 FINAL; DOC v4.0.21.23; DESIGN V2.5.3.36.4; CODE NONE  
**Tracking:** issue #7 → MASTER #13 → Audit #41.

## Purpose

The existing owner QA candidate and A01–A20 signoff packet are detailed at *family* level. This contribution provides **a proposed structured test record for each of the 133 test references already named in the canonical API/IPC contracts**, with source-derived identities, logical request/response, permission/auth scope, and negative assertions.

**This does not introduce 133 new TEST IDs:** it references only the already published **86 TEST-API** and **47 TEST-IPC** IDs. The associated 82 active permissions remain distinct typed identifiers and are already covered by API source relationships. Any test requirement beyond these references must be accepted and assigned by QA.

Machine-readable review candidate:
`governance/proposals/SC-M01-QA-133-TEST-RECORDS-OWNER-CANDIDATE-v0.1.json`

Current input:
`governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json`
Git blob `e039c8d7842df2192b0ecb41abf653000d74d116`.

## Source fidelity verification — performed before publication

| Property | Observed |
|---|---|
| Existing canonical API references | 86 |
| Existing canonical IPC references | 47 |
| Distinct referenced test IDs | 133 / 133 |
| Active permission registry | 82 |
| Exact source contract/test/permission/action/primitive mismatch against input index | **0 detected** |
| QA owner approvals inferred | **0** |
| Actual product test executions | **0** |
| PASS / FAIL results created | **0 / 0** |
| Record status | Every record `BLOCKED / NOT_EXECUTED` |

This is a reference-data consistency check, not an execution of application tests or evidence that every future test assertion is correct.

## Record fields and owner-acceptance tasks

Each proposed API test record includes:

- `test_id`, `origin_contract_id`, `source_authority`;
- authentication `surface`, `permission_id`, `action_id`, resource and tenant scope;
- logical request and response schemas, existing error profile, timeout, retry, idempotency, audit/security/compatibility classification;
- *proposed* positive schema assertion and negative authorization/schema/idempotency assertions;
- explicit forbidden side effects and expected evidence envelope;
- `blocked_on` owner-decision and missing implementation list;
- `owner_acceptance = PENDING_TESTS_QA`, `actual_execution = null`.

Each proposed IPC test record includes:

- canonical `ipc_operation_id`, `action_id`, job type, target type, input schema, bounded primitive, timeout, idempotency, concurrency scope, reconciliation and `TEST-IPC` reference;
- *proposed* wrong-peer, malformed-frame/unknown-operation, wrong-tenant/revision, arbitrary-exec rejection, uncertainty/reconciliation negative cases;
- additional explicit `Security/API #91` dependency for the **12** parent/provisioning/OR origin bindings;
- no actual kernel peer, context, filesystem, daemon or privileged effect is simulated or certified.

### What QA must still define explicitly

The files **do not** fill the missing owner-normative exact HTTP route/method/status/DTO for H01–H10, exact DB table/transaction assertions (#6), final Security S01–S09 composition, control C03/C06/C07 protocol and context lifecycle, Engineering #9 toolchain/coverage quality gates, executable fixtures, actual expected side-effect results, or approved evidence retention defaults. Those dependencies are `BLOCKED`, not guessed.

QA must ratify/correct A01–A20 from its published decision packet; then for each test either:

1. accept the proposed inherited family assertions with an **explicit exact fixture / expected result / safe error / forbidden effect / audit evidence**;
2. amend to match the owner-approved contract and separately version any TEST-ID changes;
3. defer as out of first SC-M01 activated slice with recorded reason;
4. reject an assertion not supported by canonical source.

The older negative cases `N01–N26`, browser `TEST-SEC-WEB-001→040`, database/recovery and DevOps cases remain in the QA owner candidate and are **not replaced or made PASS by this 133-reference pack**. The 85 historical proposed scenario labels remain noncanonical.

## First canonical materialization path

Current project evidence does **not** prove a previously complete final SC-M01 QA body. The owner may therefore choose publication type **P2 — FIRST CANONICAL MATERIALIZATION** if ratifying a consolidated complete candidate. This proposal recommends the owner *review P2*, but cannot choose for the owner.

Final requirements:

```text
OWNER = TESTS / QA
PUBLICATION TYPE = P1 EXACT / P2 FIRST MATERIALIZATION / P3 NEW VERSION
QA A01–A20 DECISIONS = EXPLICIT
FIRST-CODE SC-M01 TEST REQUIREMENTS = COMPLETE
QA TEST RECORD SCHEMAS / INHERITANCE = OWNER RATIFIED
ALL REQUIREMENT-CRITICAL NEGATIVE EFFECTS = EXPRESS
MISSING DB / ENGINEERING / API SEMANTICS = BLOCKED OR OWNER DECIDED
ACTUAL EXECUTION STATES = PLANNED / READY / BLOCKED, NO FABRICATED PASS
VERSION IMPACT / CONTENT CHANGE = HONESTLY DECLARED
FINAL OWNER SOURCE PATH = specifications/tests/<owner-approved-filename>
EXACT OWNER APPROVAL + SHA = REQUIRED
```

**Only a self-contained final owner-approved contract**, not this derived 133-pack, can close issue #7. It must be independently cross-reviewed with Database/Security/API/Engineering and verified by MASTER before the 8/8 gate changes.

## Current outcome

```text
QA RECORD REVIEW CANDIDATES = 133
QA ACTUAL APPLICATION TEST EXECUTIONS = 0
QA FINAL OWNER ARTIFACT = MISSING
SC-M01 CANONICAL INPUTS = 5 / 8
SC-M01 = PAUSED — TARGETED EVIDENCE
CODE-BASELINE = NONE
```

Do not copy into `specifications/tests/` and mark FINAL without a real TESTS/QA owner decision and supporting provenance.
