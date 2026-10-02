# WCP360 — SC-M01 Owner Decision Matrix

Date : 2026-10-02. **Status: REVIEW COORDINATION — NON NORMATIVE.**

PR #43. Reference branch is synchronized with main `9c1a6cb62e3a81e75cbe40bf28671ea717421be4`.

Purpose: provide one bounded checklist for owners. This matrix does not approve a proposal, change a baseline, reserve test IDs, or authorize implementation.

## 1. Proposal set

| Area | Proposal | Owner decisions | QA cases |
|---|---|---:|---:|
| Authorization bindings | `SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.2.md` | D01–D04 | C01–C17 = 17 |
| IPC timeout/idempotency | `SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` | T01–T07 | TQ01–TQ10 = 10 |
| Control boundary | `SC-M01-CONTROL-BOUNDARY-DELTA-v0.1.md` | C01–C07 | CQ01–CQ12 = 12 |
| API transport | `SC-M01-API-TRANSPORT-DELTA-v0.1.md` | H01–H10 | HQ01–HQ15 = 15 |
| Security Web/Authz | `SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.1.md` | S01–S09 | SQ01–SQ16 = 16 |
| Toolchain / build inputs | SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md | F01–F10 | FQ01–FQ15 = 15 |
| **Total** | 6 active proposal files | **47 owner decisions** | **85 proposed cases** |

All 85 cases are PLANNED. Application tests executed by this PR = **0**.

## 2. Decision register

### API / IPC / CONTRACTS — primary

| ID | Decision | Required output | Status |
|---|---|---|---|
| D01 | semantics of origin action vs IPC action | accepted model + exact canonical section/version impact | OPEN |
| D02 | trusted authorization-context lifecycle | producer/resolver/binding/expiry/revocation | OPEN |
| D03 | API→IPC target/revision/grant translation | typed mapping and owner references | OPEN |
| D04 | parent/step audit and uncertain-result correlation | exact event/result relationships | OPEN |
| T01 | I/O timeout semantics | phase definition | OPEN |
| T02 | `deadline_ms` representation | absolute/relative/monotonic contract | OPEN |
| T03 | effective operation-budget scope | formula + start/end phases | OPEN |
| T04 | timeout vs UNKNOWN_OUTCOME boundary | deterministic outcome rules | OPEN |
| T05 | idempotency/identifier binding | relation between request/job/key/protection classes | OPEN |
| C03 | USER/ADMIN surface binding at control boundary | peer→surface/context constraints | OPEN |
| C05 | trusted context resolution in wcpd | normative lookup/verification path | OPEN |
| C06 | control request/response/job protocol | schema/version/error/idempotency contract | OPEN |
| C07 | backend vs wcpd responsibility boundary | explicit producer/consumer responsibilities | OPEN |
| H01 | HTTP versioning strategy | path/media/other choice + compatibility | OPEN |
| H02 | method/route binding | per activated API-WCP contract | OPEN |
| H03 | parameter locations | path/query/header/body mapping | OPEN |
| H04 | Idempotency-Key transport source | header-only vs dual representation | OPEN |
| H05 | DTO definitions | JobAccepted/Mutation/Collection/SecretCreated | OPEN |
| H06 | safe error format + HTTP status map | deterministic map | OPEN |
| H07 | session/token transport coexistence | auth selection + CSRF/CORS | OPEN |
| H08 | discriminated `operation` schemas | per operation required/forbidden fields | OPEN |
| H09 | pagination | cursor/order/filter binding | OPEN |
| H10 | cache classification | no-store/cacheable rules | OPEN |

### SECURITY / HARDENING — primary

| ID | Decision | Required output | Status |
|---|---|---|---|
| B01–B03 | parent bindings for service reload/restart/firewall apply | exact approved tuples | OPEN |
| B04–B05 | grants.set → apply/revoke step bindings | exact scope/target constraints | OPEN |
| C02 | transport access vs application authorization | fail-closed peer/context rules | OPEN |
| S01 | admissible USER/ADMIN topology | deployment rule, no port-only assumption | OPEN |
| S02 | cross-surface cookie handling | no fallback/alias behavior | OPEN |
| S03 | cross-surface CSRF/Origin/WSS behavior | exact reject/reauth rules | OPEN |
| S04 | definition of cookie non-exposure | measurable criterion for QA | OPEN |
| S05 | permission resolution vs final AUTHZ | two-stage or alternative deterministic algorithm | OPEN |
| S06 | account override ceiling | positive/negative limits | OPEN |
| S07 | reseller delegation composition | ceiling/scope/revocation algorithm | OPEN |
| S08 | temporary privilege/approval composition | non-bypass rules | OPEN |
| S09 | absent/DENY representation | internal decision + safe external behavior | OPEN |

### DEVOPS / BUILD / RELEASE — primary

Toolchain/build decisions:

| ID | Decision | Required output | Status |
|---|---|---|---|
| F03 | PostgreSQL version/client/migration policy | supported major/range + driver/tool | OPEN |
| F04 | dependency-source policy | registries/repos/pinning/checksums | OPEN |
| F05 | frontend/Node decision | explicit use/non-use + lock strategy | OPEN |
| F06 | canonical build/test entry points | commands and deterministic behavior | OPEN |
| F08 | build environment / CI runner | OS/image/privilege/network/cache policy | OPEN |
| F09 | configuration/generated artifacts | authority + reproducibility rules | OPEN |
| F10 | artifact identity/provenance/signing boundary | digest/provenance/trust policy | OPEN |

Engineering co-owns F01/F02/F04–F08; Security reviews supply-chain/secret/privilege constraints.

### ENGINEERING / CODE REVIEW — toolchain decisions

| ID | Decision | Required output | Status |
|---|---|---|---|
| F01 | application runtime/framework | language/framework/version/dependency manager | BLOCKED/PARTIAL ON #9 |
| F02 | wcpd/wcp-privd implementation language | language/runtime/version/compiler policy | BLOCKED/PARTIAL ON #9 |
| F04 | dependency lock policy | exact lock/restore expectations | BLOCKED/PARTIAL ON #9 |
| F06 | canonical developer/build entry points | owner-approved commands | BLOCKED/PARTIAL ON #9 |
| F07 | static analysis / quality gates | tools + thresholds/rules | BLOCKED ON #9 |
| F08 | build/CI quality expectations | review + environment constraints | BLOCKED/PARTIAL ON #9 |

### DEVOPS / BUILD / RELEASE — primary

| ID | Decision | Required output | Status |
|---|---|---|---|
| C01 | control socket physical identity | path/owner/group/mode/runtime lifecycle | OPEN |
| C04 | worker path to normal control boundary | W1/W2/other explicit design | OPEN |
| S01–S04 | deploy/routing/TLS/proxy behavior | topology evidence | OPEN |
| T01/T03 | socket timeout operational behavior | implementation constraints, not protocol redefinition | OPEN |

### DATABASE / DATA MODEL — dependent

| ID | Decision/reference | Blocking condition | Status |
|---|---|---|---|
| T06 | durable pre-effect identity/dedupe/intent/outbox properties | exact physical references require #6 | BLOCKED ON #6 |
| T07 | claim/recovery/fencing/reconciliation persistence | exact physical references require #6 | BLOCKED ON #6 |
| D03 | grant_set/revision/resource mappings | exact physical references require #6 where DB-backed | BLOCKED/PARTIAL |
| C04-W2 | durable worker/job path | only if W2 chosen; physical assertions require #6 | CONDITIONAL |
| S06/S07 | persisted overrides/delegations/entitlements | exact schema references require #6 | BLOCKED/PARTIAL |

No DDL, tables, indexes, locks, leases or migrations are defined by this matrix.

### TESTS / QA — final owner publication still required

QA must:

1. publish the final SC-M01 Test Baseline v1.0 artifact (#7);
2. map only accepted owner decisions to canonical TEST-* IDs;
3. define preconditions, exact expected result, prohibited side effects and evidence;
4. keep implementation-dependent results `PLANNED / READY / BLOCKED`;
5. never convert proposal cases to PASS without executable implementation evidence.

### ENGINEERING / CODE REVIEW

Engineering must publish the exact ruleset body (#9) and review implementation/repository changes on exact SHAs. PR #42 is not a substitute for `ENG-WCP-RULESET-0001 v0.2`.

### TESTS / QA — toolchain cases

Map FQ01–FQ15 only after owners decide F01–F10. These cases do not make QA #7 complete and remain PLANNED.

## 3. Decision response format

For each row an owner should respond with:

```text
DECISION ID =
OWNER =
REFERENCE EXAMINED = path + version + commit
DECISION = ACCEPT / AMEND / REJECT / ALREADY DEFINED
NORMATIVE EVIDENCE =
REQUIRED DELTA = NONE or exact change
VERSION IMPACT =
TESTS / EXPECTED EVIDENCE =
DEPENDENCIES =
STATUS = OPEN / ACCEPTED / FIX IN PROGRESS / VERIFIED / CLOSED
```

`ALREADY DEFINED` must include the exact existing source. It is preferable to a duplicate rule.

## 4. Merge / implementation rule

A documentation merge of PR #43 means only that proposal/review material is preserved in main.

It does **not** mean:

- D/T/C/H/S decisions are accepted;
- Security bindings are changed;
- an API route exists;
- a socket path is canonicalized;
- a DB structure is created;
- QA cases passed;
- SC-M01 resumes.

Implementation consumption requires the owner decision to be published in its normative domain and MASTER to apply the existing gate/checklist.

## 5. Toolchain note

The current repository fixes Debian 13 amd64, /usr/lib/wcp360, PostgreSQL as the control-plane authority and the privileged architecture. It does **not** yet fix the web framework/runtime, frontend toolchain, concrete runtime versions, package managers, static-analysis tools or quantitative quality thresholds.

Historical cancelled Sprint 0 stack choices are not eligible as defaults.

## 6. Gate snapshot

```text
CANONICAL REQUIRED ARTIFACTS PRESENT = 5 / 8

#6 DATABASE
owner final/source-ready metadata = YES
exact body materialized in GitHub = NO

#7 TESTS / QA
baseline metadata v1.0 = APPROVED
final canonical artifact publication = NOT MATERIALIZED / NOT CONFIRMED

#9 ENGINEERING
owner final/source-ready metadata = YES
exact body materialized in GitHub = NO

SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

This matrix reduces coordination ambiguity; it is not a ninth artifact and does not change the gate.
