# WCP360 — Current SC-M01 Readiness — 2026-10-04

**Status:** CURRENT COORDINATION / READINESS SNAPSHOT  
**Reference main:** 71f27d8787cee2b4c326b7fd4e45c37e64794b7d  
**Gate:** issue #13  
**Audit tracker:** issue #41  
**Previous snapshot:** governance/SC-M01-CURRENT-READINESS-2026-10-03.md

This snapshot supersedes older project-status summaries for current coordination only. It does not supersede canonical owner artifacts or change a baseline.

## 1. Executive state

    WCP360-BM-0001 = FINAL
    DOC-BASELINE = v4.0.21.23
    DESIGN-BASELINE = V2.5.3.36.4
    CODE-BASELINE = NONE

    REQUIRED SC-M01 CANONICAL ARTIFACTS = 8
    PRESENT / CONSUMABLE IN specifications/ = 5
    MISSING / NOT CANONICALLY MATERIALIZED = 3

    SC-M01 = PAUSED — TARGETED EVIDENCE
    SC-M02 = NOT AUTHORIZED
    SOURCE CODE = PAUSED

The project has substantial recovered evidence and owner-review preparation, but the 8/8 gate remains unchanged.

## 2. Eight-artifact gate

| Artifact | Version | Present | Current state |
|---|---|---:|---|
| API Contract | 1.0 | YES | FINAL |
| IPC Contract | WCP360-IPC-B/1 | YES | FINAL |
| Permission Registry | v1.0 | YES | FINAL |
| Security Baseline | v1.0 | YES | FINAL |
| Database Model | v1.0 | NO | historical final metadata + extensive partial recovery; exact complete body absent |
| Test Baseline | v1.0 metadata | NO | owner candidate + acceptance/conformance/index prepared; final QA owner artifact absent |
| DevOps Baseline | v1.0 | YES | FINAL |
| Engineering Ruleset | ENG-WCP-RULESET-0001 v0.2 | NO | historical final metadata + partial recovery; exact complete body absent |

## 3. Database #6

Current evidence in main:

- governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md
- governance/handoffs/SC-M01-DB-CANONICAL-INPUT-PACK.md
- governance/handoffs/SC-M01-DB-OWNER-REPUBLICATION-CHECKLIST.md
- governance/evidence/SC-M01-DB-REPUBLICATION-CONFORMANCE.md

Recovery conclusion:

    DB-R01..DB-R15 examined = 15
    some useful recovery path = 15 / 15
    exact-complete autonomous republication = 0 / 15

Substantial recovered evidence includes:

- schemas wcp_core / wcp_iam / wcp_infra / wcp_hosting / wcp_commercial / wcp_ops / wcp_audit / wcp_backup;
- many CORE/HOSTING/OPS/commercial/audit/backup table fragments;
- UUID/typed-registry/revision/state/tenant-aware invariants;
- jobs/idempotency/leases/outbox/audit/reconciliation concepts;
- outbox at-least-once and duplicate-safe consumer principles;
- no required v1.0 partitioning;
- distinct control-plane/hosted/workload backup classes;
- migration generation is future implementation, not canonical model publication.

Still insufficient for exact canonical import:

- complete columns/types/nullability;
- every PK/FK/composite tenant constraint;
- all UNIQUE/CHECK rules;
- all delete exceptions;
- complete approved index catalogue;
- exact transaction/locking/claim/fencing rules;
- retention policy details;
- complete backup/restore/migration text;
- original final wording/bytes.

Next valid owner action:

    retained exact source exists
    → exact republication + provenance

    retained source unavailable
    → Database owner reconstruction/amendment
    → real content delta + version impact
    → cross-domain review where required

MASTER/assistant does not reconstruct the canonical DB model.

## 4. QA #7

Current preparation in main:

- governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md
- governance/handoffs/SC-M01-QA-CANONICAL-INPUT-PACK.md
- governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.md
- governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json
- governance/handoffs/SC-M01-QA-OWNER-ACCEPTANCE-CHECKLIST.md
- governance/evidence/SC-M01-QA-OWNER-CANDIDATE-CONFORMANCE.md

Mechanically derived coverage:

    API contracts = 86
    unique TEST-API refs = 86
    IPC-B operations = 47
    unique TEST-IPC refs = 47
    ACTIVE permissions = 82
    permissions represented in API = 82
    missing/unknown permissions = 0
    contract/test suffix mismatches = 0

A01→A20 conformance result:

    owner acceptance topics = 20
    topics with prepared candidate/evidence path = 20
    QA owner ratification still required = 20

The candidate already covers:

- PLANNED / READY / BLOCKED / PASS / FAIL;
- test-record schema;
- execution evidence;
- UNIT / CONTRACT / INTEGRATION / SECURITY / E2E / RECOVERY / INSTALL-BUILD;
- API / IPC / authorization / DB / negative security / browser / recovery / DevOps / regression;
- N01–N26;
- TEST-SEC-WEB-001→040;
- 85 proposal-case intake;
- first CODE-BASELINE evidence.

Still missing:

    QA OWNER APPROVAL = NO
    FINAL specifications/tests ARTIFACT = NO
    PRODUCT APPLICATION PASS/FAIL = NONE

Important provenance rule:

Historical v1.0 metadata does not prove a prior complete final QA body existed.

QA must choose:
- exact retained prior source → exact republication;
- first complete canonical body from current owner review → first canonical materialization / real content decision;
- another version if owner determines version impact requires it.

No further broad QA draft is currently justified unless QA identifies a specific missing domain.

## 5. Engineering #9

Current evidence:

- governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md
- governance/handoffs/SC-M01-ENGINEERING-CANONICAL-INPUT-PACK.md
- governance/handoffs/SC-M01-ENGINEERING-OWNER-REPUBLICATION-CHECKLIST.md
- governance/evidence/SC-M01-ENGINEERING-REPUBLICATION-CONFORMANCE.md

Recovery conclusion:

    ENG-R01..ENG-R20 examined = 20
    historically useful theme/invariant evidence = substantial
    exact final v0.2 body = not recovered
    autonomous exact republication = not possible

Recovered constraints include architecture boundaries, no generic privileged execution, fail-closed authz, failure semantics, non-fake QA state, review themes and stale-baseline prohibition.

Still unrecovered as exact final owner rules:

- dependency/layer graph;
- per-language/component naming;
- error/log/config details;
- runtime/framework/toolchain selection;
- formatter/linter/static-analysis stack;
- complexity/duplication/coverage thresholds;
- required CI checks;
- reviewer count/settings;
- Definition of Done;
- generated artifact rules;
- release gates;
- final ordering/wording.

Cancelled Sprint 0 choices remain ineligible defaults.

Next owner action is exact retained-source republication or explicit reconstruction/amendment with version impact.

## 6. Provenance rule for missing artifacts

Current governance now states:

    CONTENT CHANGE = NONE

is **not** a universal gate requirement.

It is appropriate only for exact retained republication with owner provenance.

If missing material is reconstructed/amended, owner must declare the real delta and version impact.

If no prior complete final body existed, first canonical materialization must be classified honestly.

This applies directly to #6/#7/#9.

## 7. Semantic decision inventory

The owner decision matrix and evidence/proposals now separate already-FINAL semantics from genuinely open implementation projections.

Current major families:

- D/B — authorization/action/parent/primitive;
- T — timeout/deadline/idempotency/recovery;
- C — control boundary / worker / trusted context;
- H — HTTP API projection;
- S — Web/session/authorization details;
- F — toolchain/build.

The decision activation matrix prevents all open decisions from becoming generic blockers after eventual resume.

## 8. Decision activation after the global gate

governance/SC-M01-DECISION-ACTIVATION-MATRIX.md:

    A0 = global 8/8 gate
    A1 = toolchain/bootstrap
    A2 = positive control-plane path
    A3 = durable job/effect/recovery
    A4 = Web/session/authz
    A5 = HTTP endpoint
    A6 = feature/operation
    A7 = release/production packaging

Rules:

- A0 remains mandatory before any Source Code application implementation.
- After A0 + MASTER resume, an open semantic decision blocks the slice that needs it.
- Future-module decisions do not block unrelated foundation/bootstrap work.
- Source Code may never invent a missing decision for an activated slice.

## 9. API transport

Already final:

- semantic API version 1.0 and compatibility rules;
- AUTH-U/A/B/P/PRIV/E semantics;
- RR/RK/RN retry/idempotency semantics;
- logical error registry/profiles;
- strict unknown-field rejection;
- T-READ/T-WRITE/T-JOB;
- logical response names;
- browser CSRF/Origin baseline;
- safe error fields/redaction;
- sensitive/authenticated no-store.

Still open for activated HTTP endpoints:

- transport version representation;
- method/route;
- path/query/header/body serialization;
- exact DTO fields/status;
- HTTP status/media error map;
- scoped-token and multi-auth transport;
- operation discriminators;
- pagination/cursor;
- cacheable-read exceptions.

AUDIT-WCP-0103 remains OPEN / PARTIALLY DEFINED.

## 10. Security Web/Authz

Already final:

- final AUTHZ is an intersection;
- permission resolution order: explicit DENY > account override > role > plan/feature > default;
- DEMO additional hard restriction;
- reseller delegation ≤ effective reseller envelope and does not transfer ownership;
- temporary privilege does not mutate roles;
- approval does not replace execution-time authorization;
- privilege does not bypass action permission;
- unknown binding DENY/no side effect/audit;
- USER/ADMIN separate session/cookie/namespace/origin;
- browser mutation requires session + synchronizer CSRF + exact Origin;
- invalid/missing CSRF/Origin = 403/no mutation.

Still open:

- deployment topology/non-exposure proof;
- simultaneous/wrong-cookie transport handling;
- proxy/WSS detail;
- positive account-override ceiling;
- internal NOT_GRANTED distinction;
- physical delegation/privilege lifecycle;
- external safe error mapping.

AUDIT-WCP-0117/0118 remain OPEN / PARTIALLY DEFINED.

## 11. Timeout / idempotency / UNKNOWN_OUTCOME

Final values/semantics include:

    CONNECT TIMEOUT = 1s
    I/O TIMEOUT = 5s
    DEFAULT OPERATION TIMEOUT = 30s
    MAX OPERATION TIMEOUT = 900s
    deadline_ms field exists

    timeout != definite failure
    retry != reconciliation
    rollback != compensation

    possible dispatch/effect + unproven completion
    → UNKNOWN_OUTCOME
    → reconciliation
    → no blind retry

Still open:

- exact I/O phase/progress meaning;
- deadline_ms representation/clock/bounds;
- effective budget composition;
- pre-dispatch timeout projection;
- durable idempotency physical representation;
- exact claim/fencing/recovery persistence;
- result recovery after response loss.

Database-dependent physical parts remain blocked on #6.

## 12. Control boundary / worker

Final/canonical invariants:

- distinct USER/ADMIN identities/surfaces;
- wcp360-control is transport access, not application auth;
- origin_surface belongs to trusted context;
- wcpd non-root orchestration role;
- wcpd validates origin permission/action;
- wcpd sole normal IPC-B client;
- wcp-privd enforces bounded technical operation/target;
- worker non-root;
- worker has no supplementary control group by default;
- direct worker IPC-B forbidden;
- worker privileged effects use normal control path.

Recovered historical owner consensus:

    /run/wcp360/wcpd.sock
    owner = wcpd
    group = wcp360-control
    mode = 0660
    worker direct control-socket access = denied by default

This recovered socket tuple still requires current owner republication/confirmation before Source Code consumes it.

Still open:
- peer→origin_surface mismatch behavior;
- full control wire protocol;
- backend vs wcpd responsibility split;
- indirect worker path;
- trusted-context storage/expiry/revocation/revalidation.

## 13. Authorization / traceability

Typed identifier namespaces remain distinct.

Current Security principle:

    wcpd validates origin permission/action
    wcp-privd enforces bounded ipc_operation_id and target

Recovered/current evidence narrows traceability/idempotency, but B01–B05 explicit current owner harmonization remains unresolved.

No string similarity/prefix alias may authorize a primitive.

## 14. Toolchain / build

Final:

    OS = Debian 13
    ARCH = amd64
    PRODUCT ROOT = /usr/lib/wcp360
    PostgreSQL = authoritative control-plane logical state
    runtime trust boundaries = fixed

Not finally ratified:

- app/web framework;
- PHP use/version;
- Go version;
- Node/frontend policy;
- package managers;
- migration tool;
- static-analysis tools;
- numeric coverage/complexity thresholds;
- canonical build/test commands.

Do not infer cancelled Sprint 0 choices.

## 15. Audit

All 18 findings remain formally OPEN unless Audit/MASTER verifies closure.

Merged remediation makes 0101 / 0115 / 0116 candidates for Audit/MASTER verification.

Closure matrix:
governance/audits/AUDIT-WCP-0100/CLOSURE-MATRIX.md

## 16. Repository guardrails / PR #42

PR #42 remains the only open tooling implementation PR.

Current exact candidate:

    c10c6296868852f15ed600025b8b31d589135be7

Latest E1:

    run 37194191355 = SUCCESS
    merge-test = ffbafd88729ed62f2f0394f999241163950e2db7

Latest parsed evidence:

    base main tested = 305 files / 1,389,780 bytes
    candidate / merge-test = 322 files / 1,491,881 bytes
    mirror = 147 / 147
    audit = 4 / 4
    targeted secret findings = 0

Policy:

    E1 = satisfied
    E2 = satisfied
    E3 = missing
    main enforcement = not active

Independent review packet:
governance/reviews/PR42-E3-INDEPENDENT-REVIEW-PACKET.md

GitHub enforcement plan:
governance/GITHUB-MAIN-ENFORCEMENT-PLAN.md

The latest E1 was against main 1b828833... . The main branch is now 71f27d87... because review/enforcement documentation was merged after that run. Before #42 merge, rebase/synchronize and rerun E1 on the then-current main after any E3 fixes.

## 17. GitHub enforcement

Current observed:

    main protected = false
    required status checks = none

Connected GitHub tooling in this session exposes no supported branch-protection/ruleset update action.

Therefore:

    enforcement activated by assistant = NO

The activation plan defines post-#42 sequence and negative proofs ENF-T01→T06.

AUDIT-WCP-0109 remains OPEN.

## 18. Independent review

PR #42 requires E3 under current R1 policy.

E3 package is in main and bound to the current candidate/evidence.

No real E3 reviewer has yet been evidenced.

AUDIT-WCP-0110 remains OPEN.

## 19. Documentation publication

Documentary mirror:

    147 / 147 against committed manifest

Canonical physical DOC archive:

    WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation.zip
    exact physical source = still unavailable

Issue #16 remains separate and is not a substitute for the 8-artifact gate.

## 20. Current next actions

Gate-moving owner actions:

1. Database #6 — retained exact source or owner reconstruction/amendment.
2. QA #7 — A01→A20 owner review and final canonical publication.
3. Engineering #9 — retained exact source or owner reconstruction/amendment.

Process actions:

4. obtain E3 independent review for PR #42;
5. apply E3 fixes if any;
6. synchronize #42 to current main and rerun E1;
7. merge #42 only if policy permits;
8. run workflows successfully on main;
9. GitHub administrator activates branch enforcement;
10. execute negative enforcement proofs.

Then:

11. MASTER cross-checks 8/8;
12. selects first SC-M01 slice and verifies only its applicable normative decision set;
13. MASTER may issue SC-M01 resume.

## 21. Current conclusion

    DOCUMENTARY / GOVERNANCE CONSOLIDATION = ADVANCED
    CANONICAL ARTIFACTS = 5 / 8
    DB / ENGINEERING HISTORICAL SEARCH = EXHAUSTED ENOUGH TO STOP
    QA PREPARATION = OWNER-READY
    SEMANTIC GAPS = NARROWED AND ACTIVATION-SCHEDULED
    REPOSITORY GUARDRAILS = OPEN / E1 GREEN / E3 MISSING
    MAIN ENFORCEMENT = OFF

    APPLICATION IMPLEMENTATION = NOT STARTED
    PRODUCT QA PASS/FAIL = NONE
    SC-M01 = PAUSED — TARGETED EVIDENCE
    SC-M02 = NOT AUTHORIZED
    CODE-BASELINE = NONE
