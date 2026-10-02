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
    EVIDENCE TO RETAIN
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
    run ID / job ID
    start/end timestamps
    result
    failure details if safe
    evidence artifact digest/reference

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
