# WCP360 — SC-M01 Current Readiness Matrix

**Reference date:** 2026-10-03  
**Status:** CURRENT COORDINATION STATE — NON-NORMATIVE STATUS REPORT  
**Main examined:** `4ff664a3157e662c174260e1fd35429738188f0c`  
**Audit:** `AUDIT-WCP-0100` / issue #41  
**Implementation gate:** issue #13

This file reports current evidence and work-in-progress. It does not replace owner specifications, add a ninth gate artifact, approve proposals, or create a CODE-BASELINE.

## 1. Executive state

```text
WCP360-BM-0001 = FINAL
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4

REQUIRED CANONICAL IMPLEMENTATION ARTIFACTS = 8
PRESENT IN MAIN = 5
MISSING / NOT MATERIALIZED = 3

SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Two distinct work packages exist:

- **PR #42 — repository guardrails**, still OPEN; repository tools/tests/CI only, no application implementation.
- **PR #43 — owner-decision pack**, MERGED AS P1 NON-NORMATIVE EVIDENCE under REVIEW-MERGE-POLICY.

Neither work package is an implementation authorization. Merging PR #43 did not accept any owner decision.

## 2. Eight-artifact gate

| Domain | Version | GitHub main | Owner publication status | Current blocker |
|---|---|---:|---|---|
| API | 1.0 | YES | FINAL | semantic/transport clarifications remain under audit |
| IPC | WCP360-IPC-B/1 | YES | FINAL | timeout/context/idempotency clarifications remain under audit |
| Permission Registry | v1.0 | YES | FINAL | authorization-composition clarification remains under audit |
| Security Baseline | v1.0 | YES | FINAL | bindings/web topology clarifications remain under audit |
| Database | v1.0 | **NO** | FINAL / source-ready metadata confirmed | exact owner body materialization #6 |
| Test Baseline | v1.0 | **NO** | baseline metadata approved | final SC-M01 canonical artifact not materialized/confirmed #7 |
| DevOps | v1.0 | YES | FINAL | control-boundary/toolchain clarifications remain under audit |
| Engineering | ENG-WCP-RULESET-0001 v0.2 | **NO** | FINAL / source-ready metadata confirmed | exact owner body materialization #9 |

The 5/8 count is a publication gate count. It is not product completion, test coverage, or proof that all five present artifacts are semantically sufficient for every implementation decision.

## 3. Recovery evidence for missing artifacts

### Database #6

Conversation-history recovery confirms final owner publication metadata for:

`WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md`

Separately, earlier **closure-proposed/candidate** DB material identified schemas such as:

```text
wcp_core
wcp_iam
wcp_infra
wcp_hosting
wcp_commercial
wcp_ops
wcp_audit
wcp_backup
```

The same candidate/closure evidence also names, among other objects:

```text
wcp_core.accounts
organizations
tenants
account_organization_memberships
organization_tenant_memberships
hosting_accounts
reseller_tenant_delegations

wcp_iam.tenant_memberships
role_assignments

wcp_infra.managed_resources

wcp_ops.jobs
job_attempts
idempotency_records
leases
outbox_records
event_records
```

and describes UUIDv7/uuid identities except typed text namespaces, tenant-aware constraints, desired/observed state, transactions/concurrency/revisions, durable jobs/outbox/idempotency/audit/reconciliation and indexes.

**Provenance limit:** those detailed schema/table/constraint names are not independently recovered as the exact FINAL DB v1.0 body. They belong to candidate/closure material that preceded the separately confirmed final owner metadata. They may help the Database owner locate/recover the final file, but must not be copied into `specifications/database/` as if final.

Issue #6 remains materialization-only: exact final owner bytes/content are still required.

### Engineering #9

Conversation history confirms:

```text
ENG-WCP-RULESET-0001
VERSION = v0.2
FINAL APPROVED = YES
READY FOR SOURCE CODE = YES
BLOCKER = NONE
```

and indicates coverage of repository architecture, typed naming/namespaces, component boundaries, dependency/layering rules, configuration/secrets, error handling, logging, testability, secure coding, maintainability, complexity/duplication, concurrency, review gates, merge requirements and Definition of Done.

The exact full body is still unavailable. Older candidates/input packs must not be substituted.

### QA #7

Conversation history reconfirms that this case is different:

```text
TEST BASELINE VERSION = v1.0
BASELINE METADATA = APPROVED
FINAL SC-M01 CANONICAL TEST ARTIFACT = NOT MATERIALIZED / NOT CONFIRMED
CODE-BASELINE = NONE
```

The MASTER draft and input pack remain preparation only. QA must still publish the final owner artifact.

## 4. Audit readiness by finding

Status values in this table describe remediation work, not closure of the original finding.

| Finding | Subject | Current remediation state | Evidence / next proof | Finding status |
|---|---|---|---|---|
| 0101 | presence vs implementation readiness | **IN PROGRESS** | this matrix separates publication, semantic decisions, QA and implementation evidence | OPEN |
| 0102 | DB / QA / Engineering exports | **BLOCKED ON OWNER MATERIALIZATION** | #6/#7/#9 | OPEN |
| 0103 | API transport / DTO / HTTP mapping | **PROPOSAL READY FOR OWNER REVIEW** | PR #43 H01–H10, HQ01–HQ15 | OPEN |
| 0104 | permission/action/parent/primitive mappings | **PROPOSAL READY FOR OWNER REVIEW** | PR #43 D01–D04 + B01–B05, C01–C17 | OPEN |
| 0105 | backend/worker → wcpd | **PROPOSAL READY FOR OWNER REVIEW** | PR #43 C01–C07, CQ01–CQ12 | OPEN |
| 0106 | persistent idempotency | **PARTIAL PROPOSAL / DB DEPENDENCY** | PR #43 T05/T06 + H04; #6 for physical proof | OPEN |
| 0107 | IPC timeout/deadline phases | **PROPOSAL READY FOR OWNER REVIEW** | PR #43 T01–T04, TQ01–TQ10 | OPEN |
| 0108 | durable jobs / concurrency / uncertain outcome | **PARTIAL PROPOSAL / DB DEPENDENCY** | PR #43 T06/T07; #6 + SRE/QA proof | OPEN |
| 0109 | repository protection + CI | **PARTIAL FIX IN PR #42** | CI exists and has passed; branch enforcement remains off/unverified | OPEN |
| 0110 | independent technical review | **BLOCKED / PROCESS** | real separate reviewer/approval on exact SHA required; no fictitious reviewer | OPEN |
| 0111 | overbroad gitignore | **FIX IMPLEMENTED + TESTED IN PR #42** | 52/52 probes, negative controls; still not merged/independently reviewed | OPEN |
| 0112 | 147-file mirror vs full DOC publication | **PARTIALLY VERIFIED** | 147/147 manifest match; exact DOC archive still #16 | OPEN |
| 0113 | build inputs/toolchain/rights/secrets | **PROPOSAL READY FOR OWNER REVIEW / ENGINEERING DEPENDENCY** | merged P1 Toolchain/Build F01–F10 + FQ01–FQ15; exact Engineering/DB decisions still missing | OPEN |
| 0114 | QA assertions and execution evidence | **BLOCKED ON QA FINAL + IMPLEMENTATION** | #7; proposal cases in #43 remain PLANNED only | OPEN |
| 0115 | domain ownership vs folder precedence | **READY FOR AUDIT/MASTER VERIFICATION** | merged governance normalization separates source precedence from owner-domain authority | OPEN |
| 0116 | foundation vs future modules | **READY FOR AUDIT/MASTER VERIFICATION** | merged `SC-M01-SCOPE.md` defines first implementation foundation and future-module non-blockers | OPEN |
| 0117 | USER/ADMIN cookie topology | **PROPOSAL READY FOR OWNER REVIEW** | PR #43 S01–S04, SQ01–SQ07 | OPEN |
| 0118 | permission priority vs Security intersection | **PROPOSAL READY FOR OWNER REVIEW** | PR #43 S05–S09, SQ08–SQ16 | OPEN |

No finding is marked CLOSED by this report.

## 5. PR #42 — repository guardrails

Scope:

- corrected root `.gitignore` behavior;
- 52 synthetic tracked/ignored path probes;
- publication inventory verifier and 6 synthetic tests;
- branding-archive validator and 9 synthetic tests;
- repository secret checker;
- staged-index secret checking;
- opt-in pre-commit hook;
- 37 existing + 37 staged/hook tests on the last fully verified candidate;
- read-only GitHub Actions jobs;
- no application code, DDL, migrations, systemd units or canonical specifications.

Important limitations:

- secret signatures are targeted, not exhaustive;
- history/decompressed archives are not fully scanned;
- the local hook is opt-in and not installed automatically;
- GitHub main remains reported unprotected;
- a green workflow does not itself create merge enforcement;
- same-author tests are not an independent review.

Current synchronized candidate: `a9fb5645a214f50414f6d9bd26ed7f2b49e3ad77`.

Latest run `37072620136` is SUCCESS on merge-test `5c59f2170c1a7894e4ef0a620b9139d1e5eebb6f`:

- 297 files / 1,086,196 bytes scanned;
- mirror 147/147;
- audit files 4/4;
- targeted secret findings 0;
- branding originals remain NOT_PUBLISHED.

This is E1 automated evidence plus E2 same-author verification. Do not merge PR #42 as a substitute for Engineering v0.2 or E3 independent review.

## 6. PR #43 — SC-M01 owner-decision pack — merged P1 evidence

Current proposal families:

| Proposal | Decisions |
|---|---:|
| Authorization bindings v0.2 | D01–D04 + B01–B05 |
| IPC timeout/idempotency v0.1 | T01–T07 |
| Control boundary v0.2 | C01 recovered/republication, C02 already defined, C03–C07 OPEN |
| API transport v0.1 | H01–H10 |
| Security Web/Authz v0.1 | S01–S09 |

Central coordination file:

`governance/proposals/SC-M01-OWNER-DECISION-MATRIX.md`

It tracks **47 decision points**:

- 45 OPEN semantic owner decisions;
- C01 recovered historical owner consensus requiring republication/confirmation;
- C02 already defined in current DevOps/Security.

Proposed local QA scenarios:

```text
Authorization        = 17
Timeout/idempotency  = 10
Control boundary     = 12
API transport        = 15
Security Web/Authz   = 16
Toolchain/Build      = 15
TOTAL                = 85
```

All 85 are `PLANNED`, none are canonical TEST IDs, and none are executable PASS evidence.

## 7. Owner decisions that can advance before #6/#7/#9

The following can be decided without reconstructing Database/QA/Engineering:

### API / IPC / CONTRACTS

- origin action vs technical IPC action model;
- `deadline_ms` representation;
- timeout phase semantics;
- control-boundary protocol shape;
- API HTTP method/route/DTO/error/pagination profiles for SC-M01 endpoints;
- idempotency source/transport semantics, leaving physical persistence to Database.

### Security

- exact parent bindings for the five targeted privileged paths;
- surface/peer/context fail-closed rules;
- USER/ADMIN cookie/session/origin isolation model;
- permission-resolution vs final AUTHZ composition;
- override and delegation ceilings;
- non-bypass behavior for approval/temporary privilege.

### DevOps

- control socket path/ownership/group/mode and lifecycle;
- worker access model;
- reverse-proxy/topology rules;
- operational interpretation of transport timeout, without redefining protocol semantics.

These decisions must be published by their owners before SOURCE CODE consumes them.

## 8. Decisions blocked on Database #6

Do not invent:

- exact table/column names beyond recovered evidence;
- unique indexes for idempotency;
- job claim/lease/fencing mechanism;
- outbox transaction layout;
- reconciliation persistence;
- retention horizons;
- grant_set physical representation;
- exact FK/delete/locking rules.

Published proposal evidence may describe required properties but not choose their physical implementation.

## 9. Decisions blocked on QA #7

Do not mark PASS/FAIL for:

- 85 proposal cases;
- TEST-API-*;
- TEST-IPC-*;
- N01–N26;
- TEST-SEC-WEB-001→040;
- repository/bootstrap application requirements.

The repository-maintenance tests in PR #42 are real executed tests of the repository tools, but they are **not** the missing WCP360 Test Baseline.

## 10. Decisions blocked on Engineering #9

Do not declare final compliance with:

- repository architecture conventions;
- complexity/duplication thresholds;
- required static analysis;
- build/test quality thresholds;
- Definition of Done;
- independent code-review gates;
- merge/release quality requirements;

until the exact v0.2 ruleset is materialized.

## 11. Recommended order from this state

### A — owners decide the merged six proposal domains

No general re-audit. Each owner answers the centralized decision matrix with exact source/version impact.

### B — materialize #6 and #9; publish QA final #7

Do not reconstruct DB/Engineering. QA must produce its own final artifact.

### C — cross-check the eight artifacts against accepted owner decisions

Use the existing SC-M01 checklist. Any semantic amendment must be versioned honestly.

### D — merge/review repository guardrails

Only after exact-SHA review and current-main CI proof. Branch protection/enforcement remains a separate administrative control.

### E — MASTER evaluates SC-M01 resume

Only after 8/8 and the blocking decisions for the first implementation package are sufficiently resolved.

### F — Source Code implements SC-M01 only

Then:

```text
implementation
→ tests
→ Engineering review
→ fixes
→ regression tests
→ verified first CODE-BASELINE decision
```

No SC-M02 automatically follows.

## 12. Current non-goals

This readiness pass does not:

- rescan the >1000 Markdown documentation corpus;
- reopen H3-C / H3-CM / H3-V2;
- recreate the cancelled documentation-recovery workstream;
- reintroduce Sprint 0;
- reintroduce V4.1 Executable Contracts;
- create application code;
- create DB DDL/migrations;
- choose production hostnames;
- create API routes;
- change baseline versions;
- fabricate owner approvals or independent reviewers.

## 13. Readiness conclusion

```text
DOCUMENTARY / GOVERNANCE CONSOLIDATION = ADVANCED
REPOSITORY GUARDRAILS = IMPLEMENTED/TESTED IN OPEN PR, NOT YET ACCEPTED
CONTRACT AMBIGUITIES = CONVERTED INTO OWNER DECISIONS, NOT YET RATIFIED
MISSING CANONICAL ARTIFACTS = 3
APPLICATION IMPLEMENTATION = NOT STARTED
APPLICATION QA EXECUTION = 0
CODE-BASELINE = NONE

IMPLEMENTATION READINESS = PAUSED — TARGETED EVIDENCE
```

The next meaningful progress comes from **owner decisions and exact artifact materialization**, not from producing additional generic audits.


## 14. Recovered M1 control-boundary evidence

Conversation-history recovery cross-checks the final H3-CM M1 consensus of 2026-09-27:

```text
CONTROL SOCKET = /run/wcp360/wcpd.sock
OWNER = wcpd
GROUP = wcp360-control
MODE = 0660
DEFAULT CLIENTS = wcp360-user, wcp360-admin
DEFAULT DENIED = wcp360-worker, hosting identities
```

Earlier/provisional naming such as `wcp360-ctl` does not override the later final `wcp360-control` wording.

This recovery does **not** recover the complete control wire protocol, exact indirect worker path, detailed retry/idempotency model or full systemd lifecycle. C01 therefore requires owner republication/confirmation; C03/C04/C06/C07 remain open.

C05 is now **OPEN / PARTIALLY DEFINED**: current Security already defines the trusted-context fields and origin-binding responsibilities, and recovered final evidence states that wcpd creates/validates `authorization_context_id`; storage/resolver, expiry/revocation, freshness/revalidation and replay/substitution semantics remain open.

T04 is also **OPEN / PARTIALLY DEFINED**: IPC-B already canonically requires `UNKNOWN_OUTCOME → reconciliation → no blind retry` when execution may have occurred but cannot be proven; only phase/error boundaries and result-retrieval details remain open.


## 15. Evidence recovery and QA-owner preparation — 2026-10-03

The three missing gate artifacts are now classified more precisely.

### Database

```text
FINAL OWNER METADATA = CONFIRMED
FINAL COMPLETE BODY WAS PREVIOUSLY PUBLISHED = CONFIRMED
FULL ORIGINAL BODY RECOVERED NOW = NO
```

Merged recovery evidence:

`governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md`

The recovery now preserves substantial exact/near-verbatim fragments across CORE, IAM, managed-resource, HOSTING, COMMERCIAL, OPS, AUDIT and BACKUP plus transaction/concurrency/reconciliation/delete/index classes.

It is intentionally incomplete and cannot be promoted. Detailed INFRA structures beyond recovered `managed_resources` are not asserted when no reliable historical fragment is available.

### QA

Deeper historical recovery found no evidence of a complete final autonomous SC-M01 QA artifact ever being published.

Merged owner candidate:

`governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md`

It provides a self-contained structure for TESTS / QA review:

- mandatory test record and evidence envelope;
- unit/contract/integration/security/E2E/recovery/install-build levels;
- repository/surface/API/IPC/control/authz/DB/failure/audit/DevOps/toolchain/regression families;
- explicit #6/#9 dependencies;
- 85 proposal-case intake process;
- PLANNED/READY/BLOCKED/PASS/FAIL transitions;
- first CODE-BASELINE QA evidence expectations.

It creates no canonical TEST IDs and no PASS/FAIL.

### Engineering

```text
FINAL OWNER METADATA = CONFIRMED
FINAL FULL BODY = NOT RECOVERED
PHASE G/H3-C CANDIDATE = PARTIALLY RECOVERED
```

Merged historical evidence:

`governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md`

The candidate evidence is explicitly separated from final authority. It preserves recoverable Engineering themes but cannot be copied into `specifications/engineering/`.

### Gate effect

```text
RECOVERY / PREPARATION QUALITY = IMPROVED
CANONICAL ARTIFACT COUNT = UNCHANGED

PRESENT = 5 / 8
MISSING = 3 / 8

SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

The work has reduced owner rediscovery, not bypassed owner authority.
