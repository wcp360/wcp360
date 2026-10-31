# WCP360 — Current SC-M01 Readiness — 2026-10-03

**Status:** CURRENT COORDINATION / READINESS SNAPSHOT  
**Reference main:** \`13304bbcc6e982a5995cac724ed8174819e7d817\`  
**Gate:** issue #13  
**Audit tracker:** issue #41

This snapshot supersedes older conversational summaries. It does not supersede owner artifacts or change any baseline.

## 1. Executive state

\`\`\`text
WCP360-BM-0001 = FINAL

DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4
CODE-BASELINE = NONE

REQUIRED SC-M01 CANONICAL ARTIFACTS = 8
PRESENT IN specifications/ = 5
MISSING / NOT CANONICALLY MATERIALIZED = 3

SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
SOURCE CODE = PAUSED
\`\`\`

The project is materially better prepared than the raw 5/8 count suggests, but the gate must not be bypassed: Database #6, QA #7 and Engineering #9 remain owner actions.

## 2. Eight-artifact gate

| Artifact | Version | Present in main | Current state |
|---|---|---:|---|
| API Contract | 1.0 | YES | FINAL |
| IPC Contract | WCP360-IPC-B/1 | YES | FINAL |
| Permission Registry | v1.0 | YES | FINAL |
| Security Baseline | v1.0 | YES | FINAL |
| Database Model | v1.0 | **NO** | final metadata confirmed; exact final body unavailable |
| Test Baseline | v1.0 | **NO** | final owner artifact never confirmed as published; owner candidate prepared |
| DevOps Baseline | v1.0 | YES | FINAL |
| Engineering Ruleset | ENG-WCP-RULESET-0001 v0.2 | **NO** | final metadata confirmed; exact final body unavailable |

## 3. Database #6 — recovery exhausted, owner republication bounded

Canonical target:

\`specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md\`

Recovered evidence in main:

- \`governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md\`
- \`governance/handoffs/SC-M01-DB-CANONICAL-INPUT-PACK.md\`
- \`governance/handoffs/SC-M01-DB-OWNER-REPUBLICATION-CHECKLIST.md\`

Confirmed:

\`\`\`text
DB MODEL VERSION = v1.0
FINAL APPROVED CONTENT = YES
PHYSICAL CONTRACT = INCLUDED
READY FOR SOURCE CODE CONSUMPTION = YES
historical final body existed = YES
full exact body recoverable now = NO
\`\`\`

Recovered material is substantial, including schemas:

\`\`\`text
wcp_core
wcp_iam
wcp_infra
wcp_hosting
wcp_commercial
wcp_ops
wcp_audit
wcp_backup
\`\`\`

and many CORE/HOSTING/OPS/idempotency/outbox/lease/audit/backup fragments.

The recovery still does **not** justify canonical import because exact final physical details remain incomplete.

DB-R01→DB-R15 now bound the owner task:

- complete CORE/IAM/INFRA/HOSTING/COMMERCIAL/AUDIT/BACKUP definitions;
- all PK/FK/UNIQUE/CHECK/nullability/composite tenant constraints;
- delete exceptions;
- complete approved indexes;
- transaction boundaries;
- concurrency/locking/fencing;
- retention/partitioning;
- backup/restore;
- migration boundary.

Critical rule:

\`\`\`text
exact original recovered
→ CONTENT CHANGE = NONE may be used with provenance

owner reconstructs/amends missing content
→ CONTENT CHANGE = YES + version impact required
\`\`\`

MASTER/assistant must not reconstruct the missing canonical DB model.

## 4. QA #7 — owner candidate and exact coverage are ready

There is no evidence that a complete final SC-M01 QA artifact was historically published.

Current preparation in main:

- \`governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md\`
- \`governance/handoffs/SC-M01-QA-CANONICAL-INPUT-PACK.md\`
- \`governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.md\`
- \`governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json\`
- \`governance/handoffs/SC-M01-QA-OWNER-ACCEPTANCE-CHECKLIST.md\`

Mechanically verified identifier/reference coverage:

\`\`\`text
API contracts = 86
unique TEST-API refs = 86

IPC-B operations = 47
unique TEST-IPC refs = 47

ACTIVE permissions = 82
permissions represented in API bindings = 82

missing permissions = 0
unknown API permissions = 0
contract/test suffix mismatches = 0
\`\`\`

The QA owner candidate defines:

- PLANNED / READY / BLOCKED / PASS / FAIL;
- exact test-record schema;
- exact execution-evidence envelope;
- UNIT / CONTRACT / INTEGRATION / SECURITY / E2E / RECOVERY / INSTALL-BUILD;
- API, IPC, authorization, DB, browser security, negative security, recovery, DevOps and regression families;
- no fabricated PASS/FAIL.

A01→A20 now reduce the remaining owner task to explicit arbitration/publication.

Still missing:

\`\`\`text
TESTS / QA FINAL APPROVAL = NO
canonical specifications/tests artifact = NO
product/application test execution = 0
\`\`\`

Any earlier wording calling the coverage recount “independent” was corrected: the recount is E2 same-author evidence, not E3.

## 5. Engineering #9 — final metadata confirmed, exact rules missing

Canonical target:

\`specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md\`

Evidence/support:

- \`governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md\`
- \`governance/handoffs/SC-M01-ENGINEERING-CANONICAL-INPUT-PACK.md\`
- \`governance/handoffs/SC-M01-ENGINEERING-OWNER-REPUBLICATION-CHECKLIST.md\`

Confirmed:

\`\`\`text
RULESET = ENG-WCP-RULESET-0001
VERSION = v0.2
FINAL APPROVED CONTENT = YES
READY FOR SOURCE CODE CONSUMPTION = YES
historical final body = NOT fully recovered
\`\`\`

ENG-R01→ENG-R20 identify missing final details including:

- exact layer/dependency graph;
- language/component naming;
- errors/logging/configuration;
- runtime/framework selection if the final ruleset contained one;
- formatter/linter/static-analysis tools;
- complexity/duplication/coverage thresholds, if any;
- required CI/review/merge rules;
- Definition of Done;
- generated code;
- dependency policy;
- concurrency;
- release gates;
- SC-M01 consumption rules.

Critical rule:

**Cancelled Sprint 0 stack choices are not eligible defaults.**

Current evidence does not establish a final post-Sprint-0 web framework, PHP version, Go version, Node policy, package manager or concrete static-analysis toolchain.

## 6. API transport — semantic core final, HTTP projection incomplete

Canonical API 1.0 already fixes:

- semantic version 1.0 and breaking/additive rules;
- AUTH-U/A/B/P/PRIV/E profiles;
- RR/RK/RN retry/idempotency semantics;
- logical error_code registry and E-* profiles;
- unknown request field → VALIDATION_FAILED;
- T-READ/T-WRITE/T-JOB;
- logical response names;
- safe browser/session constraints through Security;
- sensitive/authenticated no-store rules.

Evidence:

- \`governance/evidence/SC-M01-CANONICAL-API-TRANSPORT-EVIDENCE.md\`
- \`governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.2.md\`

Still open:

- HTTP version representation;
- actual method/route per activated contract;
- path/query/header/body mapping;
- DTO fields/types/status;
- HTTP error-status/media map;
- scoped-token transport and multi-auth behavior;
- operation discriminators;
- pagination/cursor;
- any explicit non-sensitive cache exceptions.

AUDIT-WCP-0103 remains OPEN / PARTIALLY DEFINED.

## 7. Security Web/Authz — core semantics final, deployment/details open

Already final in Security/Permission:

- final AUTHZ is an intersection;
- permission resolution order:
  \`explicit DENY > account override > role grants > plan/feature envelope > default policy\`;
- WCP-USER/WCP-ADMIN session/cookie/namespace/origin separation;
- browser mutation = valid session + synchronizer CSRF + exact expected Origin;
- invalid/missing CSRF/Origin = 403/no mutation;
- reseller delegation ≤ effective reseller envelope and never transfers ownership;
- temporary privilege does not mutate roles;
- approval never replaces execution-time authorization;
- privileged context does not bypass action permission;
- unknown permission/action/IPC binding = DENY/no effect/audit.

Evidence:

- \`governance/evidence/SC-M01-CANONICAL-SECURITY-COMPOSITION-EVIDENCE.md\`
- \`governance/proposals/SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.2.md\`

Genuinely open:

- actual USER/ADMIN deployment topology;
- measurable cookie non-exposure criterion;
- simultaneous/wrong-cookie transport behavior;
- proxy/WSS details;
- positive account-override ceiling;
- internal NOT_GRANTED representation;
- physical delegation/privilege lifecycle;
- external safe error mapping.

AUDIT-WCP-0117 / 0118 remain OPEN / PARTIALLY DEFINED.

## 8. IPC timeout / UNKNOWN_OUTCOME / idempotency

Already final:

\`\`\`text
CONNECT TIMEOUT = 1s
I/O TIMEOUT = 5s
DEFAULT OPERATION TIMEOUT = 30s
MAX OPERATION TIMEOUT = 900s
deadline_ms field exists

timeout != definite failure
possible execution + unproven completion
→ UNKNOWN_OUTCOME
→ reconciliation
→ no blind retry
\`\`\`

Evidence:

- \`governance/evidence/SC-M01-CANONICAL-TIMEOUT-WORKER-EVIDENCE.md\`
- \`governance/proposals/SC-M01-RECOVERED-AUTHORIZATION-CONTEXT-OUTCOME.md\`
- \`governance/proposals/SC-M01-RECOVERED-TRACEABILITY-IDEMPOTENCY.md\`

Still open:

- exact I/O timeout phase/progress semantics;
- \`deadline_ms\` representation/clock/bounds;
- effective budget composition/start/end phases;
- pre-dispatch timeout/error boundary;
- durable idempotency representation/concurrency/retention, DB-dependent;
- exact result recovery after response loss.

AUDIT-WCP-0106/0107/0108 remain OPEN; DB-dependent portions wait for #6.

## 9. Control boundary / worker path

Already final:

- WCP-USER and WCP-ADMIN runtimes/identities are distinct;
- both may use \`wcp360-control\` transport access;
- transport access != application authorization;
- \`origin_surface\` exists in trusted context;
- \`wcpd\` is non-root control/orchestration daemon;
- \`wcpd\` validates origin permission/action binding;
- \`wcpd\` is the sole normal IPC-B client;
- \`wcp-privd\` enforces bounded technical operation/target;
- worker is non-root;
- worker has no supplementary control group by default;
- direct worker IPC-B is forbidden;
- worker privileged effects must use the normal control path.

Recovered owner consensus awaiting current republication:

\`\`\`text
control socket = /run/wcp360/wcpd.sock
owner = wcpd
group = wcp360-control
mode = 0660
worker direct control-socket access = denied by default
\`\`\`

Still open:

- exact peer→origin_surface binding/error;
- full control wire protocol C06;
- exact backend vs wcpd job/retry/reconciliation split;
- indirect worker→authorized-dispatch mechanism;
- trusted context storage/expiry/revocation/revalidation.

Evidence:

- \`governance/evidence/SC-M01-CANONICAL-CONTROL-BOUNDARY-EVIDENCE.md\`
- \`governance/proposals/SC-M01-CONTROL-BOUNDARY-DELTA-v0.3.md\`
- recovered M1 evidence in governance proposals.

## 10. Authorization bindings / traceability

Current canonical typed namespaces remain distinct:

\`\`\`text
permission_id
action_id
job_type
event_id
audit_event_id
audit_record_id
ipc_operation_id
error_code
\`\`\`

Security already says:

\`\`\`text
wcpd validates origin permission/action
wcp-privd enforces bounded ipc_operation_id/target
\`\`\`

Recovered evidence establishes traceability/idempotency core semantics, but explicit owner-ratified B01–B05 cross-domain mappings were **not** recovered.

Still open:

- B01–B05 current owner harmonization;
- D01 exact origin-action vs IPC-action meaning;
- D02 full context lifecycle;
- D03 API→IPC resource/revision/grant translation;
- D04 detailed parent/step audit persistence/event relationships.

No text similarity/prefix alias may authorize a primitive.

## 11. Toolchain/build

Final today:

\`\`\`text
OS = Debian 13
ARCH = amd64
PRODUCT ROOT = /usr/lib/wcp360
PostgreSQL = authoritative control-plane logical state
runtime trust boundaries = fixed
\`\`\`

Not finally ratified:

- application/web framework;
- PHP version/use;
- Go version;
- frontend/Node policy;
- package managers;
- migration tool;
- static-analysis tools;
- numeric coverage/complexity thresholds;
- canonical build/test commands.

Evidence:

\`governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md\`

F01–F10 remain owner decisions, many dependent on #9 and #6.

AUDIT-WCP-0113 remains OPEN.

## 12. Audit state

18 findings remain formally OPEN unless Audit/MASTER records verification.

Merged governance remediation made these candidates for verification:

- AUDIT-WCP-0101 — readiness/presence distinction;
- AUDIT-WCP-0115 — owner-domain authority vs repository precedence;
- AUDIT-WCP-0116 — SC-M01 foundation scope.

Closure criteria are in:

\`governance/audits/AUDIT-WCP-0100/CLOSURE-MATRIX.md\`

No same-author E2 check is relabeled as independent verification.

## 13. Repository guardrails / PR #42

PR #42 is the only active implementation/tooling PR at this snapshot.

Class:

\`\`\`text
R1 — repository tooling/CI
E1 automated evidence required
E3 independent Engineering/DevOps/Security review required
\`\`\`

Current re-synchronization candidate at snapshot preparation:

\`8aba04bc00c6068e5a281beda7da2aabd2b91422\`

This candidate is synchronized with main \`13304bbcc6e982a5995cac724ed8174819e7d817\`.

CI must be re-read for this exact SHA before claiming current-main compatibility.

Main branch protection remains reported:

\`\`\`text
protected = false
required status checks = none
\`\`\`

The installed GitHub integration does not expose the Administration permission required to configure that enforcement.

Therefore:

- CI can be implemented/tested;
- same-author evidence can be retained;
- branch enforcement cannot be claimed active;
- AUDIT-WCP-0109 remains OPEN;
- AUDIT-WCP-0110 remains OPEN until real E3 or a formally changed governance requirement.

## 14. SC-M01 implementation scope

Merged scope:

\`governance/SC-M01-SCOPE.md\`

When explicitly resumed, SC-M01 means first implementation foundation only:

- repository/bootstrap;
- separate WCP-USER/WCP-ADMIN skeletons;
- backend foundation;
- wcpd/wcp-privd bounded skeletons;
- DB/migration framework only with approved DB schema;
- config/secrets foundation;
- test harness;
- deterministic build/CI/package foundation.

It does not automatically authorize full DNS/mail/backup/HA/billing/database-hosting/etc modules or SC-M02.

## 15. Next gate-moving actions

Priority order:

1. **Database #6**
   - owner exact republication or explicit versioned reconstruction/amendment;
   - resolve DB-R01→DB-R15.

2. **QA #7**
   - owner reviews A01→A20;
   - publishes final v1.0 under \`specifications/tests/\`;
   - no fake PASS/FAIL.

3. **Engineering #9**
   - exact republication or explicit versioned reconstruction/amendment;
   - resolve ENG-R01→ENG-R20.

4. **Owner semantic harmonization**
   - B/D authorization;
   - H HTTP projection;
   - C control protocol;
   - T timeout/deadline budget;
   - S topology/override details;
   - F toolchain/build.

5. **PR #42**
   - current-main E1 CI proof;
   - E3 independent review;
   - merge only after policy requirements.

6. **MASTER cross-check**
   - 8/8 artifacts;
   - owner decisions required by the first component;
   - resume checklist.

Only then:

\`\`\`text
SC-M01 TARGETED EVIDENCE = SATISFIED
SC-M01 RESUME = AUTHORIZED
CODE-BASELINE = still NONE until implementation/test/review verification
\`\`\`

## 16. Current conclusion

\`\`\`text
DOCUMENTATION / GOVERNANCE CONSOLIDATION = ADVANCED
MISSING CANONICAL OWNER ARTIFACTS = 3
QA OWNER DRAFTING = OWNER-READY
DB / ENGINEERING RECOVERY = EXHAUSTED AND BOUNDED
SEMANTIC GAPS = MOSTLY NARROWED TO OWNER HARMONIZATION
REPOSITORY GUARDRAILS = OPEN / TESTED HISTORICALLY / CURRENT-SHA CI RECHECK PENDING
INDEPENDENT E3 REVIEW = NOT RECEIVED
BRANCH ENFORCEMENT = NOT ACTIVE

APPLICATION CODE = NOT STARTED
APPLICATION QA PASS/FAIL = NONE
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
\`\`\`
