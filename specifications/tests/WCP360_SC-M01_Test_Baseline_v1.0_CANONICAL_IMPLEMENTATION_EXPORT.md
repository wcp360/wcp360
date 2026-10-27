# WCP360 — SC-M01 Test Baseline v1.0 — CANONICAL IMPLEMENTATION EXPORT

**Owner:** WCP360 — TESTS / QA  
**Status:** **FINAL APPROVED — CANONICAL IMPLEMENTATION AUTHORITY**  
**Owner approval date:** 2026-10-28  
**Scope:** SC-M01 — FIRST IMPLEMENTATION FOUNDATION only  
**Baseline:** WCP360-BM-0001 FINAL / DOC v4.0.21.23 / DESIGN V2.5.3.36.4 / CODE NONE  
**TEST BASELINE VERSION:** `v1.0`  
**PUBLICATION TYPE:** **P2 — FIRST CANONICAL MATERIALIZATION / OWNER APPROVAL**  
**CONTENT CHANGE:** **INITIAL CANONICAL CONTENT**  
**VERSION IMPACT:** **NONE — retain the already-published Test Baseline identifier v1.0**  
**Historical exact prior complete final body:** NOT EVIDENCED  
**Historical content included:** NO — historical proposal bodies are excluded; only provenance and owner disposition are retained  
**Product tests actually executed:** 0  
**PASS:** 0  
**FAIL:** 0  
**CODE-BASELINE:** NONE  
**Source Code authorization:** NOT GRANTED BY THIS ARTIFACT  
**MASTER gate:** remains 5/8 until MASTER verifies this owner-final publication; this artifact is eligible to be counted as the sixth owner artifact after MASTER verification.

## Provenance

This is an honest first canonical materialization. It does **not** claim byte-identical recovery of a previous final Test Baseline body.

| Source | Git blob SHA | Use |
|---|---|---|
| `governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md` | `fe2e93ade5541808ff0a0c718ef5c74fb0b2356b` | QA structure reviewed and owner-amended |
| `governance/proposals/SC-M01-QA-133-TEST-RECORDS-OWNER-CANDIDATE-v0.1.json` | `fd1c570c396cebc2b20b3c89c96ca9b033fc2dc1` | 86 API + 47 IPC review records |
| `governance/proposals/SC-M01-QA-66-SECURITY-TEST-ANNEX-OWNER-CANDIDATE-v0.1.json` | `e56db7549d8b30ebffd3687265e92964bfd1acdf` | N01–N26 + proposed WEB mappings |
| `governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json` | `e039c8d7842df2192b0ecb41abf653000d74d116` | machine-derived API/IPC/permission source relationships |
| `governance/proposals/SC-M01-QA-A01-A20-OWNER-DECISION-PACKET-v0.1.md` | `5d951d53410d4ab5ee1e922524f85f1479e60ec5` | decision packet reviewed, not treated as prior approval |
| API Contract v1.0 | `4893c5191b4928ecfa5a70f4dc14b39a8939fcea` | canonical API source |
| IPC Contract WCP360-IPC-B/1 | `fec9c74e8413db822662e47c76c40486e282334f` | canonical IPC source |
| Permission Registry v1.0 | `81f3d425f36470167aba4bfb7ba069268626342f` | canonical permission source |
| Security Baseline v1.0 | `6d74f2f363524511424f839ca7262143c2af95ab` | canonical security source |
| DevOps Baseline v1.0 | `61edf698c50cf58779b75ba02fba913bf8276c61` | canonical platform source |

## Source verification performed for owner approval

- API records: **86 / 86** exact `TEST-API ↔ API-WCP` relationships and source fields matched the canonical coverage index.
- IPC records: **47 / 47** exact `TEST-IPC ↔ IPC-WCP` relationships and all source fields matched the canonical coverage index.
- Active permissions: **82 / 82** represented through **86** API bindings; missing active permissions: **0**.
- Security negative labels: **26 / 26** exact matches to Security v1.0 §14.
- WEB range: **40 / 40 unique IDs**; TESTS / QA approves the one-to-one mapping in PART V.
- Historical proposal rows: **85 / 85** source-exact rows reviewed; final triage is in PART VI; historical bodies are excluded.
- Product execution: **NONE**. No PASS/FAIL is inferred from document review, CI, source matching, or owner approval.


## 1. Final owner decision matrix — A01–A20

| ID | Decision | Final QA disposition |
|---|---|---|
| **A01** | **AMEND** | Select P2 FIRST CANONICAL MATERIALIZATION / OWNER APPROVAL; retain Test Baseline v1.0; publish exact final path under specifications/tests/; CONTENT CHANGE = INITIAL CANONICAL CONTENT. |
| **A02** | **ACCEPT** | PLANNED/READY/BLOCKED/PASS/FAIL lifecycle retained; PASS/FAIL require execution. |
| **A03** | **AMEND** | Required record fields accepted; inheritance is allowed only through an explicit source/version/section pointer and cannot hide required assertions. |
| **A04** | **AMEND** | Exact-SHA/environment evidence envelope accepted; durable retention is mandatory, but no numeric retention duration/storage backend/digest algorithm is invented here. |
| **A05** | **ACCEPT** | UNIT/CONTRACT/INTEGRATION/SECURITY/E2E/RECOVERY/INSTALL-BUILD distinction retained; tags remain separate from test level. |
| **A06** | **AMEND** | 86/86 TEST-API anchors accepted after field-by-field source verification. Logical contract assertions are accepted; transport route/method/status/DTO assertions stay BLOCKED where the activated owner contract does not define them. |
| **A07** | **ACCEPT** | 47/47 TEST-IPC anchors accepted after exact source verification; bounded primitive, peer, framing, authz, timeout, idempotency, concurrency, UNKNOWN_OUTCOME and reconciliation obligations retained. |
| **A08** | **AMEND** | Mixed evidence strategy fixed: 82/82 permissions are covered through their 86 API bindings plus dedicated authorization/tenant/delegation/entitlement negative assertions; lexical equality never substitutes typed binding. |
| **A09** | **AMEND** | N01–N26 labels and behavioral/no-effect expectations accepted. Candidate safe error codes are not made normative unless an owner-final contract fixes them; those projections remain BLOCKED otherwise. |
| **A10** | **AMEND** | Approve the 40 one-to-one TEST-SEC-WEB-001..040 mappings in PART V as QA-owned mapping. Design mapping is READY; product execution remains NOT_EXECUTED, and topology-specific assertions may remain BLOCKED. |
| **A11** | **ACCEPT** | Physical DB PK/FK/UNIQUE/CHECK/transaction/lease/outbox/recovery assertions remain BLOCKED on Database #6 where exact owner-final physical definitions are unavailable. |
| **A12** | **ACCEPT** | Exact Engineering toolchain/static-analysis/threshold/DoD gates remain BLOCKED on Engineering #9. |
| **A13** | **ACCEPT** | Failure-injection matrix and UNKNOWN_OUTCOME semantics retained; no blind retry. |
| **A14** | **ACCEPT** | Semantic idempotency rules retained; persistence-specific assertions remain BLOCKED where Database #6 is required. |
| **A15** | **ACCEPT** | Foundation-scoped repository/bootstrap/secret/generated-asset/product-root/CI guardrail tests retained; repository CI is not product PASS evidence. |
| **A16** | **AMEND** | Debian GNU/Linux 13 (trixie) / amd64 target is accepted from DevOps v1.0; exact language/toolchain pins and owner quality gates remain BLOCKED on Engineering #9. |
| **A17** | **AMEND** | All 85 historical proposal rows were source-verified 85/85 and individually triaged below: 64 MERGE/REUSE into existing QA anchors; 21 BLOCKED/DEFER with exact owner dependency; no new TEST ID invented. |
| **A18** | **ACCEPT** | Failure-visible CI reporting and exact selection/environment/result accounting retained. |
| **A19** | **ACCEPT** | Bug regression lifecycle plus attempt history/flaky/quarantine/determinism/isolation/rerun eligibility retained. |
| **A20** | **ACCEPT** | Exact-SHA first CODE-BASELINE QA gate retained; QA publication does not authorize Source Code or SC-M02. |

## 2. Execution-integrity decision matrix — QI01–QI07

| ID | Decision | Final QA disposition |
|---|---|---|
| **QI-01** | **ACCEPT** | Attempt history is immutable evidence; rerun never erases earlier failed/blocked attempt metadata. |
| **QI-02** | **ACCEPT** | Failure classification distinguishes product defect, infrastructure, nondeterminism/flaky and operator/environment failure. |
| **QI-03** | **ACCEPT** | Quarantine is metadata, never PASS; required quarantined assertions cannot contribute silently to a full-baseline PASS. |
| **QI-04** | **ACCEPT** | Clock/seed and other reproducibility inputs are explicit and retained when material. |
| **QI-05** | **ACCEPT** | Per-attempt tenant/resource/DB/schema/filesystem/socket fixtures must be isolated; privileged state cannot leak across tests. |
| **QI-06** | **AMEND** | Evidence must remain durable/auditable after ephemeral CI expiry and always redact secrets. Exact retention duration/storage backend/digest selection is non-blocking owner follow-up, not invented here. |
| **QI-07** | **ACCEPT** | Material code/tool/contract changes require explicit prior-evidence applicability review before reuse. |


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


## 22. Historical proposal intake — final owner rule

Historical proposal labels are not canonical TEST IDs. Their source-exact rows were reviewed 85/85 and are dispositioned individually in PART VI. No historical option overrides an owner-final API/IPC/Security/DevOps contract.


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


## 26. Current unresolved dependencies

These dependencies do **not** prevent publication of the QA owner baseline, but affected execution assertions remain `BLOCKED` until the responsible owner publishes the needed contract.

### Database #6 — BLOCKED where physical persistence is required
- exact PK/FK/UNIQUE/CHECK/table/column assertions;
- physical tenant-aware constraints;
- transaction/outbox/audit atomicity details;
- durable idempotency/job/lease/fencing storage;
- physical reconciliation/restore fixtures.

### Engineering #9 — BLOCKED where exact toolchain/quality gates are required
- exact language/runtime/tool versions beyond the DevOps-supported OS target;
- static-analysis tool set and thresholds;
- exact build/test commands and code-quality thresholds;
- review/merge/DoD rules that belong to Engineering.

### Slice-specific owner decisions — BLOCKED only when activated and still absent
- HTTP route/method/status/DTO projection not defined by API 1.0;
- deadline representation/budget composition beyond the fixed IPC timeout invariants;
- backend/worker → wcpd control-wire details not fixed by an owner-final contract;
- topology/proxy/WSS assertions not fixed by the activated Security/API deployment contract;
- exact negative error code where Security/API/IPC does not fix one.

No missing dependency may be guessed merely to make a test READY.

## 27. Canonical publication checklist

- TESTS / QA owner approval: **YES**.
- Test Baseline version: **v1.0**.
- Publication provenance: **P2 first canonical materialization**.
- 86 API records accounted for: **YES**.
- 47 IPC records accounted for: **YES**.
- 82 active permissions represented: **YES**.
- N01–N26 retained and behaviorally approved: **YES**.
- TEST-SEC-WEB-001→040 individually mapped by QA: **YES**.
- 85 historical proposals individually triaged: **YES — 64 MERGE / 21 BLOCKED**.
- DB/Engineering/slice-specific blocked clauses preserved: **YES**.
- Test-record schema and execution-evidence schema: **YES**.
- Fabricated PASS/FAIL: **NONE**.
- Historical proposal bodies included: **NO**.
- Source Code authorization: **NO — MASTER owns resume/implementation authorization**.


## Canonical source-coverage verification

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
product executions = 0
PASS = 0
FAIL = 0
```


## PART II — 86 owner-approved API test specification records (not executed)

The following 86 records preserve source typed relationships and contain **proposed** positive/negative assertions only. No transport route/status codes, actual fixtures or DB columns are manufactured. QA must accept/amend each record before final use.

### TEST-API-0101 — API-WCP-0101

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0102 — API-WCP-0102

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0103 — API-WCP-0103

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0104 — API-WCP-0104

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0105 — API-WCP-0105

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0106 — API-WCP-0106

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0201 — API-WCP-0201

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0202 — API-WCP-0202

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0203 — API-WCP-0203

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0001 — API-WCP-0001

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0204 — API-WCP-0204

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0205 — API-WCP-0205

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0301 — API-WCP-0301

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0302 — API-WCP-0302

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0303 — API-WCP-0303

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0304 — API-WCP-0304

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0305 — API-WCP-0305

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0306 — API-WCP-0306

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0307 — API-WCP-0307

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0308 — API-WCP-0308

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0401 — API-WCP-0401

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0402 — API-WCP-0402

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0403 — API-WCP-0403

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0404 — API-WCP-0404

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0405 — API-WCP-0405

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0406 — API-WCP-0406

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0407 — API-WCP-0407

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0501 — API-WCP-0501

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0502 — API-WCP-0502

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0503 — API-WCP-0503

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0504 — API-WCP-0504

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0505 — API-WCP-0505

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0601 — API-WCP-0601

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0602 — API-WCP-0602

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0603 — API-WCP-0603

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0604 — API-WCP-0604

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0605 — API-WCP-0605

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0701 — API-WCP-0701

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0702 — API-WCP-0702

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0703 — API-WCP-0703

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0704 — API-WCP-0704

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0705 — API-WCP-0705

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0706 — API-WCP-0706

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0707 — API-WCP-0707

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0708 — API-WCP-0708

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0709 — API-WCP-0709

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0801 — API-WCP-0801

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0802 — API-WCP-0802

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0803 — API-WCP-0803

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0002 — API-WCP-0002

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0804 — API-WCP-0804

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0805 — API-WCP-0805

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0806 — API-WCP-0806

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0807 — API-WCP-0807

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0808 — API-WCP-0808

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0809 — API-WCP-0809

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0810 — API-WCP-0810

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0811 — API-WCP-0811

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0812 — API-WCP-0812

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0813 — API-WCP-0813

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0003 — API-WCP-0003

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0007 — API-WCP-0007

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0814 — API-WCP-0814

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0815 — API-WCP-0815

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0816 — API-WCP-0816

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0901 — API-WCP-0901

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0902 — API-WCP-0902

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0903 — API-WCP-0903

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0904 — API-WCP-0904

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0905 — API-WCP-0905

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0906 — API-WCP-0906

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0004 — API-WCP-0004

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0005 — API-WCP-0005

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1001 — API-WCP-1001

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1002 — API-WCP-1002

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1003 — API-WCP-1003

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1004 — API-WCP-1004

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1005 — API-WCP-1005

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1006 — API-WCP-1006

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1007 — API-WCP-1007

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1008 — API-WCP-1008

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1009 — API-WCP-1009

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked
- post-dispatch uncertainty → UNKNOWN_OUTCOME and reconciliation before destructive retry; never assume timeout means definite failure

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1010 — API-WCP-1010

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0006 — API-WCP-0006

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-0008 — API-WCP-0008

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-API-1011 — API-WCP-1011

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** API Contract 1.0.

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

**Owner-approved logical expected / negative scenarios (execution fixtures not yet run):**
- correct authenticated authorized scope → returns logical response shape from existing API contract; precise route/HTTP status not invented
- unknown request field or malformed logical request → VALIDATION_FAILED and no mutation
- wrong origin surface/unauthenticated principal → no unauthorized data disclosure or effect; safe AUTHENTICATION_REQUIRED/AUTHORIZATION_DENIED projection as applicable
- valid auth but missing base permission/entitlement/policy gate → DENY, no effect, security/audit evidence as required
- cross-tenant or revoked delegation → DENY with no tenant leak
- missing required Idempotency-Key for mutation → reject before effect; same key with different semantic payload → IDEMPOTENCY_CONFLICT
- same key + same semantic payload → same logical result without duplicate external mutation; exact DB persistence currently blocked

**Forbidden side effects:** unauthorized OS or DB mutation; privileged fallback; cross-tenant data disclosure; secret/token/raw credential log; fabricated success on uncertain external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not present / no executable API route
- Database #6 for durable job/outbox/transaction assertions where applicable
- Security S01–S04 for exact session/CSRF/Origin/cookie topology
- API owner H01–H10 exact HTTP transport binding when activated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no SC-M01 implementation exists. HTTP route/method/status/DTO assertions remain `BLOCKED` where the activated owner contract does not define them; logical API contract assertions above are accepted.
**Planned evidence:** Exact implementation SHA and environment; request + safe response; durable state/audit/trace assert when applicable; all reruns/attempts retained.
**Actual execution:** `NONE` — no PASS/FAIL.


## PART III — 47 owner-approved IPC-B test specification records (not executed)

IPC-B/1 already defines exact 47 `ipc_operation_id` names, the paired action, the source field-name schema and no generic primitive. The per-field **value types** and real authorization/reconciliation are NOT validated in these P1 proposals. Each listed scenario is not a final accepted TEST-IPC case.

### TEST-IPC-1001 — IPC-WCP-1001

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1002 — IPC-WCP-1002

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1003 — IPC-WCP-1003

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1004 — IPC-WCP-1004

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1005 — IPC-WCP-1005

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1006 — IPC-WCP-1006

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1007 — IPC-WCP-1007

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1008 — IPC-WCP-1008

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1009 — IPC-WCP-1009

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1010 — IPC-WCP-1010

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1011 — IPC-WCP-1011

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1012 — IPC-WCP-1012

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1013 — IPC-WCP-1013

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1014 — IPC-WCP-1014

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1015 — IPC-WCP-1015

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1016 — IPC-WCP-1016

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Security/API #91 exact parent permission/action/target binding owner decision
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1017 — IPC-WCP-1017

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1018 — IPC-WCP-1018

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1019 — IPC-WCP-1019

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1020 — IPC-WCP-1020

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1021 — IPC-WCP-1021

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1022 — IPC-WCP-1022

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1023 — IPC-WCP-1023

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1024 — IPC-WCP-1024

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1025 — IPC-WCP-1025

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1026 — IPC-WCP-1026

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1027 — IPC-WCP-1027

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1028 — IPC-WCP-1028

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1029 — IPC-WCP-1029

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1030 — IPC-WCP-1030

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1031 — IPC-WCP-1031

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1032 — IPC-WCP-1032

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1033 — IPC-WCP-1033

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1034 — IPC-WCP-1034

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1035 — IPC-WCP-1035

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1036 — IPC-WCP-1036

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1037 — IPC-WCP-1037

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1038 — IPC-WCP-1038

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1039 — IPC-WCP-1039

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1040 — IPC-WCP-1040

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1041 — IPC-WCP-1041

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1042 — IPC-WCP-1042

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1043 — IPC-WCP-1043

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1044 — IPC-WCP-1044

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1045 — IPC-WCP-1045

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1046 — IPC-WCP-1046

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.

### TEST-IPC-1047 — IPC-WCP-1047

**Status:** `PLANNED / NOT_EXECUTED`; QA owner decision `ACCEPT`. **Level:** CONTRACT + SECURITY. **Version:** IPC-B/1.

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

**Owner-approved positive/negative scenarios:**
- authentic wcpd peer + fresh trusted authorization context + exact action/operation/resource binding → only registered bounded primitive eligibility; no caller-chosen command
- unknown operation or malformed version/framing/JSON/schema → no privileged dispatch, no OS/DB mutation and recorded security evidence
- wrong Unix peer/service context, wrong surface, missing/revoked auth context or cross-tenant target → fail closed before privileged effect
- caller-supplied arbitrary command/executable/absolute path/env/sudo/systemctl/signal → reject
- stale resource revision/fencing or duplicate destructive dispatch → reject/reconcile without blind retry
- uncertain post-dispatch result → UNKNOWN_OUTCOME with reconciliation_required, no fictional success

**Forbidden side effects:** any unregistered privileged primitive execution; dynamic shell/systemctl/sudo proxy; cross-tenant resource mutation; secret/credential/log leakage; un-audited external effect.

**Execution dependencies / blocked subassertions:**
- SC-M01 implementation not yet authorized
- Security peer+service and target authorization require actual wcpd/wcp-privd components
- Database #6 when job/lease/idempotency/audit/reconciliation persistence is implicated

**Executable fixture status:** `PLANNED / NOT_EXECUTED` — no live SC-M01 privileged implementation exists; inert research parsers are not product effect tests. Dependency-specific persistence/handler assertions listed above remain `BLOCKED`.
**Planned evidence:** Exact candidate SHA; safe peer/permission denial, operation effect instrumentation, correlated audit/job/trace, replay/recovery attempt history.
**Actual execution:** `NONE` — no PASS/FAIL.


## PART IV — N01..N26 privileged-negative labels with QA-approved behavioral expectations

The N01–N26 labels and original text below match the final Security v1.0 §14 catalog. TESTS / QA approves the behavioral expectation and forbidden-effect assertion for each label. These labels remain Security catalog labels, not newly invented canonical TEST IDs. Exact error-code projections remain `BLOCKED` unless an applicable owner-final API/IPC/Security contract fixes that code.

### N01 — unauthorized Unix peer

**Original Security v1.0 catalog label:** unauthorized Unix peer.

**QA-approved test title:** IPC peer authentication. **QA-approved negative behavior:** SO_PEERCRED must match exact wcpd UID and expected service identity; unknown peer refused.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `IPC_CALLER_UNTRUSTED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N02 — wrong surface

**Original Security v1.0 catalog label:** wrong surface.

**QA-approved test title:** Surface isolation. **QA-approved negative behavior:** Wrong USER/ADMIN surface claim or peer/session cross-over is denied, no privilege fallback.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `AUTHORIZATION_DENIED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N03 — bad permission/action binding

**Original Security v1.0 catalog label:** bad permission/action binding.

**QA-approved test title:** Typed authorization binding. **QA-approved negative behavior:** Action/permission/IPC operation tuple mismatch denies dispatch, does not fall back by string similarity.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `AUTHORIZATION_DENIED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N04 — cross-Tenant target

**Original Security v1.0 catalog label:** cross-Tenant target.

**QA-approved test title:** Tenant ownership. **QA-approved negative behavior:** Target resource not owned/authorized by trusted Tenant context denied.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `AUTHORIZATION_DENIED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N05 — invalid ownership/membership/delegation

**Original Security v1.0 catalog label:** invalid ownership/membership/delegation.

**QA-approved test title:** Delegation and ownership. **QA-approved negative behavior:** Missing/expired/revoked delegation or membership never supplies substitute authority.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `AUTHORIZATION_DENIED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N06 — malformed schema

**Original Security v1.0 catalog label:** malformed schema.

**QA-approved test title:** IPC-B strict schema. **QA-approved negative behavior:** Malformed frame, duplicate key, malformed UTF-8, unknown fields or invalid types rejected before side effect.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `IPC_SCHEMA_INVALID`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N07 — unknown IPC operation

**Original Security v1.0 catalog label:** unknown IPC operation.

**QA-approved test title:** IPC-B allowlist. **QA-approved negative behavior:** Unknown IPC operation rejected with no dynamic fallback.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `IPC_OPERATION_NOT_ALLOWED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N08 — arbitrary command/executable

**Original Security v1.0 catalog label:** arbitrary command/executable.

**QA-approved test title:** Generic command execution ban. **QA-approved negative behavior:** Caller-provided shell/argv/executable/command/sudo invocation cannot be dispatched.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `IPC_OPERATION_NOT_ALLOWED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N09 — path traversal

**Original Security v1.0 catalog label:** path traversal.

**QA-approved test title:** Filesystem path traversal. **QA-approved negative behavior:** Traversal/absolute paths outside typed managed registry forbidden.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `VALIDATION_FAILED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N10 — symlink/mount/TOCTOU escape

**Original Security v1.0 catalog label:** symlink/mount/TOCTOU escape.

**QA-approved test title:** Filesystem TOCTOU. **QA-approved negative behavior:** Symlink/mount swap and path escape denied at use time; no outside-target mutation.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `VALIDATION_FAILED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N11 — arbitrary signal

**Original Security v1.0 catalog label:** arbitrary signal.

**QA-approved test title:** Signal boundedness. **QA-approved negative behavior:** Caller cannot supply signal number or arbitrary process signal.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `VALIDATION_FAILED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N12 — arbitrary systemd unit

**Original Security v1.0 catalog label:** arbitrary systemd unit.

**QA-approved test title:** Service registry. **QA-approved negative behavior:** Caller cannot provide arbitrary systemd unit name; only registered service_id.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `IPC_OPERATION_NOT_ALLOWED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N13 — arbitrary package argv/tampered plan

**Original Security v1.0 catalog label:** arbitrary package argv/tampered plan.

**QA-approved test title:** Package plan integrity. **QA-approved negative behavior:** Tampered plan, arbitrary argv/package selection rejected.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `VALIDATION_FAILED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N14 — duplicate/idempotency violation

**Original Security v1.0 catalog label:** duplicate/idempotency violation.

**QA-approved test title:** Durable idempotency. **QA-approved negative behavior:** Same key/different request conflicts; duplicate same semantic request does not repeat effects.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `IDEMPOTENCY_CONFLICT`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N15 — timeout/lost ACK → UNKNOWN_OUTCOME

**Original Security v1.0 catalog label:** timeout/lost ACK → UNKNOWN_OUTCOME.

**QA-approved test title:** Lost acknowledgment. **QA-approved negative behavior:** Post-dispatch ambiguous effect becomes UNKNOWN_OUTCOME and requires reconciliation, never definite FAIL/success.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `UNKNOWN_OUTCOME`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N16 — concurrent conflict

**Original Security v1.0 catalog label:** concurrent conflict.

**QA-approved test title:** Concurrency fencing. **QA-approved negative behavior:** Stale expected revision/fencing and concurrent owner race do not overwrite newer state.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `CONCURRENCY_CONFLICT`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N17 — mandatory audit unavailable

**Original Security v1.0 catalog label:** mandatory audit unavailable.

**QA-approved test title:** Mandatory audit durability. **QA-approved negative behavior:** Failure to commit required audit/outbox evidence prevents committed pure DB mutation; external effects retain durable uncertainty evidence.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `DEPENDENCY_FAILURE`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N18 — reconciliation after uncertainty

**Original Security v1.0 catalog label:** reconciliation after uncertainty.

**QA-approved test title:** Reconciliation after uncertainty. **QA-approved negative behavior:** No destructive re-dispatch before persisted observed-state reconciliation.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `UNKNOWN_OUTCOME`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N19 — secret echo

**Original Security v1.0 catalog label:** secret echo.

**QA-approved test title:** Secret redaction. **QA-approved negative behavior:** Secrets/cookies/raw tokens/keys not echoed in response/log/audit/events.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `INTERNAL_ERROR`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N20 — parameter boundary

**Original Security v1.0 catalog label:** parameter boundary.

**QA-approved test title:** Typed parameter bounds. **QA-approved negative behavior:** Oversized or malformed parameter values rejected before privileged operation.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `VALIDATION_FAILED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N21 — arbitrary SQL

**Original Security v1.0 catalog label:** arbitrary SQL.

**QA-approved test title:** Hosted database command safety. **QA-approved negative behavior:** Untrusted arbitrary SQL forbidden in database privileged operation.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `VALIDATION_FAILED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N22 — stale PID/PID reuse

**Original Security v1.0 catalog label:** stale PID/PID reuse.

**QA-approved test title:** Process identity revalidation. **QA-approved negative behavior:** Stale PID or PID reuse cannot terminate another process; require registered instance identity.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `CONCURRENCY_CONFLICT`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N23 — unregistered service ID

**Original Security v1.0 catalog label:** unregistered service ID.

**QA-approved test title:** Service identifier registry. **QA-approved negative behavior:** Unregistered service_id not mapped to dynamic systemd command.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `IPC_OPERATION_NOT_ALLOWED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N24 — invalid/stale resource ID

**Original Security v1.0 catalog label:** invalid/stale resource ID.

**QA-approved test title:** Resource identifier validation. **QA-approved negative behavior:** Unknown/stale/resource cross-tenant identity rejected, no target substitution.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `RESOURCE_NOT_FOUND`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N25 — destructive blind retry

**Original Security v1.0 catalog label:** destructive blind retry.

**QA-approved test title:** Destructive retry safety. **QA-approved negative behavior:** UNKNOWN_OUTCOME or lost response cannot automatically replay destructive operation.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `UNKNOWN_OUTCOME`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.

### N26 — privilege downgrade/fallback

**Original Security v1.0 catalog label:** privilege downgrade/fallback.

**QA-approved test title:** Privilege downgrade/fallback. **QA-approved negative behavior:** No fallback from privileged to generic/root shortcut or ambiguous origin identity.

**Error classification candidate (NON-NORMATIVE UNTIL SOURCE-FIXED):** `AUTHORIZATION_DENIED`. Exact error/transport projection remains `BLOCKED` unless the activated owner-final contract defines it.

**Forbidden side effects:** no unauthorized privileged OS/filesystem/service/db mutation; no cross-tenant data exposure; no sensitive secret leakage.

**Evidence to retain once runnable:** exact actor/surface/tenant/peer context; correct rejection component and error; instrumented zero-effect assertion; redacted immutable security/audit evidence; tested SHA/environment and failure attempt history.

**Dependencies:** Security / IPC owner target-specific and exact error/denial semantics; SC-M01 implementation + E3 review.
**Status:** `PLANNED / NOT_EXECUTED`; behavioral expectation `ACCEPT`; exact error projection `BLOCKED` when not source-defined.


## PART V — 40 QA-approved WEB security per-ID scenario mappings

**Owner mapping decision:** Security v1.0 owns the reserved `TEST-SEC-WEB-001..040` range and thematic requirements. TESTS / QA now owns and approves the one-to-one number-to-scenario mapping below. This is a QA mapping decision; it is not represented as a historical Security per-ID assignment.

### TEST-SEC-WEB-001 — Wrong USER cookie on ADMIN

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** USER cookie must never authenticate ADMIN even when browser presents it; no fallback to USER identity.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-002 — Wrong ADMIN cookie on USER

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** ADMIN cookie must never authenticate USER or elevate USER scope.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-003 — Both surface credentials

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** When both cookies exist, backend uses exact current-origin surface and must not heuristically select a credential.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-004 — Cookie attributes on both surfaces

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Both distinct __Host- cookies require Secure, HttpOnly, SameSite=Strict, Path=/, host-only, no Domain.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-005 — Independent server session namespaces

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** USER and ADMIN sessions have different server-side namespaces, surface claims and Unix runtime identities.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-006 — Session fixation

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Login always rotates opaque unpredictable ≥128-bit session identifier; old fixed session cannot authenticate.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-007 — Authentication-strength rotation

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** MFA/auth-strength change rotates existing session ID and invalidates stale credential.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-008 — Privilege/impersonation rotation

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Elevation or impersonation start/end rotates credentials and separates child context.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-009 — Idle timeout

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** USER idle session expires after 30 min, ADMIN idle session after 15 min (evaluate server-side).

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-010 — Absolute lifetime

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** USER absolute session lifetime ≤12h, ADMIN ≤8h; no renewal past absolute expiration.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-011 — Remember-me prohibition

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** USER remember-me disabled at baseline, ADMIN remember-me forbidden.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-012 — Admin MFA login

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** WCP-ADMIN login must require MFA before authorized session use.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-013 — Fresh MFA elevated action

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** ADMIN high-impact actions require fresh MFA ≤5min; USER high-risk when required ≤10min.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-014 — Privileged child expiry

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** ADMIN privileged child context idle ≤5min absolute ≤15min; never persistent role grants.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-015 — Break-glass duration

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** ADMIN break-glass ≤15min without automatic extension; requires explicit scope/audit.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-016 — CSRF token absent

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** State-changing browser request without valid synchronizer CSRF token receives 403, no mutation and security evidence.

**Safe HTTP status:** 403 fixed by canonical source.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-017 — CSRF token wrong

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** State-changing browser request with wrong CSRF/X-WCP-CSRF token receives 403, no mutation.

**Safe HTTP status:** 403 fixed by canonical source.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-018 — Origin absent

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** State-changing browser request with missing exact Origin receives 403, no mutation.

**Safe HTTP status:** 403 fixed by canonical source.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-019 — Origin mismatched

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** State-changing browser request with other-origin value receives 403, no mutation.

**Safe HTTP status:** 403 fixed by canonical source.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-020 — No unsafe GET/HEAD/OPTIONS

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** GET, HEAD and OPTIONS cannot produce business-state mutation.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-021 — Credentialed wildcard CORS

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Session API does not allow wildcard credentialed cross-origin request; default is same-origin.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-022 — Untrusted HTML injection

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Untrusted content context-encoded, not executed as script; approved rich HTML sanitized.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-023 — Forbidden DOM execution APIs

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** No untrusted innerHTML/v-html/eval/new Function/javascript: URL/data-derived event-handler injection.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-024 — CSP nonces and prohibited directives

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Per-response CSP nonce for script/style; no unsafe-eval/global unsafe-inline/unapproved external script.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-025 — Frame exclusion

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** frame-ancestors none and X-Frame-Options DENY prevent framing.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-026 — SSRF target scope

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** WCP-USER rejects loopback/private/link-local/metadata/unapproved non-global targets.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-027 — SSRF unsupported schemes

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Reject file/ftp/gopher/data/javascript/unix and unapproved scheme variants.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-028 — SSRF DNS rebinding

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Resolve and validate every target DNS IP before connection; reject non-global DNS changes.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-029 — SSRF redirect policy

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Max three redirects; each redirect reparsed, re-resolved and revalidated.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-030 — Admin diagnostics registered endpoints

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** WCP-ADMIN internal diagnostics only by registered server/node/service/endpoint ID, not arbitrary URL.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-031 — Open redirect external/bypass

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Reject external, scheme-relative, encoded/backslash/userinfo-host redirects; allow internal named/normalized same-origin routes only.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-032 — Upload unsafe archive and staging

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Reject traversal, symlink/hardlink/device entries, client filename as path authority, executable staging or protected WCP directory.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-033 — Download reauthorization

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Authorize every download at request time, no raw browser path or cross-tenant disclosure.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-034 — SHOW_ONCE secret retrieval

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** SHOW_ONCE response Cache-Control no-store; never place secret in log/audit/notification/export.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-035 — NEVER_REVEAL secret retrieval

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** NEVER_REVEAL has no retrieval endpoint; never expose credentials or private keys.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-036 — Safe error projection

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Normal errors only stable safe error_code/message/correlation_id/field details; no stack trace/SQL/path/command/token.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-037 — Authenticated caching

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Authenticated HTML/sensitive APIs use Cache-Control no-store.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-038 — Logout and delegation revocation

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Logout revokes server session/CSRF/channels and ADMIN privilege/impersonation/break-glass; delegation revocation enforced from current state.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-039 — Reseller privilege non-escalation

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Reseller delegated access does not imply Tenant ownership or permit ADMIN privileges; plan/policy deny not bypassed.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.

### TEST-SEC-WEB-040 — Rate limit never grants access

**Source reference:** Security Baseline v1.0 §13/§14; `TEST-SEC-WEB-001..040` is a reserved range, NOT a ratified per-ID behavior map.

**QA-approved expected behavior:** Security endpoint rate-limit class enforced in addition to authorization, no account enumeration or rate-limit-as-auth shortcut.

**Safe HTTP status:** NO specific HTTP status invented; QA/API to ratify.

**Execution fixture:** `PLANNED / NOT_EXECUTED` — instantiate the applicable browser/origin/cookie/session/API topology for the exact candidate SHA. Topology-specific assertions remain `BLOCKED` only when the activated owner contract leaves that topology unspecified.

**Forbidden effects:** no cross-surface session privilege; no unauthorized business or system mutation; no sensitive secret/log/cookie leakage.

**Evidence:** tested SHA, browser/request/response safe trace; server-side accepted surface and session result; instrumented no-unauthorized-effect assertion; audit/safe security event and attempt history.

**Dependencies:** SC-M01 actual implementation; any topology-specific owner decision explicitly required by the activated slice.
**Status:** `READY / NOT_EXECUTED`; one-to-one mapping `OWNER APPROVED — TESTS / QA`.


## PART VI — Final QA disposition of the 85 historical proposal labels

The 85 historical rows were reviewed against their exact source wording. Their verbatim historical bodies are intentionally **not republished** in this canonical artifact. Provenance stays in Git; this section records only the owner disposition. No label below becomes a new TEST ID unless explicitly stated (none are created here).

| Label | QA disposition | Canonical reuse / merge target | Blocking dependency |
|---|---|---|---|
| `C01` | **MERGE** | TEST-API-0802 + TEST-IPC-1029 | NONE |
| `C02` | **MERGE** | TEST-API-0802/0803 + TEST-IPC-1029/1030 + N03 | NONE |
| `C03` | **MERGE** | TEST-API-0803 + TEST-IPC-1030 + N15/N25 | NONE |
| `C04` | **MERGE** | N03 + affected TEST-API typed authorization binding | NONE |
| `C05` | **MERGE** | TEST-API-0809 + TEST-IPC-1025/1026 | NONE |
| `C06` | **MERGE** | TEST-API-0809 + TEST-IPC-1026 + N16/N24 | NONE |
| `C07` | **MERGE** | TEST-API-0305 + TEST-IPC-1023/1024 + N21 | NONE |
| `C08` | **MERGE** | TEST-API-0305 + TEST-IPC-1023/1024 + N04/N05 | NONE |
| `C09` | **MERGE** | TEST-IPC-1024 + N03 | NONE |
| `C10` | **MERGE** | N05 + authorization revalidation | Database #6 for durable enqueue/effect-state fixture where required |
| `C11` | **BLOCKED** | NONE | No owner-final applications.runtime.update → linux_user.delete parent workflow mapping; do not invent it. |
| `C12` | **MERGE** | TEST-SEC-WEB-013 + authorization negative suite | NONE |
| `C13` | **MERGE** | TEST-API-0305 + TEST-IPC-1023 + N03 | NONE |
| `C14` | **MERGE** | TEST-IPC-1023/1024 + N03/N05 | NONE |
| `C15` | **MERGE** | N05/N24 + IPC authorization-context rejection | NONE |
| `C16` | **MERGE** | N16 + affected IPC stale-revision/concurrency assertions | NONE |
| `C17` | **MERGE** | N15/N18/N25 + affected IPC UNKNOWN_OUTCOME/reconciliation assertions | NONE |
| `TQ01` | **MERGE** | IPC connect-timeout contract + no-dispatch assertion | NONE |
| `TQ02` | **MERGE** | IPC framing/I-O timeout + N06 | NONE |
| `TQ03` | **MERGE** | IPC pre-dispatch deadline + zero-effect assertion | deadline representation/budget composition remains BLOCKED if not fixed by activated API/IPC owner contract |
| `TQ04` | **MERGE** | IPC operation-timeout cap | deadline/budget composition remains BLOCKED if not fixed by activated API/IPC owner contract |
| `TQ05` | **MERGE** | IPC I/O-vs-operation-timeout assertion | deadline/budget composition remains BLOCKED if not fixed by activated API/IPC owner contract |
| `TQ06` | **MERGE** | IPC pre-dispatch timeout/no-effect | deadline representation/budget composition remains BLOCKED if not fixed by activated API/IPC owner contract |
| `TQ07` | **MERGE** | N15/N18/N25 + applicable TEST-IPC UNKNOWN_OUTCOME | NONE |
| `TQ08` | **MERGE** | N14/N25 + applicable idempotency contract | NONE |
| `TQ09` | **MERGE** | N16 + applicable concurrency/claim test | Database #6 for physical claim/fencing persistence |
| `TQ10` | **MERGE** | N18/N25 + reconciliation test | Database #6 for physical durable recovery state |
| `CQ01` | **MERGE** | N01 + control-peer rejection | NONE |
| `CQ02` | **MERGE** | N02 + WCP-USER/WCP-ADMIN boundary | NONE |
| `CQ03` | **MERGE** | N02/N03 + typed surface/context assertion | NONE |
| `CQ04` | **MERGE** | N03/N05 + fail-closed authorization context | NONE |
| `CQ05` | **MERGE** | N24 + resource-binding assertion | NONE |
| `CQ06` | **MERGE** | N05 + authorization revalidation | NONE |
| `CQ07` | **MERGE** | N01/N26 + privileged boundary | NONE |
| `CQ08` | **BLOCKED** | NONE | Backend/worker → wcpd control-wire and worker path not owner-final for this historical scenario. |
| `CQ09` | **BLOCKED** | N16 + concurrency family | Database #6 physical job-claim/fencing contract required. |
| `CQ10` | **MERGE** | N07/N08 + no-generic-fallback assertion | NONE |
| `CQ11` | **BLOCKED** | N18 + recovery family | Database #6 physical jobs/intents/lease recovery contract required. |
| `CQ12` | **MERGE** | N03 + correlation-id non-authorization assertion | NONE |
| `HQ01` | **MERGE** | TEST-SEC-WEB-020 | NONE |
| `HQ02` | **MERGE** | Applicable TEST-API request-schema validation | NONE |
| `HQ03` | **MERGE** | Applicable TEST-API request-schema validation | NONE |
| `HQ04` | **MERGE** | N14 + mutating TEST-API idempotency assertions | NONE |
| `HQ05` | **MERGE** | N14 + mutating TEST-API idempotency assertions | NONE |
| `HQ06` | **BLOCKED** | NONE | Exact header/body dual-source idempotency transport rule is not owner-final; do not select historical option I2 by QA. |
| `HQ07` | **MERGE** | TEST-SEC-WEB-001/002 + authorization surface tests | NONE |
| `HQ08` | **MERGE** | TEST-SEC-WEB-016 | NONE |
| `HQ09` | **MERGE** | TEST-SEC-WEB-019 | NONE |
| `HQ10` | **BLOCKED** | NONE | Separate non-cookie token-auth/CSRF transport contract not owner-final for this scenario. |
| `HQ11` | **MERGE** | Applicable TEST-API strict-schema validation | NONE |
| `HQ12` | **MERGE** | Applicable list/cursor tenant-scope authorization assertion | NONE |
| `HQ13` | **MERGE** | Mutating TEST-API JobAccepted + durable-intent assertion | Database #6 for exact physical persistence proof. |
| `HQ14` | **MERGE** | TEST-SEC-WEB-036 | NONE |
| `HQ15` | **MERGE** | TEST-SEC-WEB-034/035 | NONE |
| `SQ01` | **MERGE** | TEST-SEC-WEB-001..005 | NONE |
| `SQ02` | **MERGE** | TEST-SEC-WEB-004/005 | NONE |
| `SQ03` | **MERGE** | TEST-SEC-WEB-002 | NONE |
| `SQ04` | **MERGE** | TEST-SEC-WEB-001 | NONE |
| `SQ05` | **MERGE** | TEST-SEC-WEB-003 | NONE |
| `SQ06` | **MERGE** | TEST-SEC-WEB-016/017 + surface isolation | NONE |
| `SQ07` | **MERGE** | TEST-SEC-WEB-019 | NONE |
| `SQ08` | **MERGE** | Authorization entitlement-negative coverage | NONE |
| `SQ09` | **MERGE** | N04/N05 + cross-tenant authorization | NONE |
| `SQ10` | **MERGE** | Authorization explicit-DENY coverage | NONE |
| `SQ11` | **MERGE** | Authorization override/deny coverage | NONE |
| `SQ12` | **MERGE** | Authorization security-gate coverage | NONE |
| `SQ13` | **MERGE** | TEST-SEC-WEB-039 | NONE |
| `SQ14` | **MERGE** | TEST-SEC-WEB-038 + N05 | NONE |
| `SQ15` | **MERGE** | Authorization permission/approval negative coverage | NONE |
| `SQ16` | **MERGE** | Authorization context-switch non-escalation coverage | NONE |
| `FQ01` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ02` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ03` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ04` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ05` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ06` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ07` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ08` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ09` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ10` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ11` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ12` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ13` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ14` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |
| `FQ15` | **BLOCKED** | A15/A16 build-toolchain family | Engineering #9 owner-final rules/toolchain gates are not yet canonical in main. |

Triage totals: **64 MERGE / 21 BLOCKED = 85**. Historical source rows verified byte-textually against their cited source files: **85 / 85**.


# FINAL OWNER APPROVAL

```text
OWNER = WCP360 — TESTS / QA
ARTIFACT = specifications/tests/WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
TEST BASELINE VERSION = v1.0
SCOPE = SC-M01 — FIRST IMPLEMENTATION FOUNDATION
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
PUBLICATION TYPE = P2 — FIRST CANONICAL MATERIALIZATION / OWNER APPROVAL
CONTENT CHANGE = INITIAL CANONICAL CONTENT
VERSION IMPACT = NONE — retain v1.0
A01–A20 = INDIVIDUALLY DECIDED
QI01–QI07 = INDIVIDUALLY DECIDED
REVIEW CASE RECORDS = 199
API RECORDS VERIFIED = 86 / 86
IPC RECORDS VERIFIED = 47 / 47
N LABELS VERIFIED = 26 / 26
WEB MAPPINGS APPROVED = 40 / 40
HISTORICAL PROPOSALS TRIAGED = 85 / 85
HISTORICAL TRIAGE = 64 MERGE / 21 BLOCKED
PRODUCT TESTS ACTUALLY EXECUTED = 0
RESULT PASS = 0
RESULT FAIL = 0
CODE-BASELINE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES AS QA SPECIFICATION INPUT; EXECUTION REMAINS SUBJECT TO MASTER AUTHORIZATION AND ACTIVATED DEPENDENCIES
BLOCKER TO QA OWNER PUBLICATION = NONE
MASTER GATE = 5/8 UNTIL MASTER VERIFIES THIS PUBLICATION
```

## Independent / cross-domain review still required

Owner approval is not an E3 independent review.

- **Security / Hardening:** independently review the final N01–N26 behavioral mapping, WEB001–040 mapping and any activated exact denial/error projection.
- **API / IPC / Contracts:** review activated HTTP transport assertions and operation-specific timeout/retry/idempotency/error projections where the logical contract is insufficient.
- **Database / Data Model:** review and unblock physical DB assertions once #6 is owner-final/materialized.
- **Engineering / Code Review:** review and unblock FQ/toolchain/build-quality assertions once #9 is owner-final/materialized.
- **Audit / Architecture Review:** independent exact-SHA verification remains required by the wider SC-M01 audit process before MASTER treats owner publication as independently verified.

This final owner approval does not execute product tests and does not authorize SC-M01 resume, SC-M02, or a CODE-BASELINE.
