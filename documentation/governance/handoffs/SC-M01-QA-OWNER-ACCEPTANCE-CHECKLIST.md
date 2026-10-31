# WCP360 — SC-M01 Test Baseline v1.0 — QA Owner Acceptance Checklist

**Status:** OWNER REVIEW AID — NON-CANONICAL  
**Owner:** WCP360 — TESTS / QA  
**Issue:** #7  
**Reference main:** 844c50299f6b117288b753ec1c919529f9b0dab8

This checklist is intentionally smaller than the QA owner candidate.

Its purpose is to tell TESTS / QA exactly what must be **accepted, amended or rejected** before publishing the final canonical SC-M01 Test Baseline v1.0.

It does not approve anything on behalf of QA.

## 1. Inputs already prepared

QA should review these current inputs together:

1. \`governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md\`
2. \`governance/handoffs/SC-M01-QA-CANONICAL-INPUT-PACK.md\`
3. \`governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.md\`
4. \`governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json\`
5. \`governance/SC-M01-SCOPE.md\`
6. accepted owner decisions under \`specifications/\`
7. current evidence/proposal notes only where they are explicitly marked non-normative.

## 2. Source coverage already mechanically verified

The derived coverage index proves, against exact source blobs:

    API contracts = 86
    unique TEST-API references = 86

    IPC-B operations = 47
    unique TEST-IPC references = 47

    ACTIVE permissions = 82
    permissions represented in API bindings = 82

    missing active permission = 0
    unknown permission in API = 0
    TEST suffix mismatch = 0

QA does **not** need to recount these manually unless the canonical source blobs change.

This is identifier/reference coverage, not executable coverage.

## 3. A01 — artifact identity

QA must ACCEPT or AMEND:

    OWNER = WCP360 — TESTS / QA
    TEST BASELINE VERSION = v1.0
    SCOPE = SC-M01 — FIRST IMPLEMENTATION FOUNDATION

Required final decision:

    FINAL ARTIFACT FILENAME =
    FINAL TARGET PATH = specifications/tests/<filename>
    CONTENT CHANGE =
    PROVENANCE =

Recommended filename remains only a candidate:

    WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md

QA may choose another exact name.

## 3A. A01-PROVENANCE — publication type / content-change honesty

Historical recovery has established an important difference between QA #7 and Database/Engineering #6/#9:

    TEST BASELINE v1.0 metadata = approved
    prior complete final QA owner body = no evidence found
    final canonical QA artifact = not previously materialized/confirmed

Therefore QA must explicitly choose one publication classification.

### Option P1 — retained prior final source exists

Use only if TESTS / QA can produce a previously approved complete v1.0 owner source with provenance.

    PUBLICATION TYPE = EXACT REPUBLICATION
    CONTENT CHANGE = NONE

The retained source must be identifiable. MASTER must not infer this option merely from old v1.0 metadata.

### Option P2 — current candidate is reviewed into the first complete canonical body

Use when QA is approving/amending the current owner candidate because no prior complete final body exists.

    PUBLICATION TYPE = FIRST CANONICAL MATERIALIZATION / OWNER APPROVAL
    CONTENT CHANGE = INITIAL CANONICAL CONTENT (or equivalent owner wording)
    VERSION IMPACT = QA OWNER DECISION

This is not a historical-content recovery and must not be described as byte-identical/content-neutral republication.

### Option P3 — owner determines another version is required

If QA concludes that the material decisions represented by the new complete body cannot legitimately remain v1.0, it must publish the version impact and route the baseline change through MASTER.

### Mandatory rule

The issue template's earlier `CONTENT CHANGE = NONE` wording is not evidence by itself.

QA must base CONTENT CHANGE on actual provenance.

This checklist does not choose P1/P2/P3 for QA.

## 4. A02 — lifecycle states

Candidate states:

    PLANNED
    READY
    BLOCKED
    PASS
    FAIL

QA must confirm or amend the semantics.

Minimum invariant to preserve:

- PASS and FAIL only arise from execution;
- READY means expected behavior and prerequisites are fully defined;
- BLOCKED names the exact missing dependency;
- PLANNED does not imply executable readiness;
- document presence never creates PASS.

QA should explicitly reject any hidden state such as “assumed pass”.

## 5. A03 — canonical test record schema

QA must accept/amend the required fields for a canonical test.

Candidate mandatory information:

    TEST ID
    TITLE
    OWNER REQUIREMENT / CONTRACT
    SOURCE FILE + VERSION + SECTION
    TEST LEVEL
    STATUS
    PRECONDITIONS
    FIXTURE / INPUT
    ACTION
    EXPECTED RESULT
    EXPECTED ERROR / OUTCOME
    FORBIDDEN SIDE EFFECTS
    AUTHORIZATION / SURFACE CONTEXT
    DB STATE ASSERTIONS
    AUDIT / EVENT ASSERTIONS
    OBSERVABILITY ASSERTIONS
    CLEANUP
    FAILURE INJECTION, if applicable
    DETERMINISM / CLOCK / RANDOM SEED, if applicable
    ISOLATION / PARALLELISM REQUIREMENTS, if applicable
    RERUN / ATTEMPT POLICY
    QUARANTINE / FLAKY CLASSIFICATION, if applicable
    EVIDENCE TO RETAIN
    EVIDENCE RETENTION CLASS
    DEPENDENCIES
    AUTOMATION STATUS

QA may define inheritance/family profiles to avoid duplication, but a consumer must still be able to determine the complete expected behavior.

## 6. A04 — execution evidence envelope

QA must accept/amend what constitutes reproducible PASS/FAIL evidence.

Candidate envelope:

    repository
    commit SHA
    branch / PR
    build artifact/version
    Test Baseline version
    DB model/migration version, where applicable
    OS image/version
    architecture
    runtime/toolchain versions
    config/test profile
    test command
    test runner/tool version
    selected test manifest/filter
    CI run / job ID
    shard / parallel worker identity, if applicable
    attempt number / rerun reason
    deterministic clock source / random seed, if applicable
    start/end timestamps
    result
    safe failure details
    evidence artifact digest/reference
    evidence retention policy/reference

Must remain forbidden:

- passwords;
- raw tokens;
- cookies;
- private keys;
- production data;
- sensitive payload dumps.

## 7. A05 — test-level taxonomy

Candidate levels:

    UNIT
    CONTRACT
    INTEGRATION
    SECURITY
    E2E
    RECOVERY
    INSTALL / BUILD

QA must confirm whether these are:
- canonical levels;
- tags/families;
- or should be renamed.

The important requirement is that the final artifact distinguishes pure component checks from real multi-component/security/recovery evidence.

## 8. A06 — API coverage rule

Already source-derived:

    86 API-WCP contracts
    86 unique TEST-API references

QA must decide the canonical rule for each TEST-API record.

Minimum assertions proposed per applicable contract:

- surface;
- authentication profile;
- permission/action;
- tenant/resource scope;
- request schema;
- unknown-field rejection;
- response projection;
- logical error profile;
- timeout;
- retry/idempotency;
- audit/security/compatibility.

HTTP transport assertions remain BLOCKED until their owner transport decision is final where not already defined.

QA must not invent routes or status codes.

## 9. A07 — IPC coverage rule

Already source-derived:

    47 IPC-WCP contracts
    47 unique TEST-IPC references

QA must confirm each canonical operation receives validation for applicable:

- trusted peer;
- protocol/op version;
- frame/schema;
- origin authorization binding;
- resource/target binding;
- exact bounded primitive;
- timeout/deadline;
- idempotency/replay;
- concurrency/revision;
- UNKNOWN_OUTCOME/reconciliation;
- audit durability;
- postcondition.

No generic-exec test is optional for the privileged boundary.

## 10. A08 — Permission coverage rule

Already source-derived:

    82 ACTIVE permission_id
    all 82 represented by API bindings

QA must decide whether coverage is measured:

- directly one test family per permission;
- transitively through API/action cases plus explicit authorization-engine cases;
- or a mixed strategy.

Regardless of structure, QA must preserve:

    permission_id != action_id
    permission_id != job_type
    permission_id != event_id
    permission_id != audit_event_id
    permission_id != ipc_operation_id
    permission_id != error_code

Lexical equality must not create authority.

## 11. A09 — N01–N26

QA should explicitly ACCEPT / AMEND / REJECT each existing negative-security case family.

Mandatory property:

Every case states:
- rejection component;
- expected safe error/outcome;
- forbidden external effect;
- audit/security evidence.

Particularly critical:

    N07 unknown IPC operation
    N08 arbitrary command/executable
    N09/N10 path escape/TOCTOU
    N11 arbitrary signal
    N12 arbitrary systemd unit
    N13 arbitrary package argv
    N15 lost ACK / UNKNOWN_OUTCOME
    N21 arbitrary SQL
    N25 destructive blind retry
    N26 privilege fallback

## 12. A10 — Browser Security WEB-001→040

QA must preserve or explicitly remap the existing 40-case family.

Current Security v1.0 already fixes many expected outcomes, including:

- USER/ADMIN separation;
- CSRF + exact Origin;
- session rotation/revocation;
- XSS/CSP/framing;
- SSRF;
- redirects;
- upload/download;
- secret/error/cache rules;
- MFA/privileged expiry;
- reseller non-escalation.

Topology-dependent cookie non-exposure remains owner-dependent.

QA must distinguish:

    browser stored cookie
    browser selected cookie
    ingress received cookie
    proxy forwarded cookie
    backend received cookie
    backend accepted credential

when evaluating AUDIT-WCP-0117.

## 13. A11 — Database test dependency

Exact physical DB assertions remain BLOCKED until #6 canonical materialization/republication.

QA must not turn recovered historical fragments into canonical fixtures automatically.

Once #6 is available, QA should map:

- PK/FK;
- composite tenant-aware FKs;
- UNIQUE/CHECK/nullability;
- tenant ownership immutability;
- delete semantics;
- revisions;
- idempotency;
- jobs/attempts;
- lease/fencing;
- outbox;
- audit;
- reconciliation;
- restore/backup/migration;
- required correctness indexes.

For now:

    DB semantic test design = PLANNED
    exact physical DB assertions = BLOCKED ON #6

## 14. A12 — Engineering dependency

Exact Engineering quality gates remain BLOCKED where they depend on #9.

QA must not invent:

- coverage thresholds;
- complexity thresholds;
- lint/static analyzer tools;
- required build command names;
- Definition of Done;
- required review count.

The final QA artifact should name #9 as a dependency rather than fabricating those values.

## 15. A13 — UNKNOWN_OUTCOME / recovery

Core semantics are already FINAL:

    timeout != definite failure
    retry != reconciliation
    rollback != compensation
    execution success != verified success

    execution may have occurred but cannot be proven
    → UNKNOWN_OUTCOME
    → reconciliation
    → no blind retry

QA must preserve failure injection around at least:

1. before durable intent;
2. after intent before dispatch;
3. during dispatch;
4. external effect before result persistence;
5. result persisted but response lost;
6. daemon/worker restart;
7. stale executor;
8. audit dependency unavailable;
9. DB unavailable after effect;
10. reconciliation restart.

Exact DB persistence assertions remain blocked on #6.

## 16. A14 — idempotency

Already FINAL at API semantic level:

    same key + same semantic request
    → same logical result

    same key + different semantic request
    → IDEMPOTENCY_CONFLICT

QA must cover:
- concurrent duplicate submission;
- new request_id does not bypass logical idempotency;
- cross-tenant isolation;
- retained result;
- post-dispatch uncertainty before destructive retry.

Exact durable-key representation/concurrency remains #6/API-owner dependent.

## 17. A15 — repository/bootstrap family

QA must decide which repository requirements belong in the canonical product baseline.

Candidate coverage:
- component/surface separation;
- no cancelled Sprint 0 or V4.1;
- dependency/lock files according to Engineering;
- no secrets;
- test entrypoints;
- clean-checkout CI;
- generated-artifact reproducibility;
- product root not runtime-writable.

Real PR #42 tooling tests may be referenced as evidence for those tools after accepted review/merge.

They must not be counted as API/IPC/application PASS results.

## 18. A16 — build/toolchain family

The final stack/toolchain is not yet fully owner-ratified.

QA should keep exact version/tool checks BLOCKED until Engineering/DevOps decisions exist.

No historical Sprint 0 toolchain may become expected behavior by default.

Candidate test themes:
- clean build;
- lock consistency;
- supported architecture;
- approved dependency sources;
- deterministic generated artifacts;
- static analysis according to #9;
- artifact digest/signature where authorized.

## 19. A17 — proposal-case intake

The historical owner-decision pack produced 85 local scenario labels.

They are **not** canonical TEST IDs.

QA must triage them by:

    REUSE existing TEST
    MERGE
    SPLIT
    DISCARD
    BLOCKED
    CREATE NEW canonical TEST ID

Only after the underlying owner behavior is accepted.

The final baseline should preserve provenance from any accepted case to the owner decision it validates.

## 20. A18 — partial selection / CI reporting

QA must define what a CI result means when only a subset executes.

Candidate required reporting:

    target SHA
    Test Baseline version
    selected test scope
    selected count
    executed count
    PASS
    FAIL
    BLOCKED, if planning data included
    skipped/not-selected reason
    environment
    evidence reference

A partial suite must never be rendered as “full baseline PASS”.

## 20A. Cross-cutting execution-integrity decision

QA should explicitly ACCEPT / AMEND these safeguards as part of A03/A04/A18/A19 rather than creating an implicit CI policy later.

Required questions:

- Are all execution attempts retained/referenced when a test is rerun?
- Is “rerun until PASS” forbidden as gate evidence?
- How are product failure, infrastructure failure and flaky/non-deterministic observation distinguished?
- Can a quarantined required test contribute to a full baseline PASS? Candidate answer: NO.
- What owner/reason/expiry metadata is required for quarantine?
- Are random seeds and deterministic clock inputs retained when they affect reproduction?
- How are parallel fixtures isolated by tenant/resource/DB/filesystem/socket identity?
- Which evidence must remain durably referenceable after CI artifacts expire?
- Which material changes invalidate or require evaluation of previous PASS evidence?

Minimum invariant:

    QUARANTINE != PASS
    RERUN != ERASE PRIOR FAILURE
    FLAKY != PRODUCT PASS
    TEMPORARY ARTIFACT EXPIRY != LOSS OF AUDITABLE RESULT
    NEW SHA != AUTOMATIC REUSE OF OLD PASS

These safeguards create no new lifecycle state unless TESTS / QA explicitly chooses one.

## 21. A19 — regression policy

QA must confirm:

    every corrected bug receives a regression test
    when executable verification is technically applicable

Cycle:

    REPRODUCE
    → DIAGNOSE
    → ROOT CAUSE
    → FIX
    → TEST
    → REGRESSION TEST
    → VERIFY
    → CLOSE

## 22. A20 — first CODE-BASELINE evidence

QA must define the minimum test evidence required before MASTER may establish the first CODE-BASELINE.

Candidate conditions:
- all applicable SC-M01 canonical tests selected;
- no unresolved critical FAIL;
- any BLOCKED item explicitly judged outside candidate scope or remains a blocker;
- final candidate SHA is the tested SHA;
- DB/Security/Engineering/DevOps review evidence for affected components;
- regression tests for corrected findings/bugs;
- evidence artifacts retained.

SC-M01 completion never authorizes SC-M02 automatically.

## 23. Owner decision form

For each A01–A20:

    QA DECISION ID =
    DECISION = ACCEPT / AMEND / REJECT / DEFER
    AMENDMENT =
    RATIONALE =
    DEPENDENCIES =
    CANONICAL SECTION =
    TEST ID IMPACT =
    STATUS =

After all non-deferred foundation decisions are resolved, QA can publish the final artifact.

## 24. Final publication block

QA should provide, in the final canonical artifact:

    OWNER = WCP360 — TESTS / QA
    TEST BASELINE VERSION = v1.0
    SCOPE = SC-M01 — FIRST IMPLEMENTATION FOUNDATION
    FINAL APPROVED CONTENT = YES
    HISTORICAL CONTENT INCLUDED = NO
    READY FOR SOURCE CODE CONSUMPTION = YES
    BLOCKER = NONE or exact explicit blocker
    CONTENT CHANGE =
    PROVENANCE =
    GITHUB COMMIT =

If blockers remain that prevent Source Code consumption, QA must not state READY/BLOCKER NONE.

## 25. Current status

    QA OWNER CANDIDATE = PRESENT
    CANONICAL 86/47/82 COVERAGE INDEX = PRESENT
    QA FINAL OWNER APPROVAL = MISSING
    CANONICAL specifications/tests ARTIFACT = MISSING
    PRODUCT PASS/FAIL = NONE
    ISSUE #7 = OPEN
    SC-M01 = PAUSED
    CODE-BASELINE = NONE

The remaining work is owner arbitration/publication, not another general QA draft.
