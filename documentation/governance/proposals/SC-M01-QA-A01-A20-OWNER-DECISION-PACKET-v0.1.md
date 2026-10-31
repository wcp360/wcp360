# WCP360 — SC-M01 QA A01–A20 Owner Decision Packet v0.1

**Publication date:** 2026-10-27  
**Status:** PROPOSAL — REVIEW BY `WCP360 — TESTS / QA` REQUIRED — **NON-CANONICAL**  
**Parent:** WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE  
**QA owner tracking:** [GitHub issue #7](https://github.com/yyogas/wcp360/issues/7)  
**Global gate:** [#13](https://github.com/yyogas/wcp360/issues/13)  
**Audit:** [#41](https://github.com/yyogas/wcp360/issues/41)  
**Authority:** TESTS / QA alone owns final QA specification and test lifecycle decisions. This packet is *not* an owner acceptance, an executable test baseline, or a claimed E3 independent review.

## 1. Purpose: conclude the existing owner candidate, not create another generic QA draft

Existing evidence in GitHub main:

- `governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md` — self-contained 2026-10-03 QA candidate (Git blob `fe2e93ade5541808ff0a0c718ef5c74fb0b2356b`);
- `governance/handoffs/SC-M01-QA-OWNER-ACCEPTANCE-CHECKLIST.md` — current A01–A20 owner checklist (Git blob `a6751343a05f2727b1fd518676a2bff97fd77fff`);
- `governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.json` — derived source index (Git blob `e039c8d7842df2192b0ecb41abf653000d74d116`);
- the final API v1.0, IPC-B/1, Permission Registry v1.0, Security v1.0 and DevOps v1.0 owner exports under `specifications/`.

Current source-derived *reference* coverage is **86 API contracts / 86 TEST-API refs, 47 privileged IPC operations / 47 TEST-IPC refs, and 82 active permissions / 82 API bindings**. This is not runnable test-case coverage or any PASS/FAIL evidence. The 85 proposed C/TQ/CQ/HQ/SQ/FQ labels remain unapproved proposal cases.

**Do not** silently promote a 2026-10-03 owner candidate to FINAL merely because it has many sections. The exact final owner artifact under `specifications/tests/` is still **NOT MATERIALIZED / NOT CONFIRMED**, and QA #7 remains OPEN.

## 2. Provenance decision: P1 / P2 / P3 (owner must select ONE)

Historical metadata proves Test Baseline version `v1.0` was adopted but does **not** establish an original complete final SC-M01 test body.

| Option | When valid | Publication declaration |
|---|---|---|
| **P1 — Exact republication** | QA produces a verifiable previously approved complete final v1.0 source with provenance | `PUBLICATION TYPE = EXACT REPUBLICATION`, `CONTENT CHANGE = NONE` |
| **P2 — First canonical materialization** (recommended for QA review) | QA accepts/amends existing candidate and publishes the *first* complete final body | `PUBLICATION TYPE = FIRST CANONICAL MATERIALIZATION / OWNER APPROVAL`, `CONTENT CHANGE = INITIAL CANONICAL CONTENT`, version impact is explicit QA+MASTER decision |
| **P3 — Version change** | QA determines that changed requirements require another version | `PUBLICATION TYPE = OWNER-AMENDED FIRST FINAL`, declare full delta and proposed new version; require MASTER ratification |

**P2 recommendation is not approval.** The source record supports no claim of prior complete final QA body. Do not reuse `CONTENT CHANGE = NONE` from DB/Engineering recovery metadata.

Candidate target filename remains **unreserved** pending QA decision:

`specifications/tests/WCP360_SC-M01_Test_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md`

## 3. A01–A20 explicit owner decision matrix

Every `PROPOSE ACCEPT` below means *this author recommends retaining the already documented candidate rule*. Every item is **OWNER DECISION PENDING**, even if its core semantics are established by other approved source contracts. Owner may `ACCEPT`, `AMEND`, `REJECT` or `DEFER` with exact rationale, dependencies and final section mapping.

| ID | Recommended decision (non-authoritative) | Normative candidate rule to adopt or amend | Remaining owner decision / blocker |
|---|---|---|---|
| **A01** | PROPOSE ACCEPT P2, v1.0 if QA confirms version impact | Owner TESTS/QA; scope SC-M01 FIRST IMPLEMENTATION FOUNDATION; self-contained final artifact, accurate provenance and content delta | QA final filename, approval/commit, P1/P2/P3 choice, MASTER version publication |
| **A02** | PROPOSE ACCEPT | States `PLANNED / READY / BLOCKED / PASS / FAIL`; PASS/FAIL only from actual execution; BLOCKED names an exact missing dependency; READY means expected behavior is defined | QA transition rules; prohibit hidden assumed-PASS |
| **A03** | PROPOSE ACCEPT WITH EXPLICIT INHERITANCE | Each TEST-ID has title, source/version/section, level, preconditions, input/fixture, action, exact expected result/error, forbidden effects, auth/surface, DB/audit/observability assertions, cleanup, dependencies, automation/evidence/retention | QA must ratify complete field contract, inheritance resolution and optionality |
| **A04** | PROPOSE ACCEPT | Every executed test evidence envelope includes repository, exact candidate SHA, baseline/artifact version, OS/toolchain, selected suite, CI run/shard, timestamps/attempts, safe failure details and retained evidence digest | Retention class/duration, redaction and reproducibility decisions require QA/SRE/Security |
| **A05** | PROPOSE ACCEPT LEVEL/TAG DISTINCTION | Test families UNIT, CONTRACT, INTEGRATION, SECURITY, E2E, RECOVERY, INSTALL/BUILD; separate actual test level from tags (multiple concerns can apply) | QA decides taxonomy naming/allowed values |
| **A06** | PROPOSE ACCEPT REFERENCE COVERAGE; full HTTP behavior BLOCKED | One `TEST-API-####` anchor per of 86 `API-WCP-####`, preserving surface/auth/permission/action/tenant/schema/error/timeout/retry/idempotency/audit assertions | H01–H10 HTTP route/method/status/DTO details remain API owner decisions; no invented route |
| **A07** | PROPOSE ACCEPT REFERENCE COVERAGE; implementation execution PLANNED | One `TEST-IPC-####` anchor for each of 47 IPC-B operations, positive and negative peer/framing/authorization/bounded primitive/timeout/idempotency/reconciliation checks | Exact tests may depend on owner-issued C03/C06/C07, DB #6 and privileged handler; no generic execute |
| **A08** | PROPOSE MIXED TEST STRATEGY | 82 active permission IDs: API binding tests plus dedicated authorization-engine DENY/tenant/delegation/entitlement tests; no lexical cast between permission/action/job/event/IPC/error types | QA owner chooses direct vs transitive evidence sufficiency; Security S05/S06 pending |
| **A09** | PROPOSE RETAIN 26 NEGATIVE SECURITY CASES | Preserve catalog N01–N26 with rejection component, safe expected error/outcome, *no forbidden effect*, security/audit evidence; mandatory unknown op/arbitrary command/path escape/TOCTOU/arbitrary signal/systemd/package argv/SQL/lost ACK/blind retry/fallback | QA maps N01..N26 to final Security/IPC owner semantics and executable fixtures |
| **A10** | PROPOSE RETAIN 40 BROWSER SECURITY CASES | Preserve TEST-SEC-WEB-001→040 with CSRF/Origin, session rotation/revocation, CSP/XSS, SSRF, redirects, upload/download, secrets, cache, MFA and USER/ADMIN separation | Cookie non-exposure S01–S04 topology/proxy/WSS not all ratified; QA must separately assert stored, selected, received, forwarded, backend-received and accepted-cookie facts |
| **A11** | PROPOSE MARK PHYSICAL ASSERTIONS BLOCKED `#6` | Test DB PK/FK/composite tenant constraints, uniqueness, revisions, idempotency, jobs, leases, outbox, audit, recovery/restore only against owner-final physical schema | Exact table/column/unique/index/transaction fixtures must not be invented from recovery fragments |
| **A12** | PROPOSE MARK TOOLCHAIN GATES BLOCKED `#9` | Adopt Engineering-accepted code quality gates and version-pinned tooling only after its final owner artifact exists | Framework/language versions, coverage threshold, static tools, build commands and DoD unknown until owner ratification |
| **A13** | PROPOSE ACCEPT FAILURE-INJECTION MATRIX | `UNKNOWN_OUTCOME` after uncertain external effects; no blind retry; distinguish before intent/after intent/before dispatch/during dispatch/after effect before persistence/after result before response/daemon restart/stale worker/audit fail/DB down/reconcile restart | Exact DB expectations require #6; API/IPC deadline/retry owners; tests not PASS |
| **A14** | PROPOSE ACCEPT SEMANTIC IDEMPOTENCY | Same key + same semantic request = same logical result; same key + different semantic request = IDEMPOTENCY_CONFLICT; test parallel duplicates, cross-tenant isolation, late response and no blind destructive retry | Durable scope/key uniqueness and result persistence blocked by #6 and API T05/T06 |
| **A15** | PROPOSE ACCEPT FOUNDATION-SCOPED REPO TESTS | Bootstrap reproducibility, clear USER/ADMIN boundaries, lockfiles, no secret commits, generated assets, product root write-denied, reviewable CI entry points | PR #42 R1 tooling still awaits E3/enforcement; do not count its E1 as product test PASS |
| **A16** | PROPOSE BLOCK PINS UNTIL F01–F10 | Clean Debian 13/amd64 build, approved dependency source/digest/signature, pinned toolchain and artifact identity | Exact PHP/Go/PostgreSQL/Node/tool versions and command names require Engineering/DevOps/Security ratification |
| **A17** | PROPOSE ACCEPT DEDUPLICATION BEFORE IDs | Each of 85 historical proposal-case labels is REUSE/MERGE/SPLIT/DISCARD/BLOCKED/CREATE NEW canonical TEST ID, with owner decision as provenance | Do not issue TEST-ID or expectation for an unratified semantic decision; require QA owner triage |
| **A18** | PROPOSE ACCEPT FAILURE-VISIBLE CI REPORTING | Exact SHA, baseline, selected/executed/PASS/FAIL/BLOCKED, skipped/not-selected reasons, environment and artifacts; incomplete selection never equals full baseline PASS | QA decides required scopes and evidence retention; quarantine/rerun exclusions explicit |
| **A19** | PROPOSE ACCEPT BUG REGRESSION / EXECUTION INTEGRITY | Reproduce→diagnose→root cause→fix→test→regression→verify→close; every applicable fixed bug has executable test; retain failed attempts, classify flaky/quarantine, deterministic time/seed, parallel isolation and rerun eligibility | QA owner must explicitly ratify 20A cross-cutting safeguards, not bury them in CI |
| **A20** | PROPOSE ACCEPT EXACT-SHA FIRST CODE GATE | First CODE-BASELINE requires selected applicable SC-M01 tests, no unresolved critical FAIL, explicit handling of BLOCKED/out-of-scope, exact built/tested SHA, evidence retention and independent Engineering/Security/DB/QA review | MASTER owns implementation readiness; QA owns evidence sufficiency; completion does not authorize SC-M02 |

## 4. Cross-cutting A03/A04/A18/A19 execution integrity — required explicit owner acceptance

The existing checklist's `20A` must receive an explicit owner verdict. This packet proposes the following acceptance clauses **without marking them approved**:

| Clause | Expected owner decision | Critical evidence |
|---|---|---|
| QI-01 — Attempt history | PROPOSE ACCEPT | Rerun cannot erase original failed attempt or its exact SHA/environment/clock/seed |
| QI-02 — Failure classification | PROPOSE ACCEPT | Separate product defect, infrastructure failure, flaky/nondeterminism and operator/environment problem |
| QI-03 — Quarantine | PROPOSE ACCEPT | Quarantine metadata contains reason, owner, expiry/review; required quarantined assertion cannot silently contribute to full baseline PASS |
| QI-04 — Deterministic inputs | PROPOSE ACCEPT | Explicit clock/seed when reproducibility requires them, retained with attempt evidence |
| QI-05 — Parallel isolation | PROPOSE ACCEPT | Per-attempt tenant/resource/DB/schema/filesystem/socket fixtures isolated; no cross-test privileged state leakage |
| QI-06 — Durable evidence | PROPOSE ACCEPT | Test evidence remains auditable when temporary CI artifacts expire; secrets always redacted |
| QI-07 — Material-change rerun | PROPOSE ACCEPT | Code/tool/contract change evaluates prior PASS applicability before accepting old result on new SHA |

The exact evidence retention duration, storage location, cryptographic digest choice and rerun invalidation scope must be owner-approved. Do not invent numeric defaults.

## 5. What can already be declared from the **final source contracts** (not from this proposal)

- Security and IPC-B explicitly forbid generic privileged execution, arbitrary caller-supplied paths/argv/service names and unauthorized root access.
- WCP-USER/WCP-ADMIN remain separate authorization surfaces; permission/action/IPC types are not interchangeable.
- Browser mutation CSRF and exact-Origin requirements exist in Security/API owner material; precise topology details remain pending.
- Backend→wcpd(non-root)→bounded IPC-B→wcp-privd(root minimal) is canonical.
- Timeout does not prove failure; unknown outcome requires reconcile before destructive retry.
- Initial platform is Debian GNU/Linux 13 trixie / amd64.
- Version- and SHA-scoped QA evidence is necessary to distinguish real execution from documentation/reference checks.

A derived source relationship such as `API-WCP-0102→TEST-API-0102` establishes a TEST-ID **reference only**. The complete test record remains QA owner's responsibility.

## 6. Publication checklist — to be completed BY THE QA OWNER, not this preparatory PR

```text
QA OWNER = WCP360 — TESTS / QA
QA DECISIONS A01–A20 = (individual ACCEPT/AMEND/REJECT/DEFER attached)
QI-01..QI-07 = (individual ACCEPT/AMEND/REJECT attached)
PUBLICATION TYPE = (P1 / P2 / P3)
FINAL VERSION = (owner decision)
FINAL ARTIFACT NAME = (owner decision)
TARGET = specifications/tests/<owner-final-filename>
PROVENANCE = (source hashes + acceptance/owner decision + date)
CONTENT CHANGE = (honest classification; never assume NONE)
VERSION IMPACT = (explicit)
DEPENDENT OWNER HANDOFFS = (DB #6 / Engineering #9 / Security / API / DevOps)
HISTORICAL CONTENT INCLUDED = NO
TEST EXECUTIONS = 0 until tests actually run; status PLANNED/READY/BLOCKED
FINAL OWNER APPROVAL = (explicit source)
BLOCKER = (exact list or NONE, only if source-ready)
COMMIT SHA = (exact GitHub main implementation-authority commit)
```

QA must then produce an independently reviewable **complete owner-final test baseline**, not only this recommendation matrix, and ensure its actual accepted test cases contain required preconditions, fixtures, expected outcomes, safe error classes, negative effects, assertions and evidence lifecycle.

## 7. Gate and next handoff

```text
ISSUE #7 = OPEN — QA OWNER ACCEPTANCE/PUBLICATION REQUIRED
QA v1.0 METADATA = HISTORICALLY PRESENT
QA FINAL SC-M01 TEST BODY = NOT PRESENT
DB #6 = NOT CANONICAL IN MAIN
ENGINEERING #9 = NOT CANONICAL IN MAIN

SC-M01 EVIDENCE = 5 / 8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

HANDOFF: QA owner → review this packet against its 2026-10-03 candidate and 2026-10-04 checklist; return explicit decisions and final publication with provenance to MASTER. Do not make another full corpus scan and do not transform old proposal identifiers into final TEST IDs without accepted owner decisions.
