# WCP360 — SC-M01 Test Baseline v1.0 — Complete Single-File QA Owner Review Candidate

**Publication date:** 2026-10-28  
**Classification:** **P1 NON-CANONICAL / QA OWNER ACCEPTANCE REQUIRED — NOT FINAL**  
**Intended owner:** WCP360 — TESTS / QA  
**Issue:** #7; MASTER #13; independent Audit #41  
**Scope:** SC-M01 FIRST IMPLEMENTATION FOUNDATION only  
**Baseline:** WCP360-BM-0001 FINAL / DOC v4.0.21.23 / DESIGN V2.5.3.36.4 / CODE NONE  
**Publication provenance proposal:** **P2 FIRST CANONICAL MATERIALIZATION** (NOT selected by QA)  
**Final target:** `specifications/tests/<QA-owner-approved-name>.md` — unreserved until actual owner decision  

> **DO NOT COUNT THIS FILE IN 8/8.** QA has NOT ratified A01–A20, QI-01..07, the individual WEB number-to-scenario assignments, N expected error projections, 85 proposal labels or the publication provenance. There is NO historical complete prior final Test Baseline found. No actual WCP360 application tests ran. Every case here is PLANNED/BLOCKED and NOT_EXECUTED; no PASS/FAIL.

## 0. What this single file materializes

This self-contained review candidate combines **the verbatim complete 2026-10-03 QA baseline proposal structure**, **133 existing source-referenced TEST-API/TEST-IPC records**, **26 canonical N-label source texts and proposed negative expectations**, **40 proposed mappings over the existing TEST-SEC-WEB-001..040 range**, plus an **85-label proposal intake matrix**. It includes record schemas, test levels, lifecycle/evidence rules, negative/no-effect assertions, dependency blockers, original authority paths, and a final QA owner decision sheet.

**Important distinctions:** 133 existing TEST-IDs are only source references, not executed tests. N01–N26 are 26 existing *labels*, not approved new `TEST-SEC-...` IDs. The `TEST-SEC-WEB-001..040` range exists, but the mapping to specific browser scenarios is a **NEW PROPOSAL**, not previously approved. The 85 local proposal labels are not canonical tests and cannot be counted as separate test cases until QA triage. 199 = reference/case records, NOT a claim of runnable complete test coverage.

### Exact source provenance (Git blob SHA)

| Document | Git blob SHA | Role / authority |
|---|---|---|
| `governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md` | `fe2e93ade5541808ff0a0c718ef5c74fb0b2356b` | P1/G1 source/proposal (NOT FINAL) |
| `governance/proposals/SC-M01-QA-133-TEST-RECORDS-OWNER-CANDIDATE-v0.1.json` | `fd1c570c396cebc2b20b3c89c96ca9b033fc2dc1` | P1/G1 source/proposal (NOT FINAL) |
| `governance/proposals/SC-M01-QA-66-SECURITY-TEST-ANNEX-OWNER-CANDIDATE-v0.1.json` | `e56db7549d8b30ebffd3687265e92964bfd1acdf` | P1/G1 source/proposal (NOT FINAL) |
| `governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json` | `e039c8d7842df2192b0ecb41abf653000d74d116` | P1/G1 source/proposal (NOT FINAL) |
| `governance/proposals/SC-M01-QA-A01-A20-OWNER-DECISION-PACKET-v0.1.md` | `5d951d53410d4ab5ee1e922524f85f1479e60ec5` | P1/G1 source/proposal (NOT FINAL) |
| `specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | final owner API 1.0 | Owner-final logical API authority; no HTTP route invention |
| `specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md` | `fec9c74e8413db822662e47c76c40486e282334f` | Owner-final IPC-B/1 authority |
| `specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `6d74f2f363524511424f839ca7262143c2af95ab` | Owner-final Security v1.0 authority |

### Source-comparison prerequisites and current evidence

- Source coverage index contains 86 API contracts, 47 IPC contracts and 82 ACTIVE permission IDs. Full per-record source fields below are copied from the already checked P1 candidate JSON; this is not automatically the source-of-truth for semantics.
- For every case: final fixture execution is prohibited until owner-approved HTTP transport, physical DB and trusted security context are available for that activated slice.
- The owner may ratify this P2 first materialization only with **honest content-change classification**: `CONTENT CHANGE = INITIAL CANONICAL CONTENT` (or clearly owner-approved equivalent), not `CONTENT CHANGE = NONE` as if a prior final source was byte-identically recovered.
- A new version (P3) requires MASTER baseline change; original exact previously approved full document (P1) requires actual exact source evidence.

## PART I — Complete inherited QA owner candidate baseline (verbatim content, noncanonical)

The following source is preserved **verbatim** from the 2026-10-03 candidate for reviewer traceability. Its own metadata and owner-approval template remain candidate text, not proof of any accepted decision.

<!-- BEGIN VERBATIM QA OWNER CANDIDATE SOURCE; BLOB fe2e93ade5541808ff0a0c718ef5c74fb0b2356b -->
# WCP360 — SC-M01 Test Baseline v1.0 — QA Owner Candidate

**Status:** OWNER CANDIDATE — REVIEW REQUIRED — NOT CANONICAL  
**Prepared for:** WCP360 — TESTS / QA  
**Reference:** WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE  
**Candidate date:** 2026-10-03

This document is a complete candidate structure for the final SC-M01 Test Baseline v1.0.

It does not close issue #7.

Only TESTS / QA may review, amend, approve and publish the final canonical artifact under specifications/tests/.

No PASS or FAIL result is created by this document.

## 1. Proposed baseline metadata

The owner should ratify or amend:

    TEST BASELINE VERSION = v1.0
    SCOPE = SC-M01 — FIRST IMPLEMENTATION FOUNDATION
    OWNER = WCP360 — TESTS / QA
    CODE-BASELINE = NONE

Allowed test lifecycle states:

    PLANNED
    READY
    BLOCKED
    PASS
    FAIL

Rules:

- PASS requires actual execution against an exact candidate SHA and environment.
- FAIL requires actual execution and a reproducible failing assertion.
- READY means all non-implementation prerequisites are available.
- BLOCKED must identify the exact missing dependency.
- PLANNED means test intent exists but one or more prerequisites are not yet ready.
- No test result may be inferred from document presence, code review or owner approval alone.

## 2. Source authority

The final QA baseline must consume, not redefine:

- API Contract 1.0;
- IPC Contract WCP360-IPC-B/1;
- Permission Registry v1.0;
- Security Baseline v1.0;
- DevOps Baseline v1.0;
- Database Model v1.0 when issue #6 is materialized;
- Engineering Ruleset v0.2 when issue #9 is materialized;
- SC-M01 scope and current governance;
- owner decisions accepted from the merged proposal/recovery work.

Proposal documents are test-design input only until the relevant owner decision is canonical.

## 3. Mandatory test-record schema

Every canonical test case should provide, directly or by inherited family profile:

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
    ISOLATION NAMESPACE / PARALLELISM REQUIREMENTS, if applicable
    RERUN / ATTEMPT POLICY
    QUARANTINE / FLAKY CLASSIFICATION, if applicable
    EVIDENCE TO RETAIN
    EVIDENCE RETENTION CLASS
    DEPENDENCIES
    AUTOMATION STATUS

A title or identifier alone is not a test specification.

## 4. Execution evidence envelope

Every executed PASS/FAIL must be tied to:

    repository
    commit SHA
    branch / PR
    build artifact/version if applicable
    test baseline version
    database model/migration version if applicable
    OS image/version
    architecture
    runtime/toolchain versions
    configuration profile
    test command
    test runner/tool version
    selected test manifest/filter
    run ID / job ID
    shard / parallel worker identity, if applicable
    attempt number / rerun reason
    deterministic clock source / random seed, if applicable
    start/end timestamps
    result
    failure details if safe
    evidence artifact digest/reference
    evidence retention policy/reference

Sensitive values, session cookies, tokens, private keys, raw credentials and production data must never be placed in QA evidence.

## 5. Test-level taxonomy

Candidate levels:

### UNIT

Pure local component behavior:
- parsers;
- validators;
- type/namespace checks;
- state transitions;
- canonicalization;
- safe error mapping;
- deterministic helpers.

### CONTRACT

Boundary behavior without requiring full production topology:
- API request/response schemas;
- IPC framing/version/schema;
- action/permission bindings;
- DTO/error profiles;
- control-boundary messages;
- compatibility behavior.

### INTEGRATION

Multiple real components:
- backend + PostgreSQL;
- wcpd + control socket;
- wcpd + wcp-privd test fixture;
- transaction/outbox/recovery;
- session store + surface enforcement.

### SECURITY

Negative/adversarial tests:
- cross-surface;
- cross-tenant;
- authorization bypass;
- malformed/unknown operation;
- path/TOCTOU;
- generic execution attempts;
- secret leakage;
- stale/revoked context.

### E2E

Only when the corresponding SC-M01 flow exists:
- user/admin request through accepted HTTP boundary;
- durable enqueue/control dispatch;
- final observed state;
- audit/correlation evidence.

### RECOVERY

Failure/restart/uncertain-outcome tests:
- crash windows;
- lost acknowledgement;
- stale executor;
- DB/audit unavailability;
- restart reconciliation.

### INSTALL / BUILD

When DevOps/Engineering authorize the corresponding implementation:
- clean build;
- package contents;
- supported OS;
- identities/groups/filesystem/socket modes;
- install/upgrade/rollback/recovery.

## 6. Repository / bootstrap family

Candidate family label: QA-SC-M01-BOOTSTRAP.

Required assertions once SC-M01 is resumed:

- repository structure matches Engineering/SC-M01 scope;
- WCP-USER, WCP-ADMIN, backend, wcpd and wcp-privd remain separate components;
- no cancelled Sprint 0 source is reintroduced;
- no cancelled V4.1 executable-contract artifact is treated as runtime authority;
- dependency manifests and locks are present as required by Engineering/DevOps;
- safe configuration examples contain no secrets;
- unit/contract/integration/security test entry points exist;
- CI executes declared checks from a clean checkout;
- generated artifacts have a reproducible source;
- package-managed product root is not runtime-writable.

Engineering-specific exact thresholds remain BLOCKED on issue #9.

## 7. WCP-USER / WCP-ADMIN separation family

Candidate family label: QA-SC-M01-SURFACE.

Mandatory behaviors:

- USER session never authenticates ADMIN;
- ADMIN session never authenticates USER;
- wrong-surface cookie does not create authentication fallback;
- simultaneous USER/ADMIN credentials do not cause heuristic credential selection;
- exact Origin and CSRF rules hold per surface for browser mutation;
- session rotation/revocation/expiry is surface-correct;
- privileged/impersonation/break-glass context cannot leak across surfaces;
- surface identity is preserved across backend/control boundaries.

Topology-dependent cookie-delivery tests remain READY/BLOCKED until Security/DevOps ratify the deployment topology.

## 8. API contract family

The canonical API artifact currently references 86 TEST-API cases.

QA should not duplicate the API registry. Instead, every API contract row must map to a canonical TEST-API record validating:

- allowed surface;
- authentication profile;
- permission/action;
- tenant/resource scope;
- request schema and unknown-field rejection;
- response projection;
- safe errors;
- timeout;
- retry;
- idempotency;
- audit;
- security;
- compatibility.

Additional transport assertions are required after owner decisions for:
- HTTP method;
- route;
- parameter location;
- DTO shape;
- status code;
- CSRF/CORS;
- pagination;
- cache/no-store;
- operation discriminators.

Until these are owner-ratified, those transport assertions remain BLOCKED, not guessed.

## 9. IPC-B contract family

The current IPC artifact references 47 TEST-IPC cases.

Every operation must validate, as applicable:

- only wcpd is accepted as IPC-B peer;
- protocol version;
- operation version;
- frame size and strict JSON/schema;
- permission/action/context binding;
- target/resource binding;
- bounded operation selection;
- no generic fallback;
- timeout/deadline behavior;
- replay/idempotency behavior;
- concurrency/revision behavior;
- UNKNOWN_OUTCOME handling;
- reconciliation;
- audit durability;
- secret redaction;
- postcondition verification.

The exact 5-second I/O versus operation-timeout semantics remain BLOCKED until the API/IPC owner ratifies the pending timeout decision.

## 10. Control boundary family

Candidate family label: QA-SC-M01-CONTROL.

Recovered/current evidence supports preparation for:

- control socket peer rejection;
- wcp360-user / wcp360-admin transport access only where approved;
- worker/hosting denial by default;
- transport access != application authorization;
- wrong surface/context rejection;
- target/resource substitution rejection;
- unknown control operation has no generic fallback.

Tests requiring C03–C07 owner decisions remain BLOCKED until the control protocol/context/worker path is ratified.

## 11. Permission and authorization family

All 82 active permission_id values require coverage.

Core rules:

    permission_id != action_id
    permission_id != job_type
    permission_id != event_id
    permission_id != audit_event_id
    permission_id != ipc_operation_id
    permission_id != error_code

QA must prove lexical overlap never grants authority.

For the final authorization engine, tests must cover:

- explicit DENY;
- account override;
- role grant;
- plan/feature/entitlement;
- tenant boundary;
- ownership/delegation;
- resource state;
- auth strength/MFA;
- approval;
- temporary privilege;
- revocation between enqueue and effect.

Detailed precedence/override semantics remain BLOCKED until Security ratifies the open S05–S09 decisions.

## 12. Privileged negative catalog N01–N26

The final QA baseline must retain the existing 26-case negative catalog:

    N01 unauthorized Unix peer
    N02 wrong surface
    N03 bad permission/action binding
    N04 cross-Tenant target
    N05 invalid ownership/membership/delegation
    N06 malformed schema
    N07 unknown IPC operation
    N08 arbitrary command/executable
    N09 path traversal
    N10 symlink/mount/TOCTOU escape
    N11 arbitrary signal
    N12 arbitrary systemd unit
    N13 arbitrary package argv/tampered plan
    N14 duplicate/idempotency violation
    N15 timeout/lost ACK → UNKNOWN_OUTCOME
    N16 concurrent conflict
    N17 mandatory audit unavailable
    N18 reconciliation after uncertainty
    N19 secret echo
    N20 parameter boundary
    N21 arbitrary SQL
    N22 stale PID/PID reuse
    N23 unregistered service ID
    N24 invalid/stale resource ID
    N25 destructive blind retry
    N26 privilege downgrade/fallback

Each case must identify the exact component at which rejection occurs and prove no forbidden side effect.

## 13. Browser security TEST-SEC-WEB-001→040

The existing Security family remains READY at the design level.

Final QA records must cover:
- cross-surface cookie/session behavior;
- fixation;
- rotation;
- expiry;
- revocation;
- CSRF;
- Origin;
- CORS;
- XSS/CSP/framing;
- SSRF;
- redirects;
- upload/download;
- cache/error leakage;
- secret exposure;
- MFA/privileged expiry;
- reseller non-escalation;
- rate limiting versus authorization.

A browser test should distinguish:
- browser stored cookie;
- browser selected cookie;
- ingress observed cookie;
- backend received cookie;
- backend accepted credential.

## 14. Database physical family

Candidate family label: QA-SC-M01-DB.

This family is BLOCKED on the canonical DB Model #6 for exact physical assertions.

Once materialized, test at minimum:

- PK/FK;
- composite tenant-aware FK;
- UNIQUE/CHECK/nullability;
- ownership immutability;
- delete semantics;
- revision/stale-update rejection;
- idempotency uniqueness/fingerprint conflict;
- job attempt uniqueness;
- lease/fencing concurrency;
- outbox atomicity;
- audit occurrence identity/durability;
- reconciliation persistence;
- correctness indexes where contractually required;
- control-plane versus hosted DB boundary;
- secret-reference rather than secret-inline constraints.

Historical recovery evidence may help QA prepare fixtures but is not enough to mark this family READY for exact schema assertions.

## 15. Idempotency and duplicate-submission family

Required semantic tests:

- same key + same semantic request → same logical result;
- same key + different semantic request → IDEMPOTENCY_CONFLICT;
- concurrent same-key requests do not cause duplicate effect;
- request_id change does not bypass logical idempotency;
- cross-tenant key collision does not cross isolation;
- retained prior result is returned according to contract;
- expired/reused key behavior follows owner policy;
- post-dispatch uncertain result is reconciled before any destructive retry.

Physical assertions are BLOCKED on #6.

## 16. Failure / UNKNOWN_OUTCOME / recovery family

Mandatory scenarios include failure at these windows:

1. before durable intent;
2. after durable intent but before dispatch;
3. during dispatch;
4. external effect occurred before result persistence;
5. result persisted but client response lost;
6. worker/daemon restart;
7. stale executor resumes late;
8. mandatory audit unavailable;
9. DB unavailable after external effect;
10. reconciliation process crashes/restarts.

Assertions must distinguish:

    timeout != definite failure
    retry != reconciliation
    rollback != compensation
    execution success != verified success

No test may convert unknown state into success or failure without evidence.

## 17. Audit / outbox family

For pure control-plane mutations where mandatory audit/outbox is part of the contract:

- mutation and required durable evidence commit atomically;
- mandatory evidence failure causes DB mutation rollback.

For external effects:

    durable intent
    → bounded dispatch
    → observe
    → durable result/audit

When outcome is uncertain:

    UNKNOWN_OUTCOME
    → reconcile
    → durable final outcome

Audit payload must exclude passwords, raw tokens, private keys, sensitive cookies and credentials.

## 18. DevOps/platform family

Initial platform:

    Debian GNU/Linux 13 (trixie)
    amd64 / x86_64
    product root = /usr/lib/wcp360

Validate, when implementation exists:

- expected users/groups;
- no-login service identities;
- WCP-USER/WCP-ADMIN separation;
- wcpd non-root;
- wcp-privd minimal root service;
- worker has no unauthorized privileged group;
- IPC-B owner/group/mode and peer checks;
- runtime write denied to product root;
- protected config/secret paths;
- ephemeral /run state recreated correctly;
- health/readiness;
- failure on insecure permissions;
- uninstall does not destroy hosted tenant data.

Toolchain/runtime version assertions remain BLOCKED until Engineering/DevOps ratify F01–F10.

## 19. Build/toolchain family

Candidate family label: QA-SC-M01-BUILD.

Map the existing FQ01–FQ15 proposal cases after owner decisions.

Required outcome classes include:

- clean-checkout reproducibility;
- lock-file consistency;
- no floating release dependency;
- supported runtime/architecture;
- secret-free source/example/config;
- approved dependency source;
- cache-independent output;
- same-input reproducibility;
- frontend build-runtime separation if applicable;
- static-analysis failure blocks according to Engineering rule;
- generated-artifact drift detection;
- artifact digest/signature verification where authorized.

No numerical quality threshold is invented before Engineering #9.

## 20. Repository-guardrail evidence

PR #42 contains real repository-tool tests.

QA may reference those results as evidence for the repository tooling itself after review/merge, but must not count them as WCP360 application test results.

Current distinction:

    repository tool tests != SC-M01 product QA baseline
    E1 automated evidence != E3 independent review

## 21. Regression rule

Every corrected implementation bug must receive an executable regression test where technically applicable.

Bug closure cycle:

    REPRODUCE
    → DIAGNOSE
    → ROOT CAUSE
    → FIX
    → TEST
    → REGRESSION TEST
    → VERIFY
    → CLOSE

A review-only assertion is insufficient when executable verification is possible.

## 22. Proposal-case intake

The merged SC-M01 owner-decision pack contains 85 local PLANNED scenario labels:

    Authorization C01–C17 = 17
    Timeout/idempotency TQ01–TQ10 = 10
    Control boundary CQ01–CQ12 = 12
    API transport HQ01–HQ15 = 15
    Security Web/Authz SQ01–SQ16 = 16
    Toolchain/build FQ01–FQ15 = 15

These labels are not canonical TEST IDs.

QA owner workflow:

1. discard cases invalidated by owner decisions;
2. merge duplicates with existing TEST-API/TEST-IPC/Nxx/WEB cases;
3. split compound cases when one assertion cannot isolate the failure cause;
4. assign canonical IDs only after expected behavior is owner-approved;
5. mark dependency BLOCKED where #6/#9 or another owner decision is required;
6. preserve source traceability to the decision that created the obligation.

## 23. Test status transitions

Proposed owner rule:

    PLANNED → READY
    PLANNED → BLOCKED
    BLOCKED → READY
    READY → PASS
    READY → FAIL
    FAIL → READY after a fix only when rerun is pending

PASS and FAIL are execution results and must never be hand-edited to satisfy a gate.

If the implementation changes materially, prior PASS evidence must be evaluated for rerun applicability.

## 24. Required CI reporting

When product tests exist, CI should publish at least:

- exact SHA;
- test baseline version;
- total selected;
- READY/executed count;
- PASS count;
- FAIL count;
- BLOCKED count, when blocked tests are part of planning rather than execution;
- test artifact/reference;
- environment identity.

Do not expose secrets in logs/artifacts.

A partial test selection must identify its scope; it must not be displayed as a full baseline PASS.

## 24A. Execution integrity / flakiness / determinism

This section is a candidate owner rule set intended to prevent a future green CI result from hiding unstable or non-reproducible tests.

### Attempt history

A rerun must never erase the first failing attempt.

For every test execution that is rerun, QA should retain or reference:

    original attempt result
    rerun trigger
    attempt number
    exact SHA / artifact
    environment identity
    seed / clock source where applicable
    final disposition

A “rerun until PASS” loop is forbidden as acceptance evidence.

If a retry is performed only to diagnose infrastructure instability, the report must distinguish:

    PRODUCT TEST FAILURE
    TEST INFRASTRUCTURE FAILURE
    FLAKY / NON-DETERMINISTIC OBSERVATION
    OPERATOR / ENVIRONMENT ERROR

The exact labels are owner decisions, but failure classification must not silently convert a product failure into PASS.

### Flaky / quarantine behavior

Quarantine is metadata, not a PASS state.

A quarantined test:

- remains visible in the canonical inventory;
- retains its owner requirement and blocker;
- has an explicit quarantine reason, owner and review/expiry condition;
- cannot satisfy a required SC-M01 assertion while quarantined unless QA explicitly decides that assertion is outside the activated candidate scope;
- must not be hidden from CI totals.

QA should define whether an unstable required test is represented as BLOCKED, FAIL, or another explicit non-PASS disposition. No hidden “green because quarantined” behavior is allowed.

### Deterministic time and randomness

Tests depending on time, randomized input, UUID generation, backoff, expiry or scheduling should use an injectable/fixed source where technically practical.

When randomness/property testing is used:

- retain the seed for a failing attempt;
- make the failure reproducible from that seed where possible;
- record generator/tool version when it affects reproduction.

When wall-clock behavior is under test:

- distinguish monotonic duration from UTC/wall-clock assertions;
- record timezone/clock source where relevant;
- avoid relying on real elapsed sleeps when a deterministic clock can test the same contract.

### Isolation and parallel execution

Parallel execution must not create false PASS/FAIL through shared mutable fixtures.

Where tests may run concurrently, define:

- unique tenant/resource/test namespace;
- isolated DB/schema/transaction strategy as permitted by #6;
- unique filesystem/socket/service fixture names;
- cleanup ownership;
- collision behavior;
- whether a test is intentionally serial.

A PASS obtained only when the suite is forced serial must not be treated as evidence for a production concurrency property.

### Evidence retention

Temporary CI artifacts may expire. QA should define which evidence must remain durably referenceable for:

- first CODE-BASELINE;
- security/recovery failures;
- regression tests for corrected bugs;
- release/upgrade proofs where in scope.

A durable summary should retain hashes/IDs sufficient to identify the tested SHA, environment, selected tests and result after temporary artifacts expire, without copying secrets into Git.

### Material-change rerun rule

A prior PASS is not automatically valid for a new candidate SHA.

QA should define rerun triggers based on affected contracts/components/dependencies. At minimum, changes to:

- canonical owner contract consumed by a test;
- implementation under test;
- schema/migration;
- test code/fixture;
- runtime/toolchain;
- security boundary;
- dependency version relevant to the assertion

must cause QA to evaluate and record rerun applicability.

## 25. Gate to first CODE-BASELINE

A future CODE-BASELINE requires more than a green build.

QA evidence must show:

- applicable SC-M01 canonical tests executed;
- no unresolved critical FAIL;
- blocked tests are understood and not required for the candidate scope;
- Security/DB/DevOps/Engineering reviews for affected components;
- regression tests for corrected findings/bugs;
- final candidate SHA reviewed;
- MASTER verifies exit criteria.

SC-M01 completion does not authorize SC-M02.

## 26. Current dependency matrix

### Database #6

Blocks exact physical tests for:
- table/column/constraint/index assertions;
- idempotency persistence;
- job/lease/fencing persistence;
- transaction/outbox/audit durability details;
- reconciliation storage.

### Engineering #9

Blocks exact owner rules for:
- directory/layering quality;
- toolchain/static analysis;
- thresholds;
- review/merge/DoD;
- some build gates.

### Open owner semantics

May block affected tests:
- HTTP transport mapping;
- timeout/deadline phases;
- control protocol/context;
- web topology;
- authorization override/delegation composition;
- toolchain selections.

These do not justify inventing expected behavior.

## 27. Candidate final-publication checklist

Before TESTS / QA promotes a final artifact, verify:

- owner is TESTS / QA;
- version v1.0;
- scope is SC-M01 First Implementation Foundation;
- every canonical existing TEST-API and TEST-IPC reference is accounted for;
- 82 permissions covered appropriately;
- N01–N26 retained;
- TEST-SEC-WEB-001→040 retained/mapped;
- DB/Engineering blocked clauses identified rather than guessed;
- 85 proposal cases triaged;
- test-record schema defined;
- execution evidence schema defined;
- no fabricated PASS/FAIL;
- historic/candidate material clearly separated;
- artifact is self-contained and directly consumable by Source Code/CI once dependencies exist.

## 28. Owner publication decision

TESTS / QA must choose the final filename and publish under specifications/tests/.

Suggested candidate name only, not reserved:

    WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md

Owner should explicitly state:

    OWNER = WCP360 — TESTS / QA
    TEST BASELINE VERSION = v1.0
    SC-M01 TEST REQUIREMENTS = INCLUDED
    FINAL APPROVED CONTENT = YES
    HISTORICAL CONTENT INCLUDED = NO
    CONTENT CHANGE = <OWNER DECISION>
    READY FOR SOURCE CODE CONSUMPTION = YES
    BLOCKER = NONE or exact remaining blocker
    PROVENANCE = <OWNER SOURCE>
    COMMIT = <GITHUB SHA>

Until owner publication:

    THIS FILE = OWNER CANDIDATE ONLY
    ISSUE #7 = OPEN
    SC-M01 EVIDENCE = 5 / 8
    SOURCE CODE = PAUSED
    SC-M02 = NOT AUTHORIZED
    CODE-BASELINE = NONE


## Canonical coverage index — generated evidence

A derived source-coverage index is available at:

- `governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.md`
- `governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json`

At source main `4ff664a3157e662c174260e1fd35429738188f0c`, it verifies mechanically:

```text
API contracts = 86
unique TEST-API references = 86
IPC contracts = 47
unique TEST-IPC references = 47
active permissions = 82
permissions with API binding = 82
missing active permissions = 0
unknown API permissions = 0
TEST suffix mismatches = 0
```

This is derived evidence only. It does not define expected results, create new TEST IDs, execute tests, or close #7. If any source blob changes, the index must be regenerated/reverified.
<!-- END VERBATIM QA OWNER CANDIDATE SOURCE -->

## PART II — 86 canonical API test-ID *review records* (not executed)

The following 86 records preserve source typed relationships and contain **proposed** positive/negative assertions only. No transport route/status codes, actual fixtures or DB columns are manufactured. QA must accept/amend each record before final use.

### TEST-API-0101 — API-WCP-0101

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `applications.read` |
| action_id | `applications.read` |
| resource_type | `application` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,application_id?,cursor?,limit?}` |
| logical_response | `ApplicationView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0101` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0102 — API-WCP-0102

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `applications.create` |
| action_id | `applications.create` |
| resource_type | `application` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,name,environment,runtime_profile_id,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0102` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0103 — API-WCP-0103

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `applications.runtime.manage` |
| action_id | `applications.runtime.update` |
| resource_type | `application` |
| tenant_scope | `TS-T` |
| logical_request | `{application_id,runtime_profile_id,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0103` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0104 — API-WCP-0104

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `applications.deploy` |
| action_id | `applications.deploy` |
| resource_type | `application` |
| tenant_scope | `TS-T` |
| logical_request | `{application_id,release_id,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0104` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0105 — API-WCP-0105

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `applications.deploy` |
| action_id | `applications.rollback` |
| resource_type | `application` |
| tenant_scope | `TS-T` |
| logical_request | `{application_id,target_release_id,current_release_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0105` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0106 — API-WCP-0106

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `applications.maintenance` |
| action_id | `applications.maintenance.set` |
| resource_type | `application` |
| tenant_scope | `TS-T` |
| logical_request | `{application_id,enabled,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0106` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0201 — API-WCP-0201

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `domains.read` |
| action_id | `domains.read` |
| resource_type | `domain` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,domain_id?,cursor?,limit?}` |
| logical_response | `DomainView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0201` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0202 — API-WCP-0202

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `domains.create` |
| action_id | `domains.create` |
| resource_type | `domain` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,application_id?,fqdn_ascii,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0202` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0203 — API-WCP-0203

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `domains.write` |
| action_id | `domains.record.create` |
| resource_type | `dns_record` |
| tenant_scope | `TS-T` |
| logical_request | `{domain_id,type,name,ttl,values[],priority?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0203` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0001 — API-WCP-0001

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `domains.write` |
| action_id | `domains.record.update` |
| resource_type | `dns_record` |
| tenant_scope | `TS-T` |
| logical_request | `{dns_record_id,expected_revision,type,name,ttl,values[],priority?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0001` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0204 — API-WCP-0204

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `domains.write` |
| action_id | `domains.record.delete` |
| resource_type | `dns_record` |
| tenant_scope | `TS-T` |
| logical_request | `{dns_record_id,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0204` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0205 — API-WCP-0205

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `domains.dnssec.manage` |
| action_id | `domains.dnssec.set` |
| resource_type | `domain` |
| tenant_scope | `TS-T` |
| logical_request | `{domain_id,enabled,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0205` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0301 — API-WCP-0301

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `databases.read` |
| action_id | `databases.read` |
| resource_type | `database` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,database_id?,cursor?,limit?}` |
| logical_response | `DatabaseView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0301` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0302 — API-WCP-0302

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `databases.create` |
| action_id | `databases.create` |
| resource_type | `database` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,engine_profile_id,logical_name,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0302` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0303 — API-WCP-0303

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `databases.delete` |
| action_id | `databases.delete` |
| resource_type | `database` |
| tenant_scope | `TS-T` |
| logical_request | `{database_id,expected_revision,confirmation_token,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0303` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0304 — API-WCP-0304

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `databases.users.manage` |
| action_id | `databases.users.manage` |
| resource_type | `database_identity` |
| tenant_scope | `TS-T` |
| logical_request | `{database_id,operation,db_identity_id?,name?,expected_revision?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0304` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0305 — API-WCP-0305

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `databases.grants.manage` |
| action_id | `databases.grants.set` |
| resource_type | `grant_set` |
| tenant_scope | `TS-T` |
| logical_request | `{database_id,db_identity_id,privileges[],expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0305` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0306 — API-WCP-0306

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `databases.import` |
| action_id | `databases.import` |
| resource_type | `database` |
| tenant_scope | `TS-T` |
| logical_request | `{database_id,artifact_id,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0306` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0307 — API-WCP-0307

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `databases.export` |
| action_id | `databases.export` |
| resource_type | `database` |
| tenant_scope | `TS-T` |
| logical_request | `{database_id,format,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0307` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0308 — API-WCP-0308

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `databases.restore` |
| action_id | `databases.restore` |
| resource_type | `database` |
| tenant_scope | `TS-T` |
| logical_request | `{database_id,restore_point_id,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0308` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0401 — API-WCP-0401

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `email.read` |
| action_id | `email.read` |
| resource_type | `mail_config` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id}` |
| logical_response | `MailConfigView` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0401` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0402 — API-WCP-0402

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `email.manage` |
| action_id | `email.settings.update` |
| resource_type | `mail_config` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,default_quota_bytes,catchall_policy,expected_revision,idempotency_key}` |
| logical_response | `Mutation<MailConfigView>` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0402` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0403 — API-WCP-0403

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `email.domains.manage` |
| action_id | `email.domains.manage` |
| resource_type | `mail_domain` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,operation,domain_id,mail_domain_id?,expected_revision?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0403` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0404 — API-WCP-0404

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `email.mailboxes.manage` |
| action_id | `email.mailboxes.manage` |
| resource_type | `mailbox` |
| tenant_scope | `TS-T` |
| logical_request | `{mail_domain_id,operation,mailbox_id?,local_part?,quota_bytes?,expected_revision?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0404` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0405 — API-WCP-0405

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `email.aliases.manage` |
| action_id | `email.aliases.manage` |
| resource_type | `mail_alias` |
| tenant_scope | `TS-T` |
| logical_request | `{mail_domain_id,operation,alias_id?,source?,destinations[]?,expected_revision?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0405` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0406 — API-WCP-0406

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `email.auth.manage` |
| action_id | `email.auth.apply` |
| resource_type | `mail_domain` |
| tenant_scope | `TS-T` |
| logical_request | `{mail_domain_id,spf_policy,dkim_enabled,dmarc_policy,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0406` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0407 — API-WCP-0407

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `email.antispam.manage` |
| action_id | `email.antispam.update` |
| resource_type | `mail_config` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,profile_id,mode,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0407` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0501 — API-WCP-0501

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `files.read` |
| action_id | `files.read` |
| resource_type | `file` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,file_id?,parent_id?,cursor?,limit?}` |
| logical_response | `FileView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0501` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0502 — API-WCP-0502

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `files.write` |
| action_id | `files.write` |
| resource_type | `file` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,file_id?,parent_id?,logical_name?,content_object_id,expected_revision?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0502` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0503 — API-WCP-0503

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `files.delete` |
| action_id | `files.delete` |
| resource_type | `file` |
| tenant_scope | `TS-T` |
| logical_request | `{file_id,expected_revision,deletion_mode,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0503` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0504 — API-WCP-0504

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `files.permissions.manage` |
| action_id | `files.permissions.update` |
| resource_type | `file` |
| tenant_scope | `TS-T` |
| logical_request | `{file_id,permission_profile_id,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0504` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0505 — API-WCP-0505

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `files.access.manage` |
| action_id | `files.access.manage` |
| resource_type | `file_access` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,operation,credential_ref?,expected_revision?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU3` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0505` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0601 — API-WCP-0601

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `backups.read` |
| action_id | `backups.read` |
| resource_type | `backup` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,backup_id?,resource_type?,resource_id?,cursor?,limit?}` |
| logical_response | `BackupView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0601` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0602 — API-WCP-0602

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `backups.create` |
| action_id | `backups.create` |
| resource_type | `backup` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,resource_type,resource_id,backup_policy_id?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0602` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0603 — API-WCP-0603

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `backups.policy.manage` |
| action_id | `backups.policy.update` |
| resource_type | `backup_policy` |
| tenant_scope | `TS-T` |
| logical_request | `{tenant_id,policy_id,schedule,retention_class,resource_selector,expected_revision,idempotency_key}` |
| logical_response | `Mutation` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0603` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0604 — API-WCP-0604

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `backups.restore` |
| action_id | `backups.restore` |
| resource_type | `backup` |
| tenant_scope | `TS-T` |
| logical_request | `{backup_id,target_resource_id,restore_mode,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0604` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0605 — API-WCP-0605

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `backups.delete` |
| action_id | `backups.delete` |
| resource_type | `backup` |
| tenant_scope | `TS-T` |
| logical_request | `{backup_id,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0605` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0701 — API-WCP-0701

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `security.sessions.manage` |
| action_id | `security.sessions.manage` |
| resource_type | `session` |
| tenant_scope | `TS-S` |
| logical_request | `{operation,session_id?}` |
| logical_response | `SessionView/Collection/Mutation` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU3` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0701` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0702 — API-WCP-0702

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `security.keys.manage` |
| action_id | `security.keys.manage` |
| resource_type | `key` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,key_id?,public_key?,label?,idempotency_key?}` |
| logical_response | `KeyView/Collection` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU3` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0702` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0703 — API-WCP-0703

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `security.tokens.manage` |
| action_id | `security.tokens.manage` |
| resource_type | `security_token` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,token_class,token_id?,expires_at?,idempotency_key?}` |
| logical_response | `SecurityTokenView/SecretCreated` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU3` |
| security | `S-S` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0703` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0704 — API-WCP-0704

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `security.network.manage` |
| action_id | `security.network.update` |
| resource_type | `network_policy` |
| tenant_scope | `TS-B` |
| logical_request | `{scope_ref,policy_id,rules[],expected_revision,idempotency_key}` |
| logical_response | `Mutation` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0704` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0705 — API-WCP-0705

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `security.waf.manage` |
| action_id | `security.waf.update` |
| resource_type | `waf_policy` |
| tenant_scope | `TS-T` |
| logical_request | `{application_id,policy_id,ruleset_ref,mode,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0705` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0706 — API-WCP-0706

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `POLICY` |
| auth | `AUTH-P` |
| permission_id | `privilege.elevate` |
| action_id | `privilege.elevate` |
| resource_type | `privileged_session` |
| tenant_scope | `TS-P` |
| logical_request | `{requested_scope,reason,expires_in_seconds,idempotency_key}` |
| logical_response | `PrivilegedSessionView` |
| error_profile | `E-P` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU5` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0706` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0707 — API-WCP-0707

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `impersonation.start` |
| action_id | `impersonation.start` |
| resource_type | `impersonation_session` |
| tenant_scope | `TS-P` |
| logical_request | `{target_principal_id,tenant_id,reason,case_reference,expires_in_seconds,idempotency_key}` |
| logical_response | `ImpersonationSessionView` |
| error_profile | `E-P` |
| timeout | `15s` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU5` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0707` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0708 — API-WCP-0708

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `EMERGENCY` |
| auth | `AUTH-E` |
| permission_id | `break_glass.use` |
| action_id | `break_glass.start` |
| resource_type | `break_glass_session` |
| tenant_scope | `TS-P` |
| logical_request | `{reason,incident_reference,requested_scope,idempotency_key}` |
| logical_response | `BreakGlassSessionView` |
| error_profile | `E-P` |
| timeout | `15s` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU5` |
| security | `S-E` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0708` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0709 — API-WCP-0709

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `api.tokens.manage` |
| action_id | `api.tokens.manage` |
| resource_type | `api_token` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,token_id?,scopes[]?,resource_restrictions[]?,expires_at?,idempotency_key?}` |
| logical_response | `ApiTokenView/Collection/SecretCreated` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU3` |
| security | `S-S` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0709` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0801 — API-WCP-0801

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `servers.read` |
| action_id | `servers.read` |
| resource_type | `server` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id?,cursor?,limit?}` |
| logical_response | `ServerView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-A` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0801` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0802 — API-WCP-0802

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `servers.services.manage` |
| action_id | `servers.service.reload` |
| resource_type | `service` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,service_id,expected_config_revision?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0802` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0803 — API-WCP-0803

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `servers.services.manage` |
| action_id | `servers.service.restart` |
| resource_type | `service` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,service_id,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0803` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0002 — API-WCP-0002

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `servers.reboot` |
| action_id | `servers.reboot` |
| resource_type | `server` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,impact_acknowledged,approval_id?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0002` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0804 — API-WCP-0804

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `updates.read` |
| action_id | `updates.read` |
| resource_type | `update_plan` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,update_plan_id?}` |
| logical_response | `UpdateView` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-A` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0804` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0805 — API-WCP-0805

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `updates.apply` |
| action_id | `updates.apply` |
| resource_type | `update_plan` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,package_plan_id,approval_id?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0805` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0806 — API-WCP-0806

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `clusters.read` |
| action_id | `clusters.read` |
| resource_type | `cluster` |
| tenant_scope | `TS-A` |
| logical_request | `{cluster_id?,cursor?,limit?}` |
| logical_response | `ClusterView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-A` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0806` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0807 — API-WCP-0807

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `clusters.failover` |
| action_id | `clusters.failover` |
| resource_type | `cluster` |
| tenant_scope | `TS-A` |
| logical_request | `{cluster_id,target_node_id,reason,approval_id?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0807` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0808 — API-WCP-0808

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `network.read` |
| action_id | `network.read` |
| resource_type | `network` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id}` |
| logical_response | `NetworkView` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-A` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0808` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0809 — API-WCP-0809

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `network.firewall.manage` |
| action_id | `network.firewall.apply` |
| resource_type | `firewall_policy` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,policy_revision_id,expected_current_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0809` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0810 — API-WCP-0810

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `network.routes.manage` |
| action_id | `network.routes.update` |
| resource_type | `route_policy` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,route_policy_revision_id,expected_current_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0810` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0811 — API-WCP-0811

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `storage.read` |
| action_id | `storage.read` |
| resource_type | `storage` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,storage_id?}` |
| logical_response | `StorageView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-A` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0811` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0812 — API-WCP-0812

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `storage.expand` |
| action_id | `storage.expand` |
| resource_type | `storage` |
| tenant_scope | `TS-A` |
| logical_request | `{storage_id,target_size_bytes,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0812` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0813 — API-WCP-0813

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `processes.read` |
| action_id | `processes.read` |
| resource_type | `process` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,service_id?,cursor?,limit?}` |
| logical_response | `ProcessView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-A` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0813` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0003 — API-WCP-0003

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `processes.terminate` |
| action_id | `processes.terminate` |
| resource_type | `process` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,process_instance_id,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0003` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0007 — API-WCP-0007

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `processes.kill` |
| action_id | `processes.kill` |
| resource_type | `process` |
| tenant_scope | `TS-A` |
| logical_request | `{server_id,process_instance_id,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0007` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0814 — API-WCP-0814

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `settings.system.manage` |
| action_id | `settings.system.update` |
| resource_type | `system_settings` |
| tenant_scope | `TS-A` |
| logical_request | `{settings_patch[],expected_revision,idempotency_key}` |
| logical_response | `Mutation<SystemSettingsView>` |
| error_profile | `E-P` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0814` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0815 — API-WCP-0815

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `service_config.read` |
| action_id | `service_config.read` |
| resource_type | `service_config` |
| tenant_scope | `TS-A` |
| logical_request | `{service_id}` |
| logical_response | `ServiceConfigView` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-A` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0815` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0816 — API-WCP-0816

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-PRIV` |
| permission_id | `service_config.write` |
| action_id | `service_config.apply` |
| resource_type | `service_config` |
| tenant_scope | `TS-A` |
| logical_request | `{service_id,schema_version,config_document,expected_revision,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-P` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-X` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0816` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0901 — API-WCP-0901

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `iam.users.manage` |
| action_id | `iam.users.manage` |
| resource_type | `principal` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,principal_id?,account_id?,profile?,expected_revision?,idempotency_key?}` |
| logical_response | `IAMUserView/Collection` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0901` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0902 — API-WCP-0902

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `iam.roles.manage` |
| action_id | `iam.roles.manage` |
| resource_type | `role` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,role_id?,scope?,permission_ids[]?,expected_revision?,idempotency_key?}` |
| logical_response | `RoleView/Collection` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0902` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0903 — API-WCP-0903

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `iam.plans.manage` |
| action_id | `iam.plans.manage` |
| resource_type | `plan` |
| tenant_scope | `TS-A` |
| logical_request | `{operation,plan_id?,feature_ids[]?,quota_profile_id?,expected_revision?,idempotency_key?}` |
| logical_response | `PlanView/Collection` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0903` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0904 — API-WCP-0904

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `iam.features.manage` |
| action_id | `iam.features.manage` |
| resource_type | `feature_set` |
| tenant_scope | `TS-A` |
| logical_request | `{feature_set_id,feature_ids[],expected_revision,idempotency_key}` |
| logical_response | `Mutation<FeatureSetView>` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0904` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0905 — API-WCP-0905

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `iam.resellers.manage` |
| action_id | `iam.resellers.manage` |
| resource_type | `delegation` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,delegation_id?,reseller_account_id,tenant_ids[],permission_ceiling[],expires_at?,expected_revision?,idempotency_key}` |
| logical_response | `Mutation<ResellerView>` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0905` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0906 — API-WCP-0906

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-ADMIN` |
| auth | `AUTH-A` |
| permission_id | `iam.account_overrides.manage` |
| action_id | `iam.account_overrides.manage` |
| resource_type | `account_override` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,account_id,key,value?,expected_revision?,idempotency_key}` |
| logical_response | `Mutation<AccountOverrideView>` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0906` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0004 — API-WCP-0004

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `jobs.read` |
| action_id | `jobs.read` |
| resource_type | `job` |
| tenant_scope | `TS-B` |
| logical_request | `{job_id?,resource_type?,resource_id?,state?,cursor?,limit?}` |
| logical_response | `JobView/Collection` |
| error_profile | `E-J` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-Q` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0004` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0005 — API-WCP-0005

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `jobs.cancel` |
| action_id | `jobs.cancel` |
| resource_type | `job` |
| tenant_scope | `TS-B` |
| logical_request | `{job_id,expected_state,idempotency_key}` |
| logical_response | `Mutation<JobView>` |
| error_profile | `E-J` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0005` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1001 — API-WCP-1001

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `webhooks.manage` |
| action_id | `webhooks.manage` |
| resource_type | `webhook` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,webhook_id?,owner_scope,event_filters[]?,endpoint_ref?,expected_revision?,idempotency_key?}` |
| logical_response | `WebhookView/Collection` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1001` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1002 — API-WCP-1002

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `automations.manage` |
| action_id | `automations.manage` |
| resource_type | `automation` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,automation_id?,owner_scope,trigger_type?,action_id?,action_payload_ref?,expected_revision?,idempotency_key?}` |
| logical_response | `AutomationView/Collection` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1002` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1003 — API-WCP-1003

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `notifications.manage` |
| action_id | `notifications.manage` |
| resource_type | `notification_route` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,route_id?,owner_scope,channels[]?,filters[]?,expected_revision?,idempotency_key?}` |
| logical_response | `NotificationRouteView/Collection` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1003` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1004 — API-WCP-1004

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `POLICY` |
| auth | `AUTH-P` |
| permission_id | `approvals.request` |
| action_id | `approvals.request` |
| resource_type | `approval` |
| tenant_scope | `TS-P` |
| logical_request | `{action_id,resource_type,resource_id,reason,expires_at?,idempotency_key}` |
| logical_response | `Mutation<ApprovalView>` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1004` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1005 — API-WCP-1005

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `POLICY` |
| auth | `AUTH-P` |
| permission_id | `approvals.decide` |
| action_id | `approvals.decide` |
| resource_type | `approval` |
| tenant_scope | `TS-P` |
| logical_request | `{approval_id,decision,comment?,expected_revision,idempotency_key}` |
| logical_response | `Mutation<ApprovalView>` |
| error_profile | `E-P` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1005` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1006 — API-WCP-1006

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `monitoring.read` |
| action_id | `monitoring.read` |
| resource_type | `monitoring` |
| tenant_scope | `TS-B` |
| logical_request | `{scope_type,scope_id,window,metric_family?}` |
| logical_response | `MonitoringView` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-Q` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1006` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1007 — API-WCP-1007

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `incidents.manage` |
| action_id | `incidents.manage` |
| resource_type | `incident` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,incident_id?,scope_ref?,comment?,expected_revision?,idempotency_key?}` |
| logical_response | `IncidentView/Collection` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key when mutating` |
| audit_class | `AU1` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1007` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1008 — API-WCP-1008

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `ssl.read` |
| action_id | `ssl.read` |
| resource_type | `certificate` |
| tenant_scope | `TS-B` |
| logical_request | `{scope_type,scope_id,certificate_id?}` |
| logical_response | `CertificateView/Collection` |
| error_profile | `E-R` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU0` |
| security | `S-Q` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1008` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1009 — API-WCP-1009

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `ssl.manage` |
| action_id | `ssl.manage` |
| resource_type | `certificate` |
| tenant_scope | `TS-B` |
| logical_request | `{operation,scope_ref,certificate_id?,subject_names[]?,target_service_ids[]?,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RN` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1009` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1010 — API-WCP-1010

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `BOTH` |
| auth | `AUTH-B` |
| permission_id | `support.bundle.create` |
| action_id | `support.bundle.create` |
| resource_type | `support_bundle` |
| tenant_scope | `TS-B` |
| logical_request | `{scope_ref,redaction_profile_id,consent_reference,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-M` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU2` |
| security | `S-P` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1010` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0006 — API-WCP-0006

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `POLICY` |
| auth | `AUTH-P` |
| permission_id | `audit.read` |
| action_id | `audit.read` |
| resource_type | `audit_record` |
| tenant_scope | `TS-P` |
| logical_request | `{scope_ref,audit_record_id?,audit_event_id?,actor_id?,resource_type?,resource_id?,from?,to?,cursor?,limit?}` |
| logical_response | `AuditRecordView/Collection` |
| error_profile | `E-AUD` |
| timeout | `10s` |
| retry | `RR` |
| idempotency | `none` |
| audit_class | `AU4` |
| security | `S-Q` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0006` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0008 — API-WCP-0008

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `POLICY` |
| auth | `AUTH-P` |
| permission_id | `audit.export` |
| action_id | `audit.export` |
| resource_type | `audit_export` |
| tenant_scope | `TS-P` |
| logical_request | `{scope_ref,filters,format,idempotency_key}` |
| logical_response | `JobAccepted` |
| error_profile | `E-AUD` |
| timeout | `10s accept` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU4` |
| security | `S-Q` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-0008` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1011 — API-WCP-1011

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** API Contract 1.0.

| Source field | Exact source-derived value |
|---|---|
| surface | `WCP-USER` |
| auth | `AUTH-U` |
| permission_id | `settings.profile.manage` |
| action_id | `settings.profile.update` |
| resource_type | `profile` |
| tenant_scope | `TS-S` |
| logical_request | `{locale?,timezone?,notification_defaults?,expected_revision,idempotency_key}` |
| logical_response | `Mutation<ProfileView>` |
| error_profile | `E-M` |
| timeout | `15s` |
| retry | `RK` |
| idempotency | `key-required` |
| audit_class | `AU1` |
| security | `S-T` |
| compatibility | `C1/D0` |
| `origin_contract_id` | `API-WCP-1011` |

**Proposed expected / negative scenarios (not accepted fixtures):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Pending owner / test blockers:**
- TESTS / QA #7 — owner-approved expected-result and assertion schema
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** BLOCKED — no accepted HTTP transport route, no SC-M01 implementation.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

## PART III — 47 canonical IPC test-ID *review records* (not executed)

IPC-B/1 already defines exact 47 `ipc_operation_id` names, the paired action, the source field-name schema and no generic primitive. The per-field **value types** and real authorization/reconciliation are NOT validated in these P1 proposals. Each listed scenario is not a final accepted TEST-IPC case.

### TEST-IPC-1001 — IPC-WCP-1001

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `linux_user.create` |
| job_type | `linux_user.create` |
| ipc_operation_id | `linux_user.create` |
| resource_identifier | `hosting_account` |
| validated_input_schema | `{hosting_account_id,unix_identity_id,uid_gid_policy_id,home_root_id,shell_policy_id}` |
| bounded_primitive | `identity-adapter.create_registered_user` |
| timeout | `60s` |
| idempotency | `key-required` |
| concurrency_scope | `hosting_account` |
| reconciliation | `inspect/reconcile identity registry↔OS` |
| `origin_contract_id` | `IPC-WCP-1001` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1002 — IPC-WCP-1002

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `linux_user.lock` |
| job_type | `linux_user.lock` |
| ipc_operation_id | `linux_user.lock` |
| resource_identifier | `unix_identity` |
| validated_input_schema | `{unix_identity_id,expected_revision}` |
| bounded_primitive | `identity-adapter.lock_registered_user` |
| timeout | `15s` |
| idempotency | `replay-safe` |
| concurrency_scope | `unix_identity` |
| reconciliation | `observe lock state` |
| `origin_contract_id` | `IPC-WCP-1002` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1003 — IPC-WCP-1003

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `linux_user.unlock` |
| job_type | `linux_user.unlock` |
| ipc_operation_id | `linux_user.unlock` |
| resource_identifier | `unix_identity` |
| validated_input_schema | `{unix_identity_id,expected_revision}` |
| bounded_primitive | `identity-adapter.unlock_registered_user` |
| timeout | `15s` |
| idempotency | `replay-safe` |
| concurrency_scope | `unix_identity` |
| reconciliation | `observe lock state` |
| `origin_contract_id` | `IPC-WCP-1003` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1004 — IPC-WCP-1004

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `linux_user.delete` |
| job_type | `linux_user.delete` |
| ipc_operation_id | `linux_user.delete` |
| resource_identifier | `unix_identity` |
| validated_input_schema | `{unix_identity_id,deletion_mode,expected_revision}` |
| bounded_primitive | `identity-adapter.delete_registered_user` |
| timeout | `120s` |
| idempotency | `key-protected/no blind retry` |
| concurrency_scope | `unix_identity` |
| reconciliation | `inspect user/home state` |
| `origin_contract_id` | `IPC-WCP-1004` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1005 — IPC-WCP-1005

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `filesystem.directory.create` |
| job_type | `filesystem.directory.create` |
| ipc_operation_id | `filesystem.directory.create` |
| resource_identifier | `managed_path_ref` |
| validated_input_schema | `{managed_path_ref,permission_profile_id}` |
| bounded_primitive | `fs-adapter.mkdir_managed_ref` |
| timeout | `10s` |
| idempotency | `replay-safe` |
| concurrency_scope | `managed_path_ref` |
| reconciliation | `observe existence/profile` |
| `origin_contract_id` | `IPC-WCP-1005` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1006 — IPC-WCP-1006

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `filesystem.ownership.apply` |
| job_type | `filesystem.ownership.apply` |
| ipc_operation_id | `filesystem.ownership.apply` |
| resource_identifier | `managed_path_ref` |
| validated_input_schema | `{managed_path_ref,ownership_profile_id,expected_revision}` |
| bounded_primitive | `fs-adapter.apply_ownership_profile` |
| timeout | `30s` |
| idempotency | `revision-protected` |
| concurrency_scope | `managed_path_ref` |
| reconciliation | `observe owner/group` |
| `origin_contract_id` | `IPC-WCP-1006` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1007 — IPC-WCP-1007

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `filesystem.permissions.apply` |
| job_type | `filesystem.permissions.apply` |
| ipc_operation_id | `filesystem.permissions.apply` |
| resource_identifier | `managed_path_ref` |
| validated_input_schema | `{managed_path_ref,permission_profile_id,expected_revision}` |
| bounded_primitive | `fs-adapter.apply_permission_profile` |
| timeout | `30s` |
| idempotency | `revision-protected` |
| concurrency_scope | `managed_path_ref` |
| reconciliation | `observe permission profile` |
| `origin_contract_id` | `IPC-WCP-1007` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1008 — IPC-WCP-1008

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `filesystem.quota.apply` |
| job_type | `filesystem.quota.apply` |
| ipc_operation_id | `filesystem.quota.apply` |
| resource_identifier | `hosting_account` |
| validated_input_schema | `{hosting_account_id,quota_profile_id,expected_revision}` |
| bounded_primitive | `quota-adapter.apply_registered_profile` |
| timeout | `30s` |
| idempotency | `desired-value protected` |
| concurrency_scope | `hosting_account` |
| reconciliation | `observe quota` |
| `origin_contract_id` | `IPC-WCP-1008` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1009 — IPC-WCP-1009

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `nginx_vhost.validate` |
| job_type | `N/A` |
| ipc_operation_id | `nginx_vhost.validate` |
| resource_identifier | `application_revision` |
| validated_input_schema | `{application_id,vhost_revision_id}` |
| bounded_primitive | `nginx-adapter.validate_generated_revision` |
| timeout | `15s` |
| idempotency | `replay-safe` |
| concurrency_scope | `application/revision` |
| reconciliation | `validation result` |
| `origin_contract_id` | `IPC-WCP-1009` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1010 — IPC-WCP-1010

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `nginx_vhost.apply` |
| job_type | `nginx_vhost.apply` |
| ipc_operation_id | `nginx_vhost.apply` |
| resource_identifier | `application_revision` |
| validated_input_schema | `{application_id,vhost_revision_id,expected_active_revision}` |
| bounded_primitive | `nginx-adapter.atomic_apply_revision` |
| timeout | `30s` |
| idempotency | `revision-protected` |
| concurrency_scope | `application-vhost` |
| reconciliation | `compare active revision/rollback previous revision` |
| `origin_contract_id` | `IPC-WCP-1010` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1011 — IPC-WCP-1011

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `nginx_vhost.remove` |
| job_type | `nginx_vhost.remove` |
| ipc_operation_id | `nginx_vhost.remove` |
| resource_identifier | `application_revision` |
| validated_input_schema | `{application_id,vhost_revision_id,expected_active_revision}` |
| bounded_primitive | `nginx-adapter.remove_managed_vhost` |
| timeout | `30s` |
| idempotency | `key-protected` |
| concurrency_scope | `application-vhost` |
| reconciliation | `inspect managed target` |
| `origin_contract_id` | `IPC-WCP-1011` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1012 — IPC-WCP-1012

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `nginx.reload` |
| job_type | `nginx.reload` |
| ipc_operation_id | `nginx.reload` |
| resource_identifier | `service` |
| validated_input_schema | `{service_id,vhost_revision_id}` |
| bounded_primitive | `service-adapter.reload_registered_service` |
| timeout | `30s` |
| idempotency | `replay-safe same revision` |
| concurrency_scope | `service` |
| reconciliation | `verify service health/revision` |
| `origin_contract_id` | `IPC-WCP-1012` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1013 — IPC-WCP-1013

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `php_pool.validate` |
| job_type | `N/A` |
| ipc_operation_id | `php_pool.validate` |
| resource_identifier | `application_revision` |
| validated_input_schema | `{application_id,pool_revision_id,runtime_profile_id}` |
| bounded_primitive | `php-adapter.validate_pool_revision` |
| timeout | `15s` |
| idempotency | `replay-safe` |
| concurrency_scope | `application/revision` |
| reconciliation | `validation result` |
| `origin_contract_id` | `IPC-WCP-1013` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1014 — IPC-WCP-1014

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `php_pool.apply` |
| job_type | `php_pool.apply` |
| ipc_operation_id | `php_pool.apply` |
| resource_identifier | `application_revision` |
| validated_input_schema | `{application_id,pool_revision_id,runtime_profile_id,expected_active_revision}` |
| bounded_primitive | `php-adapter.atomic_apply_pool_revision` |
| timeout | `30s` |
| idempotency | `revision-protected` |
| concurrency_scope | `application-pool` |
| reconciliation | `compare active revision` |
| `origin_contract_id` | `IPC-WCP-1014` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1015 — IPC-WCP-1015

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `php_pool.remove` |
| job_type | `php_pool.remove` |
| ipc_operation_id | `php_pool.remove` |
| resource_identifier | `application_revision` |
| validated_input_schema | `{application_id,pool_revision_id,expected_active_revision}` |
| bounded_primitive | `php-adapter.remove_managed_pool` |
| timeout | `30s` |
| idempotency | `key-protected` |
| concurrency_scope | `application-pool` |
| reconciliation | `observe pool state` |
| `origin_contract_id` | `IPC-WCP-1015` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1016 — IPC-WCP-1016

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `php_fpm.reload` |
| job_type | `php_fpm.reload` |
| ipc_operation_id | `php_fpm.reload` |
| resource_identifier | `service` |
| validated_input_schema | `{service_id,runtime_profile_id,config_revision_id}` |
| bounded_primitive | `service-adapter.reload_registered_service` |
| timeout | `30s` |
| idempotency | `replay-safe same revision` |
| concurrency_scope | `service` |
| reconciliation | `verify health/config revision` |
| `origin_contract_id` | `IPC-WCP-1016` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1017 — IPC-WCP-1017

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `certificate.deploy` |
| job_type | `certificate.deploy` |
| ipc_operation_id | `certificate.deploy` |
| resource_identifier | `certificate_deployment` |
| validated_input_schema | `{certificate_id,deployment_target_id,certificate_revision}` |
| bounded_primitive | `certificate-adapter.atomic_deploy_secret_ref` |
| timeout | `60s` |
| idempotency | `revision-protected` |
| concurrency_scope | `deployment_target` |
| reconciliation | `verify deployed fingerprint` |
| `origin_contract_id` | `IPC-WCP-1017` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1018 — IPC-WCP-1018

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `certificate.remove` |
| job_type | `certificate.remove` |
| ipc_operation_id | `certificate.remove` |
| resource_identifier | `certificate_deployment` |
| validated_input_schema | `{certificate_id,deployment_target_id,expected_revision}` |
| bounded_primitive | `certificate-adapter.remove_managed_deployment` |
| timeout | `30s` |
| idempotency | `key-protected` |
| concurrency_scope | `deployment_target` |
| reconciliation | `inspect target state` |
| `origin_contract_id` | `IPC-WCP-1018` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1019 — IPC-WCP-1019

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `database.create` |
| job_type | `database.create` |
| ipc_operation_id | `database.create` |
| resource_identifier | `database` |
| validated_input_schema | `{database_id,engine_profile_id}` |
| bounded_primitive | `database-adapter.create_registered_database` |
| timeout | `120s` |
| idempotency | `key-required` |
| concurrency_scope | `database_id` |
| reconciliation | `inspect hosted-engine inventory` |
| `origin_contract_id` | `IPC-WCP-1019` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1020 — IPC-WCP-1020

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `database.drop` |
| job_type | `database.drop` |
| ipc_operation_id | `database.drop` |
| resource_identifier | `database` |
| validated_input_schema | `{database_id,expected_revision,deletion_authorization_ref}` |
| bounded_primitive | `database-adapter.drop_registered_database` |
| timeout | `120s` |
| idempotency | `not blindly retryable` |
| concurrency_scope | `database_id` |
| reconciliation | `inspect hosted-engine inventory` |
| `origin_contract_id` | `IPC-WCP-1020` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1021 — IPC-WCP-1021

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `database_user.create` |
| job_type | `database_user.create` |
| ipc_operation_id | `database_user.create` |
| resource_identifier | `db_identity` |
| validated_input_schema | `{database_id,db_identity_id}` |
| bounded_primitive | `database-adapter.create_registered_identity` |
| timeout | `60s` |
| idempotency | `key-required` |
| concurrency_scope | `database/db_identity` |
| reconciliation | `reconcile identity state` |
| `origin_contract_id` | `IPC-WCP-1021` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1022 — IPC-WCP-1022

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `database_user.drop` |
| job_type | `database_user.drop` |
| ipc_operation_id | `database_user.drop` |
| resource_identifier | `db_identity` |
| validated_input_schema | `{database_id,db_identity_id,expected_revision}` |
| bounded_primitive | `database-adapter.drop_registered_identity` |
| timeout | `60s` |
| idempotency | `not blindly retryable` |
| concurrency_scope | `db_identity` |
| reconciliation | `observe identity state` |
| `origin_contract_id` | `IPC-WCP-1022` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1023 — IPC-WCP-1023

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `database_grant.apply` |
| job_type | `database_grant.apply` |
| ipc_operation_id | `database_grant.apply` |
| resource_identifier | `grant_set` |
| validated_input_schema | `{database_id,db_identity_id,grant_set_id,expected_revision}` |
| bounded_primitive | `database-adapter.apply_registered_grants` |
| timeout | `60s` |
| idempotency | `revision-protected` |
| concurrency_scope | `database/db_identity` |
| reconciliation | `compare observed grants` |
| `origin_contract_id` | `IPC-WCP-1023` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1024 — IPC-WCP-1024

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `database_grant.revoke` |
| job_type | `database_grant.revoke` |
| ipc_operation_id | `database_grant.revoke` |
| resource_identifier | `grant_set` |
| validated_input_schema | `{database_id,db_identity_id,grant_set_id,expected_revision}` |
| bounded_primitive | `database-adapter.revoke_registered_grants` |
| timeout | `60s` |
| idempotency | `revision-protected` |
| concurrency_scope | `database/db_identity` |
| reconciliation | `compare observed grants` |
| `origin_contract_id` | `IPC-WCP-1024` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1025 — IPC-WCP-1025

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `firewall.policy.validate` |
| job_type | `N/A` |
| ipc_operation_id | `firewall.policy.validate` |
| resource_identifier | `firewall_revision` |
| validated_input_schema | `{server_id,policy_revision_id}` |
| bounded_primitive | `firewall-adapter.validate_structured_policy` |
| timeout | `15s` |
| idempotency | `replay-safe` |
| concurrency_scope | `policy_revision` |
| reconciliation | `validation result` |
| `origin_contract_id` | `IPC-WCP-1025` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1026 — IPC-WCP-1026

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `network.firewall.apply` |
| job_type | `firewall.policy.apply` |
| ipc_operation_id | `firewall.policy.apply` |
| resource_identifier | `firewall_revision` |
| validated_input_schema | `{server_id,policy_revision_id,expected_active_revision}` |
| bounded_primitive | `firewall-adapter.atomic_apply_revision` |
| timeout | `30s` |
| idempotency | `revision-protected` |
| concurrency_scope | `server-firewall` |
| reconciliation | `observe active policy` |
| `origin_contract_id` | `IPC-WCP-1026` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1027 — IPC-WCP-1027

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `firewall.policy.rollback` |
| job_type | `firewall.policy.rollback` |
| ipc_operation_id | `firewall.policy.rollback` |
| resource_identifier | `firewall_revision` |
| validated_input_schema | `{server_id,target_known_good_revision,current_revision}` |
| bounded_primitive | `firewall-adapter.apply_known_good_revision` |
| timeout | `30s` |
| idempotency | `revision-protected` |
| concurrency_scope | `server-firewall` |
| reconciliation | `verify target revision` |
| `origin_contract_id` | `IPC-WCP-1027` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1028 — IPC-WCP-1028

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `servers.service.status` |
| job_type | `N/A` |
| ipc_operation_id | `service.status` |
| resource_identifier | `service` |
| validated_input_schema | `{server_id,service_id}` |
| bounded_primitive | `service-adapter.status_registered_service` |
| timeout | `10s` |
| idempotency | `replay-safe` |
| concurrency_scope | `read` |
| reconciliation | `read-only verification` |
| `origin_contract_id` | `IPC-WCP-1028` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1029 — IPC-WCP-1029

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `servers.service.reload` |
| job_type | `service.reload` |
| ipc_operation_id | `service.reload` |
| resource_identifier | `service` |
| validated_input_schema | `{server_id,service_id,config_revision_id?}` |
| bounded_primitive | `service-adapter.reload_registered_service` |
| timeout | `60s` |
| idempotency | `revision/replay protected` |
| concurrency_scope | `service` |
| reconciliation | `health/config observation` |
| `origin_contract_id` | `IPC-WCP-1029` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1030 — IPC-WCP-1030

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `servers.service.restart` |
| job_type | `service.restart` |
| ipc_operation_id | `service.restart` |
| resource_identifier | `service` |
| validated_input_schema | `{server_id,service_id}` |
| bounded_primitive | `service-adapter.restart_registered_service` |
| timeout | `120s` |
| idempotency | `operation-ID protected` |
| concurrency_scope | `service` |
| reconciliation | `health/process-generation observation` |
| `origin_contract_id` | `IPC-WCP-1030` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1031 — IPC-WCP-1031

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `updates.metadata.refresh` |
| job_type | `package.refresh_metadata` |
| ipc_operation_id | `package.refresh_metadata` |
| resource_identifier | `package_repository_set` |
| validated_input_schema | `{server_id,repository_set_id}` |
| bounded_primitive | `package-adapter.refresh_registered_repositories` |
| timeout | `120s` |
| idempotency | `replay-safe` |
| concurrency_scope | `server-package-manager` |
| reconciliation | `refresh-state observation` |
| `origin_contract_id` | `IPC-WCP-1031` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1032 — IPC-WCP-1032

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `updates.plan` |
| job_type | `package.plan` |
| ipc_operation_id | `package.plan` |
| resource_identifier | `package_plan` |
| validated_input_schema | `{server_id,repository_snapshot_id,update_selection_id}` |
| bounded_primitive | `package-adapter.build_registered_plan` |
| timeout | `120s` |
| idempotency | `replay-safe same snapshot` |
| concurrency_scope | `server-package-manager` |
| reconciliation | `plan reconciliation` |
| `origin_contract_id` | `IPC-WCP-1032` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1033 — IPC-WCP-1033

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `updates.apply` |
| job_type | `package.apply_plan` |
| ipc_operation_id | `package.apply_plan` |
| resource_identifier | `package_plan` |
| validated_input_schema | `{server_id,package_plan_id}` |
| bounded_primitive | `package-adapter.apply_registered_plan` |
| timeout | `900s` |
| idempotency | `plan-ID protected` |
| concurrency_scope | `server-package-manager` |
| reconciliation | `inventory/plan reconciliation` |
| `origin_contract_id` | `IPC-WCP-1033` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1034 — IPC-WCP-1034

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `updates.hold` |
| job_type | `package.hold` |
| ipc_operation_id | `package.hold` |
| resource_identifier | `package` |
| validated_input_schema | `{server_id,package_id,reason_code}` |
| bounded_primitive | `package-adapter.hold_registered_package` |
| timeout | `60s` |
| idempotency | `desired-state protected` |
| concurrency_scope | `package_id` |
| reconciliation | `observe hold state` |
| `origin_contract_id` | `IPC-WCP-1034` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1035 — IPC-WCP-1035

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `updates.unhold` |
| job_type | `package.unhold` |
| ipc_operation_id | `package.unhold` |
| resource_identifier | `package` |
| validated_input_schema | `{server_id,package_id}` |
| bounded_primitive | `package-adapter.unhold_registered_package` |
| timeout | `60s` |
| idempotency | `desired-state protected` |
| concurrency_scope | `package_id` |
| reconciliation | `observe hold state` |
| `origin_contract_id` | `IPC-WCP-1035` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1036 — IPC-WCP-1036

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `servers.reboot` |
| job_type | `server.reboot` |
| ipc_operation_id | `server.reboot` |
| resource_identifier | `server` |
| validated_input_schema | `{server_id,operation_nonce}` |
| bounded_primitive | `power-adapter.REBOOT_registered_server` |
| timeout | `90s` |
| idempotency | `NOT_SAFELY_RETRYABLE` |
| concurrency_scope | `server-power` |
| reconciliation | `reconcile boot identity/health` |
| `origin_contract_id` | `IPC-WCP-1036` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1037 — IPC-WCP-1037

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `servers.shutdown` |
| job_type | `server.shutdown` |
| ipc_operation_id | `server.shutdown` |
| resource_identifier | `server` |
| validated_input_schema | `{server_id,operation_nonce}` |
| bounded_primitive | `power-adapter.POWEROFF_registered_server` |
| timeout | `60s` |
| idempotency | `NOT_SAFELY_RETRYABLE` |
| concurrency_scope | `server-power` |
| reconciliation | `out-of-band/health observation` |
| `origin_contract_id` | `IPC-WCP-1037` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1038 — IPC-WCP-1038

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `processes.terminate` |
| job_type | `process.term` |
| ipc_operation_id | `process.term` |
| resource_identifier | `process_instance` |
| validated_input_schema | `{server_id,process_instance_id}` |
| bounded_primitive | `process-adapter.fixed_SIGTERM_registered_instance` |
| timeout | `10s` |
| idempotency | `target-instance protected` |
| concurrency_scope | `process_instance` |
| reconciliation | `observe same process instance` |
| `origin_contract_id` | `IPC-WCP-1038` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1039 — IPC-WCP-1039

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `processes.kill` |
| job_type | `process.kill` |
| ipc_operation_id | `process.kill` |
| resource_identifier | `process_instance` |
| validated_input_schema | `{server_id,process_instance_id}` |
| bounded_primitive | `process-adapter.fixed_SIGKILL_registered_instance` |
| timeout | `10s` |
| idempotency | `NOT_RETRY_LOOPED` |
| concurrency_scope | `process_instance` |
| reconciliation | `observe same process instance` |
| `origin_contract_id` | `IPC-WCP-1039` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1040 — IPC-WCP-1040

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `storage.volume.expand` |
| job_type | `volume.expand` |
| ipc_operation_id | `volume.expand` |
| resource_identifier | `volume` |
| validated_input_schema | `{volume_id,target_size_bytes,expected_revision}` |
| bounded_primitive | `storage-adapter.expand_registered_volume` |
| timeout | `900s` |
| idempotency | `target-size protected` |
| concurrency_scope | `volume` |
| reconciliation | `observe provider size; never shrink` |
| `origin_contract_id` | `IPC-WCP-1040` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1041 — IPC-WCP-1041

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `storage.filesystem.expand` |
| job_type | `filesystem.expand` |
| ipc_operation_id | `filesystem.expand` |
| resource_identifier | `filesystem` |
| validated_input_schema | `{filesystem_id,target_size_bytes,expected_revision}` |
| bounded_primitive | `storage-adapter.expand_registered_filesystem` |
| timeout | `300s` |
| idempotency | `target-size protected` |
| concurrency_scope | `filesystem` |
| reconciliation | `observe filesystem size` |
| `origin_contract_id` | `IPC-WCP-1041` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1042 — IPC-WCP-1042

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `storage.mount.verify` |
| job_type | `N/A` |
| ipc_operation_id | `mount.verify` |
| resource_identifier | `mount` |
| validated_input_schema | `{mount_id}` |
| bounded_primitive | `storage-adapter.verify_registered_mount` |
| timeout | `30s` |
| idempotency | `replay-safe` |
| concurrency_scope | `read` |
| reconciliation | `verification only` |
| `origin_contract_id` | `IPC-WCP-1042` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1043 — IPC-WCP-1043

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `cluster.node.drain` |
| job_type | `node.drain` |
| ipc_operation_id | `node.drain` |
| resource_identifier | `cluster_node` |
| validated_input_schema | `{cluster_id,node_id,drain_policy_id,operation_id}` |
| bounded_primitive | `cluster-adapter.drain_registered_node` |
| timeout | `900s` |
| idempotency | `operation-ID protected` |
| concurrency_scope | `node` |
| reconciliation | `observe workloads/node state` |
| `origin_contract_id` | `IPC-WCP-1043` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1044 — IPC-WCP-1044

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `cluster.node.uncordon` |
| job_type | `node.uncordon` |
| ipc_operation_id | `node.uncordon` |
| resource_identifier | `cluster_node` |
| validated_input_schema | `{cluster_id,node_id,expected_state}` |
| bounded_primitive | `cluster-adapter.uncordon_registered_node` |
| timeout | `60s` |
| idempotency | `desired-state protected` |
| concurrency_scope | `node` |
| reconciliation | `observe state` |
| `origin_contract_id` | `IPC-WCP-1044` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1045 — IPC-WCP-1045

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `cluster.failover.prepare` |
| job_type | `cluster.failover` |
| ipc_operation_id | `cluster.failover.prepare` |
| resource_identifier | `cluster` |
| validated_input_schema | `{cluster_id,failover_operation_id,target_node_id}` |
| bounded_primitive | `cluster-adapter.prepare_registered_failover` |
| timeout | `300s` |
| idempotency | `operation-ID protected` |
| concurrency_scope | `cluster` |
| reconciliation | `observe readiness` |
| `origin_contract_id` | `IPC-WCP-1045` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1046 — IPC-WCP-1046

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `clusters.failover` |
| job_type | `cluster.failover` |
| ipc_operation_id | `cluster.failover.execute` |
| resource_identifier | `cluster` |
| validated_input_schema | `{cluster_id,failover_operation_id,target_node_id}` |
| bounded_primitive | `cluster-adapter.execute_prepared_failover` |
| timeout | `900s` |
| idempotency | `NOT_BLINDLY_RETRYABLE` |
| concurrency_scope | `cluster` |
| reconciliation | `topology reconciliation` |
| `origin_contract_id` | `IPC-WCP-1046` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1047 — IPC-WCP-1047

**Status:** `BLOCKED / NOT_EXECUTED`; QA owner decision `PENDING`. **Level:** CONTRACT + SECURITY proposed. **Version:** IPC-B/1.

| Source field | Exact source-derived value |
|---|---|
| action_id | `cluster.failover.verify` |
| job_type | `cluster.failover` |
| ipc_operation_id | `cluster.failover.verify` |
| resource_identifier | `cluster` |
| validated_input_schema | `{cluster_id,failover_operation_id,expected_primary_node_id}` |
| bounded_primitive | `cluster-adapter.verify_failover_state` |
| timeout | `300s` |
| idempotency | `replay-safe` |
| concurrency_scope | `read/cluster` |
| reconciliation | `verify final topology` |
| `origin_contract_id` | `IPC-WCP-1047` |

**Proposed positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Blocked on:**
- TESTS / QA #7 — owner-accepted test records
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** BLOCKED — no live wcp-privd; inert research parsers not a privileged effect test.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

## PART IV — N01..N26: original privileged-negative *labels* plus pending expectations

The N01–N26 labels and original text below match the final Security v1.0 §14 catalog. Exact error-code projections, fixture and component rejection points remain **NEW QA / Security / API owner decision proposals**, not approved TEST IDs.

### N01 — unauthorized Unix peer

**Original Security v1.0 catalog label:** unauthorized Unix peer.

**PROPOSED test title:** IPC peer authentication. **PROPOSED negative behavior:** SO_PEERCRED must match exact wcpd UID and expected service identity; unknown peer refused.

**Error classification candidate (NOT OWNER FINAL):** `IPC_CALLER_UNTRUSTED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N02 — wrong surface

**Original Security v1.0 catalog label:** wrong surface.

**PROPOSED test title:** Surface isolation. **PROPOSED negative behavior:** Wrong USER/ADMIN surface claim or peer/session cross-over is denied, no privilege fallback.

**Error classification candidate (NOT OWNER FINAL):** `AUTHORIZATION_DENIED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N03 — bad permission/action binding

**Original Security v1.0 catalog label:** bad permission/action binding.

**PROPOSED test title:** Typed authorization binding. **PROPOSED negative behavior:** Action/permission/IPC operation tuple mismatch denies dispatch, does not fall back by string similarity.

**Error classification candidate (NOT OWNER FINAL):** `AUTHORIZATION_DENIED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N04 — cross-Tenant target

**Original Security v1.0 catalog label:** cross-Tenant target.

**PROPOSED test title:** Tenant ownership. **PROPOSED negative behavior:** Target resource not owned/authorized by trusted Tenant context denied.

**Error classification candidate (NOT OWNER FINAL):** `AUTHORIZATION_DENIED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N05 — invalid ownership/membership/delegation

**Original Security v1.0 catalog label:** invalid ownership/membership/delegation.

**PROPOSED test title:** Delegation and ownership. **PROPOSED negative behavior:** Missing/expired/revoked delegation or membership never supplies substitute authority.

**Error classification candidate (NOT OWNER FINAL):** `AUTHORIZATION_DENIED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N06 — malformed schema

**Original Security v1.0 catalog label:** malformed schema.

**PROPOSED test title:** IPC-B strict schema. **PROPOSED negative behavior:** Malformed frame, duplicate key, malformed UTF-8, unknown fields or invalid types rejected before side effect.

**Error classification candidate (NOT OWNER FINAL):** `IPC_SCHEMA_INVALID`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N07 — unknown IPC operation

**Original Security v1.0 catalog label:** unknown IPC operation.

**PROPOSED test title:** IPC-B allowlist. **PROPOSED negative behavior:** Unknown IPC operation rejected with no dynamic fallback.

**Error classification candidate (NOT OWNER FINAL):** `IPC_OPERATION_NOT_ALLOWED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N08 — arbitrary command/executable

**Original Security v1.0 catalog label:** arbitrary command/executable.

**PROPOSED test title:** Generic command execution ban. **PROPOSED negative behavior:** Caller-provided shell/argv/executable/command/sudo invocation cannot be dispatched.

**Error classification candidate (NOT OWNER FINAL):** `IPC_OPERATION_NOT_ALLOWED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N09 — path traversal

**Original Security v1.0 catalog label:** path traversal.

**PROPOSED test title:** Filesystem path traversal. **PROPOSED negative behavior:** Traversal/absolute paths outside typed managed registry forbidden.

**Error classification candidate (NOT OWNER FINAL):** `VALIDATION_FAILED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N10 — symlink/mount/TOCTOU escape

**Original Security v1.0 catalog label:** symlink/mount/TOCTOU escape.

**PROPOSED test title:** Filesystem TOCTOU. **PROPOSED negative behavior:** Symlink/mount swap and path escape denied at use time; no outside-target mutation.

**Error classification candidate (NOT OWNER FINAL):** `VALIDATION_FAILED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N11 — arbitrary signal

**Original Security v1.0 catalog label:** arbitrary signal.

**PROPOSED test title:** Signal boundedness. **PROPOSED negative behavior:** Caller cannot supply signal number or arbitrary process signal.

**Error classification candidate (NOT OWNER FINAL):** `VALIDATION_FAILED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N12 — arbitrary systemd unit

**Original Security v1.0 catalog label:** arbitrary systemd unit.

**PROPOSED test title:** Service registry. **PROPOSED negative behavior:** Caller cannot provide arbitrary systemd unit name; only registered service_id.

**Error classification candidate (NOT OWNER FINAL):** `IPC_OPERATION_NOT_ALLOWED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N13 — arbitrary package argv/tampered plan

**Original Security v1.0 catalog label:** arbitrary package argv/tampered plan.

**PROPOSED test title:** Package plan integrity. **PROPOSED negative behavior:** Tampered plan, arbitrary argv/package selection rejected.

**Error classification candidate (NOT OWNER FINAL):** `VALIDATION_FAILED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N14 — duplicate/idempotency violation

**Original Security v1.0 catalog label:** duplicate/idempotency violation.

**PROPOSED test title:** Durable idempotency. **PROPOSED negative behavior:** Same key/different request conflicts; duplicate same semantic request does not repeat effects.

**Error classification candidate (NOT OWNER FINAL):** `IDEMPOTENCY_CONFLICT`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N15 — timeout/lost ACK → UNKNOWN_OUTCOME

**Original Security v1.0 catalog label:** timeout/lost ACK → UNKNOWN_OUTCOME.

**PROPOSED test title:** Lost acknowledgment. **PROPOSED negative behavior:** Post-dispatch ambiguous effect becomes UNKNOWN_OUTCOME and requires reconciliation, never definite FAIL/success.

**Error classification candidate (NOT OWNER FINAL):** `UNKNOWN_OUTCOME`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N16 — concurrent conflict

**Original Security v1.0 catalog label:** concurrent conflict.

**PROPOSED test title:** Concurrency fencing. **PROPOSED negative behavior:** Stale expected revision/fencing and concurrent owner race do not overwrite newer state.

**Error classification candidate (NOT OWNER FINAL):** `CONCURRENCY_CONFLICT`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N17 — mandatory audit unavailable

**Original Security v1.0 catalog label:** mandatory audit unavailable.

**PROPOSED test title:** Mandatory audit durability. **PROPOSED negative behavior:** Failure to commit required audit/outbox evidence prevents committed pure DB mutation; external effects retain durable uncertainty evidence.

**Error classification candidate (NOT OWNER FINAL):** `DEPENDENCY_FAILURE`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N18 — reconciliation after uncertainty

**Original Security v1.0 catalog label:** reconciliation after uncertainty.

**PROPOSED test title:** Reconciliation after uncertainty. **PROPOSED negative behavior:** No destructive re-dispatch before persisted observed-state reconciliation.

**Error classification candidate (NOT OWNER FINAL):** `UNKNOWN_OUTCOME`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N19 — secret echo

**Original Security v1.0 catalog label:** secret echo.

**PROPOSED test title:** Secret redaction. **PROPOSED negative behavior:** Secrets/cookies/raw tokens/keys not echoed in response/log/audit/events.

**Error classification candidate (NOT OWNER FINAL):** `INTERNAL_ERROR`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N20 — parameter boundary

**Original Security v1.0 catalog label:** parameter boundary.

**PROPOSED test title:** Typed parameter bounds. **PROPOSED negative behavior:** Oversized or malformed parameter values rejected before privileged operation.

**Error classification candidate (NOT OWNER FINAL):** `VALIDATION_FAILED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N21 — arbitrary SQL

**Original Security v1.0 catalog label:** arbitrary SQL.

**PROPOSED test title:** Hosted database command safety. **PROPOSED negative behavior:** Untrusted arbitrary SQL forbidden in database privileged operation.

**Error classification candidate (NOT OWNER FINAL):** `VALIDATION_FAILED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N22 — stale PID/PID reuse

**Original Security v1.0 catalog label:** stale PID/PID reuse.

**PROPOSED test title:** Process identity revalidation. **PROPOSED negative behavior:** Stale PID or PID reuse cannot terminate another process; require registered instance identity.

**Error classification candidate (NOT OWNER FINAL):** `CONCURRENCY_CONFLICT`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N23 — unregistered service ID

**Original Security v1.0 catalog label:** unregistered service ID.

**PROPOSED test title:** Service identifier registry. **PROPOSED negative behavior:** Unregistered service_id not mapped to dynamic systemd command.

**Error classification candidate (NOT OWNER FINAL):** `IPC_OPERATION_NOT_ALLOWED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N24 — invalid/stale resource ID

**Original Security v1.0 catalog label:** invalid/stale resource ID.

**PROPOSED test title:** Resource identifier validation. **PROPOSED negative behavior:** Unknown/stale/resource cross-tenant identity rejected, no target substitution.

**Error classification candidate (NOT OWNER FINAL):** `RESOURCE_NOT_FOUND`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N25 — destructive blind retry

**Original Security v1.0 catalog label:** destructive blind retry.

**PROPOSED test title:** Destructive retry safety. **PROPOSED negative behavior:** UNKNOWN_OUTCOME or lost response cannot automatically replay destructive operation.

**Error classification candidate (NOT OWNER FINAL):** `UNKNOWN_OUTCOME`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

### N26 — privilege downgrade/fallback

**Original Security v1.0 catalog label:** privilege downgrade/fallback.

**PROPOSED test title:** Privilege downgrade/fallback. **PROPOSED negative behavior:** No fallback from privileged to generic/root shortcut or ambiguous origin identity.

**Error classification candidate (NOT OWNER FINAL):** `AUTHORIZATION_DENIED`. OWNER MUST CONFIRM exact error code and HTTP/IPC outcome for activated operation.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** TESTS / QA A09 final case mapping and fixture; Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `BLOCKED / NOT_EXECUTED`, owner approval `FALSE`.

## PART V — 40 reserved WEB security IDs with NEW proposed per-ID scenario allocations

**Warning:** only the `TEST-SEC-WEB-001..040` range and thematic security properties are owner-final. This file does **not** establish that the source assigned these specific scenario titles or expected behaviors to those specific numbers. All number-to-scenario allocations below are candidates for QA ACCEPT/AMEND/REPLACE.

### TEST-SEC-WEB-001 — Wrong USER cookie on ADMIN (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** USER cookie must never authenticate ADMIN even when browser presents it; no fallback to USER identity.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-002 — Wrong ADMIN cookie on USER (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** ADMIN cookie must never authenticate USER or elevate USER scope.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-003 — Both surface credentials (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** When both cookies exist, backend uses exact current-origin surface and must not heuristically select a credential.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-004 — Cookie attributes on both surfaces (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Both distinct __Host- cookies require Secure, HttpOnly, SameSite=Strict, Path=/, host-only, no Domain.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-005 — Independent server session namespaces (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** USER and ADMIN sessions have different server-side namespaces, surface claims and Unix runtime identities.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-006 — Session fixation (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Login always rotates opaque unpredictable ≥128-bit session identifier; old fixed session cannot authenticate.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-007 — Authentication-strength rotation (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** MFA/auth-strength change rotates existing session ID and invalidates stale credential.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-008 — Privilege/impersonation rotation (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Elevation or impersonation start/end rotates credentials and separates child context.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-009 — Idle timeout (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** USER idle session expires after 30 min, ADMIN idle session after 15 min (evaluate server-side).

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-010 — Absolute lifetime (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** USER absolute session lifetime ≤12h, ADMIN ≤8h; no renewal past absolute expiration.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-011 — Remember-me prohibition (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** USER remember-me disabled at baseline, ADMIN remember-me forbidden.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-012 — Admin MFA login (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** WCP-ADMIN login must require MFA before authorized session use.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-013 — Fresh MFA elevated action (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** ADMIN high-impact actions require fresh MFA ≤5min; USER high-risk when required ≤10min.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-014 — Privileged child expiry (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** ADMIN privileged child context idle ≤5min absolute ≤15min; never persistent role grants.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-015 — Break-glass duration (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** ADMIN break-glass ≤15min without automatic extension; requires explicit scope/audit.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-016 — CSRF token absent (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** State-changing browser request without valid synchronizer CSRF token receives 403, no mutation and security evidence.

**Safe HTTP status:** 403 fixed by canonical source.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-017 — CSRF token wrong (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** State-changing browser request with wrong CSRF/X-WCP-CSRF token receives 403, no mutation.

**Safe HTTP status:** 403 fixed by canonical source.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-018 — Origin absent (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** State-changing browser request with missing exact Origin receives 403, no mutation.

**Safe HTTP status:** 403 fixed by canonical source.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-019 — Origin mismatched (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** State-changing browser request with other-origin value receives 403, no mutation.

**Safe HTTP status:** 403 fixed by canonical source.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-020 — No unsafe GET/HEAD/OPTIONS (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** GET, HEAD and OPTIONS cannot produce business-state mutation.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-021 — Credentialed wildcard CORS (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Session API does not allow wildcard credentialed cross-origin request; default is same-origin.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-022 — Untrusted HTML injection (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Untrusted content context-encoded, not executed as script; approved rich HTML sanitized.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-023 — Forbidden DOM execution APIs (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** No untrusted innerHTML/v-html/eval/new Function/javascript: URL/data-derived event-handler injection.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-024 — CSP nonces and prohibited directives (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Per-response CSP nonce for script/style; no unsafe-eval/global unsafe-inline/unapproved external script.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-025 — Frame exclusion (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** frame-ancestors none and X-Frame-Options DENY prevent framing.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-026 — SSRF target scope (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** WCP-USER rejects loopback/private/link-local/metadata/unapproved non-global targets.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-027 — SSRF unsupported schemes (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Reject file/ftp/gopher/data/javascript/unix and unapproved scheme variants.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-028 — SSRF DNS rebinding (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Resolve and validate every target DNS IP before connection; reject non-global DNS changes.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-029 — SSRF redirect policy (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Max three redirects; each redirect reparsed, re-resolved and revalidated.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-030 — Admin diagnostics registered endpoints (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** WCP-ADMIN internal diagnostics only by registered server/node/service/endpoint ID, not arbitrary URL.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-031 — Open redirect external/bypass (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Reject external, scheme-relative, encoded/backslash/userinfo-host redirects; allow internal named/normalized same-origin routes only.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-032 — Upload unsafe archive and staging (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Reject traversal, symlink/hardlink/device entries, client filename as path authority, executable staging or protected WCP directory.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-033 — Download reauthorization (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Authorize every download at request time, no raw browser path or cross-tenant disclosure.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-034 — SHOW_ONCE secret retrieval (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** SHOW_ONCE response Cache-Control no-store; never place secret in log/audit/notification/export.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-035 — NEVER_REVEAL secret retrieval (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** NEVER_REVEAL has no retrieval endpoint; never expose credentials or private keys.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-036 — Safe error projection (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Normal errors only stable safe error_code/message/correlation_id/field details; no stack trace/SQL/path/command/token.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-037 — Authenticated caching (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Authenticated HTML/sensitive APIs use Cache-Control no-store.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-038 — Logout and delegation revocation (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Logout revokes server session/CSRF/channels and ADMIN privilege/impersonation/break-glass; delegation revocation enforced from current state.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-039 — Reseller privilege non-escalation (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Reseller delegated access does not imply Tenant ownership or permit ADMIN privileges; plan/policy deny not bypassed.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

### TEST-SEC-WEB-040 — Rate limit never grants access (PROPOSED mapping)

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**Proposed expected behavior:** Security endpoint rate-limit class enforced in addition to authorization, no account enumeration or rate-limit-as-auth shortcut.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Fixture:** QA owner must provide specific browser, origin, cookie, HTTP request, session and API transport topology; final failure cause.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** TESTS / QA A10 per-ID mapping acceptance; Security / API S01–S04 current topology and proxy/WSS decisions for affected cases; SC-M01 actual implementation.
**Status:** `BLOCKED / NOT_EXECUTED`, one-to-one mapping `OWNER NOT APPROVED`.

## PART VI — 85 historical proposed scenario labels, each requiring QA triage

The 85 scenario labels below come from the 2026-10-03 QA candidate §22; they are **not canonical TEST IDs**, and their exact individual assertion text was NOT provided by that source. No new expected behavior is inferred here. QA must choose for each `REUSE_EXISTING / MERGE / SPLIT / DISCARD / DEFER / CREATE_NEW_OWNER_APPROVED_ID` and document source of the decision.

| Local proposal label | Source family (existing) | QA triage | Resulting canonical test/owner decision |
|---|---|---|---|
| `C01` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C02` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C03` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C04` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C05` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C06` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C07` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C08` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C09` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C10` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C11` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C12` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C13` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C14` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C15` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C16` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `C17` | Authorization | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ01` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ02` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ03` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ04` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ05` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ06` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ07` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ08` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ09` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `TQ10` | Timeout / idempotency | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ01` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ02` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ03` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ04` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ05` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ06` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ07` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ08` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ09` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ10` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ11` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `CQ12` | Control boundary | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ01` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ02` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ03` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ04` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ05` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ06` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ07` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ08` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ09` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ10` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ11` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ12` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ13` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ14` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `HQ15` | API transport | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ01` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ02` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ03` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ04` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ05` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ06` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ07` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ08` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ09` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ10` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ11` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ12` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ13` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ14` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ15` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `SQ16` | Security web / authz | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ01` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ02` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ03` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ04` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ05` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ06` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ07` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ08` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ09` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ10` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ11` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ12` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ13` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ14` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |
| `FQ15` | Toolchain / build | **PENDING QA TRIAGE** | `NOT_ASSIGNED` |


## PART VI-A — 85 source-exact historical proposal scenario rows (NON-CANONICAL)

**Purpose:** Part VI preserved all 85 proposal labels but not their actual source scenario wording. This annex includes the **verbatim original scenario row** found in the already-versioned P1 proposal artifacts. It does NOT turn a proposal case into an approved canonical TEST ID or assert the scenario is currently correct under owner-final architecture.

**Authority and precedence:** All original rows below are **owner-pending proposals**. The last published canonical Security/API/IPC/DevOps contracts supersede any conflicting proposed behavior. The 2026-10-03 QA owner family outline and the 199 case records above remain P1 pending. QA must ACCEPT/AMEND/DISCARD/MERGE/SPLIT/DEFER cases under A17, with full fixture, exact expected outcome and no-effect evidence, then assign a canonical TEST ID only if QA approves.

### Original proposal source blobs

| Source document (existing P1) | Git blob SHA (integrity) |
|---|---|
| `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` | `2722238799c2cdee7d863e28b9d6b9728e510512` |
| `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md` | `8894b45aea7b19302b2838a77b42b070172e8b0d` |
| `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` | `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923` |
| `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` | `0ac59335fb3f2422f0e800a96370f6f3afc75313` |
| `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` | `7e13a79a347b07981356295c2dc4977817e9e26d` |
| `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` | `c0953ce88e14ae08a16fdd5b4f604e992c8a5693` |
| `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` | `abe573b629ca3c00862ff555d3e5df05d5cc76e0` |

**Each entry below preserves the source row exactly, including its original fields.** No scenario value has been inferred or normalized here. No QA case has been executed.

### C01 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C01 | Session ADMIN privilégiée valide ; parent reload ; cible et révision enregistrées | Éligible uniquement à service.reload si les autres gates passent | Aucun restart ; contexte/primitive/cible corrélés |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C02 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C02 | Même contexte mais substitution de service.restart | DENY pour ce parent | Aucun effet système ni fallback |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C03 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C03 | Parent restart valide vers service.restart, même cible | Éligible si tous les autres gates passent | Pas de duplication aveugle en cas de réponse perdue |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C04 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C04 | Permission services.manage valide mais action d'origine remplacée par ce même texte | Pas d'alias implicite ; refus sans liaison normative explicite | Pas de reconnaissance de droits à partir de l'égalité de chaînes |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C05 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C05 | Parent firewall.apply valide, bonne politique et révision | Éligible à apply | Pas de politique choisie librement par la sous-opération |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C06 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C06 | Même contexte firewall, serveur ou révision substitué | DENY ou conflit selon le cas précis à fixer | Aucun effet sur la cible substituée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C07 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C07 | Parent grants.set et set validé pour tenant A/base A/identité A | Seules les sous-étapes dérivées de ce set sont éligibles | Pas de SQL libre ni de privilèges hors du set |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C08 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C08 | Substitution tenant B, autre base ou autre identité avec le même contexte | DENY | Aucun effet sur moteur hébergé ; trace sûre corrélée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C09 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C09 | Appel direct de revoke sans parent set valide | DENY | La permission seule ne crée pas une opération publique autonome |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C10 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C10 | Délégation révoquée après enqueue avant effet | Réévaluation selon Security, puis refus si le droit a disparu | Pas d'exécution avec un droit périmé |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C11 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C11 | Parent runtime.update proposé comme autorisation de linux_user.delete sans preuve du workflow | Bloqué jusqu'à décision propriétaire ; aucune correspondance inventée | Pas de suppression Unix |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C12 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md` — blob `2722238799c2cdee7d863e28b9d6b9728e510512`.

**Verbatim proposed scenario row:**

```text
| C12 | Droit parent valide mais MFA expirée, entitlement absent ou approbation manquante lorsqu'exigés | Refus/reprise d'auth approprié ; aucun ALLOW anticipé | Aucun effet ; code/message sûrs à définir dans l'API |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C13 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md` — blob `8894b45aea7b19302b2838a77b42b070172e8b0d`.

**Verbatim proposed scenario row:**

```text
| C13 — deux niveaux | Contexte grants.set authentique et lié à apply ; remplacer dans l'enveloppe l'action technique database_grant.apply par databases.grants.set | Refus du couple IPC non conforme sous l'option B ; origine conservée dans l'audit ; aucun effet | N03, TEST-IPC-1023 ; comparer cas témoin légitime et cas substitué |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C14 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md` — blob `8894b45aea7b19302b2838a77b42b070172e8b0d`.

**Verbatim proposed scenario row:**

```text
| C14 — substitution d'étape | Même contexte borné à apply ; demander revoke avec le couple IPC lexicalement correct | Refus de l'étape non autorisée par ce contexte précis ; aucune révocation. Le couple local IPC correct ne suffit pas | N03/N05, TEST-IPC-1024 ; audit corrélé au parent et à l'étape refusée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C15 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md` — blob `8894b45aea7b19302b2838a77b42b070172e8b0d`.

**Verbatim proposed scenario row:**

```text
| C15 — contexte indisponible | Le couple action/opération est correct mais la référence ne peut pas être résolue ou sa fiabilité vérifiée avant dispatch | Aucun effet ni fallback sur des droits déclarés par l'appelant ; code sûr à fixer selon D02 | N05/N24 selon classification QA ; aucune primitive déclenchée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C16 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md` — blob `8894b45aea7b19302b2838a77b42b070172e8b0d`.

**Verbatim proposed scenario row:**

```text
| C16 — révision périmée | Une révision attendue est autorisée ; l'état actif change avant effet | Conflit/refus avant effet selon contrat applicable ; pas d'écrasement de la nouvelle révision ; si le dispatch est déjà incertain, réconciliation distincte | N16, TEST-IPC-1026/1029 ; état observé inchangé par la requête périmée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### C17 — Authorization origin/technical bindings (C01–C17) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md` — blob `8894b45aea7b19302b2838a77b42b070172e8b0d`.

**Verbatim proposed scenario row:**

```text
| C17 — résultat incertain | La primitive peut avoir modifié le système, mais sa réponse finale est perdue | UNKNOWN_OUTCOME/réconciliation ; pas de répétition aveugle ni de succès inventé ; conservation distincte de l'origine et de l'étape | N15/N18/N25, tests IPC des cinq lignes ; preuve pré-effet et preuve de reprise |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ01 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ01 | connexion impossible >1s | aucun dispatch ; erreur transport sûre ; aucune intention d'effet créée sauf politique explicitement définie |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ02 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ02 | client envoie un frame partiel puis bloque >5s | connexion close/refus ; zéro primitive |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ03 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ03 | request complète, deadline déjà expiré avant P4 | zéro primitive ; résultat déterministe, pas UNKNOWN_OUTCOME |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ04 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ04 | deadline demandé supérieur au timeout de la ligne | le plafond de l'opération gagne ; aucun allongement par le client |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ05 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ05 | opération 120s reste silencieuse >5s mais respecte son budget effectif | ne doit pas échouer uniquement parce que I/O TIMEOUT=5s sous option B |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ06 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ06 | deadline expire avant dispatch durable | zéro effet ; preuve d'absence de dispatch |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ07 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ07 | effet exécuté puis ACK/réponse finale perdue | UNKNOWN_OUTCOME ou résultat durable récupérable selon preuve ; aucun blind retry |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ08 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ08 | même demande logique réémise avec nouveau request_id après perte de réponse | ne doit pas provoquer un deuxième effet si l'idempotence durable indique une opération existante |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ09 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ09 | deux exécuteurs concurrents revendiquent la même opération à reprendre | un seul peut déclencher/réconcilier selon le contrat propriétaire ; aucun double effet |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### TQ10 — Timeout, idempotency and reconciliation (TQ01–TQ10) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` — blob `1339e13b2d2ea86c0c8cc2053274aeac5f4e7923`.

**Verbatim proposed scenario row:**

```text
| TQ10 | réconciliation après restart conclut que l'effet a eu lieu | finalisation durable/audit cohérents ; ne pas redispatcher pour « confirmer » |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ01 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ01 | peer Unix non autorisé ouvre le control socket | refus avant traitement métier ; aucun enqueue/dispatch |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ02 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ02 | peer `wcp360-user` présente contexte surface ADMIN | refus ; aucun downgrade/fallback |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ03 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ03 | peer `wcp360-admin` présente contexte USER substitué pour contourner une règle | refus ou traitement selon contrat explicite, jamais changement silencieux de surface |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ04 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ04 | peer autorisé mais permission/action/ticket context inconnu | fail closed ; aucune opération privilégiée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ05 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ05 | ressource de la requête diffère de resource_id du contexte | refus ; aucune substitution de cible |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ06 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ06 | contexte révoqué après création, avant effet | revalidation applicable puis refus si droit disparu |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ07 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ07 | worker tente directement IPC-B | refus Unix/peer avant effet |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ08 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ08 | worker tente directement le control socket alors que W1 n'est pas approuvé | refus transport ; ne pas ajouter le groupe pour faire passer le test |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ09 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ09 | même job consommé simultanément par deux workers/composants | un seul claim/exécuteur selon contrat DB ; aucune duplication d'effet |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ10 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ10 | opération inconnue au control boundary | aucun fallback en shell, generic exec ou passthrough IPC-B |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ11 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ11 | daemon redémarre avec jobs/intents persistants | ne restaure pas aveuglément les locks éphémères ; reprise via état durable/réconciliation |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### CQ12 — Backend / wcpd control trust boundary (CQ01–CQ12) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` — blob `0ac59335fb3f2422f0e800a96370f6f3afc75313`.

**Verbatim proposed scenario row:**

```text
| CQ12 | correlation_id réutilisé avec autre cible | aucun droit dérivé de la corrélation ; authorization binding indépendant |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ01 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ01 | mutation via GET | 0 mutation ; méthode refusée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ02 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ02 | champ inconnu | VALIDATION_FAILED |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ03 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ03 | champ requis manquant | VALIDATION_FAILED |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ04 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ04 | même clé idempotence, même requête | même résultat logique, pas deuxième effet |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ05 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ05 | même clé, requête différente | IDEMPOTENCY_CONFLICT |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ06 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ06 | header/body idempotence divergents si I2 retenu | refus avant mutation |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ07 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ07 | session USER sur route ADMIN | refus surface/auth |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ08 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ08 | cookie valide, CSRF manquant | 403/no mutation pour navigateur |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ09 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ09 | Origin incorrect | 403/no mutation |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ10 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ10 | token non-cookie avec CSRF absent | résultat selon contrat token, pas application mécanique de règle session |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ11 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ11 | champs d'une autre valeur `operation` inclus | validation refusée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ12 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ12 | cursor réutilisé avec autre tenant/filter | aucune fuite ; cursor refusé/invalide |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ13 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ13 | JobAccepted reçu | preuve que job/intention est durable, pas effet externe réussi |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ14 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ14 | erreur interne | aucun stack/path/secret ; correlation_id sûr |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### HQ15 — HTTP transport, validation, CSRF, safe errors (HQ01–HQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md` — blob `7e13a79a347b07981356295c2dc4977817e9e26d`.

**Verbatim proposed scenario row:**

```text
| HQ15 | réponse SecretCreated | no-store ; secret non récupérable ultérieurement si SHOW_ONCE/NEVER_REVEAL applicable |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ01 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ01 | USER et ADMIN sur hostnames distincts | chaque requête de surface n'expose/accepte que le credential attendu selon critère S04 |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ02 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ02 | même hostname / ports distincts dans fixture | démontrer que les ports seuls ne sont pas utilisés comme garantie de cookie |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ03 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ03 | cookie ADMIN envoyé à endpoint USER sans cookie USER | aucune auth USER dérivée ; aucun détail de session ADMIN divulgué |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ04 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ04 | cookie USER envoyé à endpoint ADMIN | aucune auth ADMIN dérivée |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ05 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ05 | cookies USER+ADMIN présents | sélection déterministe de la surface ; aucun fallback |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ06 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ06 | CSRF USER sur session ADMIN | refus sans mutation |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ07 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ07 | Origin USER vers mutation ADMIN | refus sans mutation |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ08 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ08 | rôle accorde permission mais entitlement absent | DENY final |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ09 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ09 | rôle accorde permission, tenant ownership invalide | DENY final |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ10 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ10 | rôle accorde permission + DENY explicite | DENY final |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ11 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ11 | override négatif + rôle positif | DENY/NOT_GRANTED selon modèle ratifié, jamais ALLOW par le rôle |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ12 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ12 | override positif + contrainte Security absente | DENY final |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ13 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ13 | délégation demande permission au-delà du reseller | DENY |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ14 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ14 | délégation révoquée entre enqueue et effet | refus à la revalidation ; aucun effet |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ15 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ15 | approval valide mais permission absente/DENY | aucun effet |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### SQ16 — Web surface, IAM, tenant authorization (SQ01–SQ16) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` — blob `c0953ce88e14ae08a16fdd5b4f604e992c8a5693`.

**Verbatim proposed scenario row:**

```text
| SQ16 | changement de contexte sans changement de permission | aucun droit nouveau dérivé du seul context switch |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ01 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ01 | clean checkout + declared toolchain | build/test bootstrap succeeds without undeclared local files |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ02 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ02 | missing lock file | official build rejected or owner-defined deterministic failure |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ03 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ03 | lock changed without manifest/dependency update | CI detects inconsistency |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ04 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ04 | dependency version floats to latest | release build policy rejects non-locked input |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ05 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ05 | unsupported runtime major | preflight/build fails clearly |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ06 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ06 | wrong architecture | build or package target rejected |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ07 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ07 | production secret present in source/config example | repository/security gate fails |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ08 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ08 | dependency from unapproved source | build fails before artifact promotion |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ09 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ09 | cache cleared | output remains semantically/bitwise reproducible according to chosen policy |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ10 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ10 | same commit + same inputs built twice | digest/reproducibility expectation holds |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ11 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ11 | frontend toolchain absent when declared unnecessary | backend/daemon foundation still builds |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ12 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ12 | Node present only as build dependency | production package has no undeclared Node runtime dependency |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ13 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ13 | static analysis tool failure | CI gate fails according to Engineering rule |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ14 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ14 | generated artifact edited without source change | drift check detects or regeneration overwrites deterministically |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

### FQ15 — Build and toolchain QA case candidates (FQ01–FQ15) (historical PROPOSAL)

**Source:** `governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md` — blob `abe573b629ca3c00862ff555d3e5df05d5cc76e0`.

**Verbatim proposed scenario row:**

```text
| FQ15 | artifact digest/signature mismatch | install/promotion path rejects artifact |
```

**Owner triage:** `PENDING`; **canonical TEST ID:** `NOT_ASSIGNED` (unless separately referenced by QA); **execution:** `NOT_EXECUTED`; **owner approval:** `NO`.

**Summary of this annex:** 17 Authorization + 10 Timeout + 12 Control + 15 HTTP + 16 Security Web + 15 Toolchain = **85 unapproved, non-executed local scenario labels**, not 85 new approved tests.
## PART VII — Final owner disposition / signature requirements (NOT SIGNED)

The QA owner must explicitly approve, amend, reject or defer **each** obligation below. A CODEOWNER auto-notification, GitHub PR author comment, green CI or same-author E2 is not specialized owner approval.

| Owner decision | Candidate disposition | Actual owner result | Owner evidence |
|---|---|---|---|
| A01 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A02 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A03 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A04 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A05 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A06 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A07 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A08 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A09 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A10 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A11 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A12 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A13 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A14 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A15 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A16 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A17 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A18 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A19 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| A20 (existing QA checklist) | Proposed by separate A01–A20 packet | **PENDING** | `NONE` |
| QI-01 (execution integrity) | Proposed by separate source | **PENDING** | `NONE` |
| QI-02 (execution integrity) | Proposed by separate source | **PENDING** | `NONE` |
| QI-03 (execution integrity) | Proposed by separate source | **PENDING** | `NONE` |
| QI-04 (execution integrity) | Proposed by separate source | **PENDING** | `NONE` |
| QI-05 (execution integrity) | Proposed by separate source | **PENDING** | `NONE` |
| QI-06 (execution integrity) | Proposed by separate source | **PENDING** | `NONE` |
| QI-07 (execution integrity) | Proposed by separate source | **PENDING** | `NONE` |
| Provenance P1/P2/P3 | P2 first canonical materialization suggested | **PENDING** | `NONE` |
| 86 TEST-API case assertions | source-referenced / blocked | **PENDING** | `NONE` |
| 47 TEST-IPC case assertions | source-referenced / blocked | **PENDING** | `NONE` |
| N01..N26 negative cases | original labels + proposed error mapping | **PENDING** | `NONE` |
| WEB001..040 per-ID allocations | **NEW** proposed mapping | **PENDING** | `NONE` |
| 85 proposed historical labels | reuse/merge/split/discard/defer | **PENDING** | `NONE` |
| DB/DevOps/Engineering/Failure/recovery test-family applicability | Candidate outline in PART I | **PENDING** | `NONE` |
| Complete code-baseline QA evidence DoD | Candidate outline in PART I | **PENDING** | `NONE` |

### Owner publication block (intentionally unsigned)

```text
ARTIFACT CLASSIFICATION = P1 OWNER REVIEW CANDIDATE — NOT FINAL
TARGET OWNER = WCP360 — TESTS / QA
TEST BASELINE = v1.0 PROPOSAL
FINAL APPROVED CONTENT = NO
OWNER PUBLICATION APPROVAL = NOT PRESENT
OWNER EXACT QA DECISIONS = PENDING
CONTENT CHANGE = UNDECIDED BY OWNER — P2 RECOMMENDED
HISTORICAL EXACT PRIOR FINAL BODY = NOT EVIDENCED
REVIEW CASE RECORDS = 199
HISTORICAL NONCANONICAL SCENARIO LABELS = 85
SOURCE_REFS API = 86
SOURCE_REFS IPC = 47
SOURCE_PERMISSIONS = 82
PRODUCT TESTS ACTUALLY EXECUTED = 0
RESULT PASS = 0
RESULT FAIL = 0
IMPLEMENTATION-DEPENDENT CASE STATUS = PLANNED/BLOCKED
FINAL SPECIFICATIONS/TESTS/ ARTIFACT = NOT PUBLISHED
QA ISSUE #7 = OPEN
MASTER SC-M01 IMPLEMENTATION GATE = 5/8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

### Required next owner action to advance 5/8 → 6/8

1. **TESTS / QA** validates the full source and all applicable accepted/inherited records in this single-file candidate. It must retain explicit `BLOCKED` state for unavailable DB/Engineering/HTTP topology decisions and never fabricate results.
2. Owner selects **P1** only with a proven byte-identical previously approved complete source, **P2** for honest first canonical materialization of the accepted/modified content, or **P3** with explicit version/MASTER change. The old v1.0 version metadata alone cannot establish P1.
3. Owner records explicit A01–A20/QI01–07 decisions, approves/replaces the WEB ID mapping and N outcome matrix, triages the 85 local labels, signs final scope and applicability of other planned QA families.
4. On acceptance, owner publishes a **complete, self-contained approved** file in `specifications/tests/`, references exact source owner approval/version/commit SHA, updates GitHub issue #7 and returns it to MASTER; independent specialized cross-review where applicable.
5. MASTER verifies actual final owner artifact in `main` and source readiness, only then updates the 8-artifact gate from **5/8 to 6/8**. This draft may **not** be renamed FINAL by the assistant to satisfy a numerical target.

**No application code, migrations, runtime execution, or branch-protection claims are produced by this P1 materialization.**
