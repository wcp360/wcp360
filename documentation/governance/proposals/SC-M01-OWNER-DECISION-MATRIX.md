# WCP360 — SC-M01 Owner Decision Matrix

Date : 2026-10-02. **Status: REVIEW COORDINATION — NON NORMATIVE.**

Reference main for this recovery pass: `1b8747f049164a9d5120fbf6f8d6b6be27987fa0`.

Purpose: provide one bounded checklist for owners. `SC-M01-CANONICAL-API-TRANSPORT-EVIDENCE.md` narrows H01/H03/H04/H05/H06/H07/H09/H10 using current FINAL API/Security semantics. `SC-M01-CANONICAL-TIMEOUT-WORKER-EVIDENCE.md` narrows T01/T02/T03 and C04 from current FINAL IPC/DevOps rules. This matrix does not approve a proposal, change a baseline, reserve test IDs, or authorize implementation.

Recovered historical evidence is tracked separately from current canonical authority. `SC-M01-RECOVERED-ACCOUNT-OVERRIDE-S06-EVIDENCE.md` records that no FINAL positive override ceiling was recovered, so S06 must not be inferred from candidate entitlement models. `SC-M01-CANONICAL-CONTROL-BOUNDARY-EVIDENCE.md` narrows C03/C07 from current FINAL Security/DevOps and leaves C06 as the genuinely unpublished wire protocol. `SC-M01-CANONICAL-SECURITY-COMPOSITION-EVIDENCE.md` additionally narrows S02/S03/S05/S07/S08/S09 from current FINAL Security/Permission artifacts. `SC-M01-RECOVERED-CONTROL-BOUNDARY-M1.md` contains verified final M1 owner consensus. `SC-M01-RECOVERED-AUTHORIZATION-BINDINGS.md` records that B01–B05/D01 lack separate explicit owner-ratification evidence. `SC-M01-RECOVERED-AUTHORIZATION-CONTEXT-OUTCOME.md` narrows C05/T04 using current canonical Security/IPC plus recovered final context evidence. `SC-M01-RECOVERED-TRACEABILITY-IDEMPOTENCY.md` narrows D04/T05/H04 using current API/IPC and recovered final traceability evidence.

## 1. Proposal set

| Area | Proposal | Owner decisions | QA cases |
|---|---|---:|---:|
| Authorization bindings | `SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.3.md` | D01–D04 = 4 OPEN; B01–B05 owner harmonization required | C01–C17 = 17 |
| IPC timeout/idempotency | `SC-M01-IPC-TIMEOUT-IDEMPOTENCY-DELTA-v0.1.md` | T01–T07 | TQ01–TQ10 = 10 |
| Control boundary | `SC-M01-CONTROL-BOUNDARY-DELTA-v0.3.md` | C01–C07 = 5 OPEN + 1 recovered/republication + 1 already defined | CQ01–CQ12 = 12 |
| API transport | `SC-M01-API-TRANSPORT-DELTA-v0.2.md` | H01–H10 | HQ01–HQ15 = 15 |
| Security Web/Authz | `SC-M01-SECURITY-WEB-AUTHZ-DELTA-v0.2.md` | S01–S09 | SQ01–SQ16 = 16 |
| Toolchain / build inputs | SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md | F01–F10 | FQ01–FQ15 = 15 |
| **Total** | 6 active proposal domains | **47 tracked decision points; 44 OPEN semantic decisions** | **85 proposed cases** |

All 85 cases are PLANNED. Application tests executed by this PR = **0**.

## 2. Decision register

### Decision-state summary

```text
TRACKED DECISION POINTS = 47
OPEN SEMANTIC OWNER DECISIONS = 44
RECOVERED OWNER CONSENSUS REQUIRING REPUBLICATION = 2  (C01, T01)
ALREADY DEFINED IN CURRENT CANONICAL ARTIFACTS = 1     (C02)
HISTORICAL ASSISTANT FREEZE / OWNER HARMONIZATION = B01–B05 + D01
PROPOSED QA CASES = 85
APPLICATION TEST EXECUTIONS = 0
```

### API / IPC / CONTRACTS — primary

| ID | Decision | Required output | Status |
|---|---|---|---|
| D01 | semantics of origin action vs IPC action | current typed/canonical identifiers + owner harmonization of origin→technical binding | OPEN |
| D02 | trusted authorization-context lifecycle | producer/resolver/binding/expiry/revocation | OPEN |
| D03 | API→IPC target/revision/grant translation | typed mapping and owner references | OPEN |
| D04 | parent/step audit and uncertain-result correlation | core traceability/durability is final; step/event/persistence relationships remain | OPEN / PARTIALLY DEFINED |
| T01 | I/O timeout semantics | recovered H3CM-X004: 5s per complete frame read/write; operation timeout separate | RECOVERED OWNER-FINAL — REPUBLISH/HARMONIZE |
| T02 | `deadline_ms` representation | required field existence is FINAL; representation/clock/bounds remain | OPEN / PARTIALLY DEFINED |
| T03 | effective operation-budget scope | default 30s/max 900s/per-operation limits are FINAL; composition/start/end phases remain | OPEN / PARTIALLY DEFINED |
| T04 | phase/error boundary around canonical UNKNOWN_OUTCOME rule | post-dispatch uncertainty is FINAL; before-dispatch/error/result-recovery details remain | OPEN / PARTIALLY DEFINED |
| T05 | idempotency/identifier binding | API replay semantics final; durable/technical binding remains | OPEN / PARTIALLY DEFINED |
| C03 | USER/ADMIN surface binding at control boundary | distinct Unix identities/surfaces + origin_surface are FINAL; exact peer→surface binding/mismatch behavior remains | OPEN / PARTIALLY DEFINED |
| C05 | trusted context resolution in wcpd | fields/origin-binding/wcpd participation partly defined; storage/expiry/revocation/revalidation remain | OPEN / PARTIALLY DEFINED |
| C06 | control request/response/job protocol | full control wire protocol remains unpublished | OPEN |
| C07 | backend vs wcpd responsibility boundary | wcpd control/orchestration + origin-binding validation + sole IPC-B client are FINAL; workflow split remains | OPEN / PARTIALLY DEFINED |
| H01 | HTTP versioning strategy | semantic version 1.0/breaking rules are FINAL; HTTP representation remains | OPEN / PARTIALLY DEFINED |
| H02 | method/route binding | per activated API-WCP contract | OPEN |
| H03 | parameter locations | logical schema + unknown-field rejection are FINAL; path/query/header/body serialization remains | OPEN / PARTIALLY DEFINED |
| H04 | Idempotency-Key transport source | semantic key behavior final; HTTP location/duplication rule remains | OPEN / PARTIALLY DEFINED |
| H05 | DTO definitions | logical DTO names are FINAL; exact fields/types/status remain | OPEN / PARTIALLY DEFINED |
| H06 | safe error registry/content is FINAL | HTTP status/media envelope/masking details remain | OPEN / PARTIALLY DEFINED |
| H07 | session/token transport coexistence | AUTH-* + browser CSRF/Origin/CORS baseline are FINAL; exact transport/multi-auth rule remains | OPEN / PARTIALLY DEFINED |
| H08 | discriminated `operation` schemas | per operation required/forbidden fields | OPEN |
| H09 | pagination | cursor/limit logical fields exist; cursor/order/filter/limits remain | OPEN / PARTIALLY DEFINED |
| H10 | cache classification | sensitive/authenticated no-store is FINAL; any cacheable-read exception remains | OPEN / PARTIALLY DEFINED |

### SECURITY / HARDENING — primary

| ID | Decision | Required output | Status |
|---|---|---|---|
| B01–B03 | parent bindings for service reload/restart/firewall apply | exact approved tuples | OPEN |
| B04–B05 | grants.set → apply/revoke step bindings | exact scope/target constraints | OPEN |
| C02 | transport access vs application authorization | DevOps v1.0 §5.1 + Security v1.0 §1 | ALREADY DEFINED |
| S01 | admissible USER/ADMIN topology | deployment rule, no port-only assumption | OPEN |
| S02 | cross-surface cookie handling | cookie names/attributes + session namespace/surface separation are FINAL; only wrong/simultaneous-cookie handling and safe projection remain | OPEN / STRONGLY CONSTRAINED |
| S03 | cross-surface CSRF/Origin/WSS behavior | exact Origin + synchronizer CSRF + 403/no-mutation + same-origin baseline are FINAL; proxy/WSS/topology details remain | OPEN / STRONGLY CONSTRAINED |
| S04 | definition of cookie non-exposure | measurable criterion for QA | OPEN |
| S05 | permission resolution vs final AUTHZ | final AUTHZ intersection + permission resolution order are FINAL; positive-override composition/internal algorithm remains | OPEN / PARTIALLY DEFINED |
| S06 | account override ceiling | positive/negative limits | OPEN |
| S07 | reseller delegation composition | effective-envelope ceiling/no ownership transfer/current-state revocation are FINAL; physical computation/storage remains | OPEN / PARTIALLY DEFINED |
| S08 | temporary privilege/approval composition | no role mutation/no permission bypass/fresh MFA+scope are FINAL; state representation/lifecycle remains | OPEN / PARTIALLY DEFINED |
| S09 | absent/DENY representation | unknown binding DENY/no side effect/audit is FINAL; NOT_GRANTED distinction + external mapping remains | OPEN / PARTIALLY DEFINED |

### DEVOPS / BUILD / RELEASE — toolchain/build decisions

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

### DEVOPS / BUILD / RELEASE — control/runtime decisions

| ID | Decision | Required output | Status |
|---|---|---|---|
| C01 | control socket physical identity | recovered M1: /run/wcp360/wcpd.sock, owner wcpd, group wcp360-control, mode 0660 | RECOVERED OWNER CONSENSUS — REPUBLISH/CONFIRM |
| C04 | worker path to normal control boundary | direct IPC-B forbidden + no control group by default + normal control path required are FINAL; exact indirect mechanism remains | OPEN / PARTIALLY DEFINED |
| S01–S04 | deploy/routing/TLS/proxy behavior | topology evidence | OPEN |
| T01/T03 | socket timeout operational behavior | T01 recovered final; T03 composition/start/end remains owner work | T01 RECOVERED / T03 OPEN |

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

- D/T/C/H/S/F decisions are accepted;
- Security bindings are changed;
- an API route exists;
- a socket path is canonicalized;
- a DB structure is created;
- QA cases passed;
- SC-M01 resumes.

Implementation consumption requires the owner decision to be published in its normative domain and MASTER to apply the existing gate/checklist.

Recovered final owner evidence such as C01 is not consumed directly by SOURCE CODE until republished/confirmed. B01–B05/D01 have current canonical identifiers but no recovered explicit owner-ratification proof; they require current owner harmonization/decision.

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
