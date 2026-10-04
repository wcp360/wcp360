# WCP360 — SC-M01 QA Owner Candidate Conformance Matrix

**Status:** DERIVED REVIEW EVIDENCE — NON-NORMATIVE — NOT QA APPROVAL  
**Reference main:** 08e3f005cd95b003e2e245a2e37ea9366296f17a  
**Owner candidate blob:** ec8f3fc6ebed49870dd76960d5b9e383d17e13b4  
**Owner acceptance checklist blob:** 6c47d394deceac0aa61f4a3cc75b67b5c59a4ba4  
**Coverage JSON blob:** e039c8d7842df2192b0ecb41abf653000d74d116  
**Coverage Markdown blob:** 7a8b1ed90366a9f9460f3742b80b1aed71f9e28f

Purpose: verify whether the current QA owner candidate contains a prepared answer path for every A01–A20 owner-acceptance topic.

This is a structural/content review. It is **not** TESTS / QA owner approval, does not create canonical TEST IDs, and does not execute application tests.

## 1. Result summary

    acceptance topics examined = 20
    topics with candidate section / prepared rule = 20
    topics requiring QA owner ratification = 20
    topics with external blocking dependencies = multiple
    canonical QA artifact created = NO
    product PASS / FAIL created = NO

Interpretation:

- the QA owner does **not** need another broad blank-slate draft;
- the current candidate is sufficiently structured to be reviewed item-by-item;
- several items remain blocked on Database #6, Engineering #9 or open owner semantics;
- A01 provenance/publication classification remains an explicit owner choice.

## 2. Conformance states

This matrix uses:

    PREPARED
    PREPARED / OWNER CHOICE
    PREPARED / DEPENDENCY BLOCKED

These are preparation states only, not canonical QA lifecycle states.

## 3. A01–A20 matrix

| ID | Acceptance topic | Candidate evidence | Conformance | Remaining owner action |
|---|---|---|---|---|
| A01 | artifact identity / provenance | Candidate §1, §27–28; acceptance checklist A01 + A01-PROVENANCE | PREPARED / OWNER CHOICE | choose final filename, publication type, content-change declaration, version impact, provenance |
| A02 | lifecycle states | Candidate §1 and §23 | PREPARED | accept/amend PLANNED/READY/BLOCKED/PASS/FAIL semantics |
| A03 | canonical test-record schema | Candidate §3 | PREPARED | accept/amend mandatory fields/inheritance rules |
| A04 | execution evidence envelope | Candidate §4 | PREPARED | accept/amend reproducibility/evidence fields |
| A05 | test-level taxonomy | Candidate §5 | PREPARED | accept/amend UNIT/CONTRACT/INTEGRATION/SECURITY/E2E/RECOVERY/INSTALL-BUILD |
| A06 | API coverage rule | Candidate §8 + coverage index | PREPARED / DEPENDENCY BLOCKED | accept source mapping; keep unresolved HTTP projections blocked until API owner decisions |
| A07 | IPC coverage rule | Candidate §9 + coverage index | PREPARED / DEPENDENCY BLOCKED | accept coverage obligations; keep timeout/control details blocked where owner decisions remain |
| A08 | permission coverage rule | Candidate §11 + 82/82 derived mapping | PREPARED | choose direct/transitive/mixed QA organization without changing canonical permission IDs |
| A09 | N01–N26 negative catalog | Candidate §12 | PREPARED | accept/amend mappings and executable assertions |
| A10 | TEST-SEC-WEB-001→040 | Candidate §13 | PREPARED / DEPENDENCY BLOCKED | preserve/map family; topology-dependent cases await Security/DevOps decisions |
| A11 | Database test dependency | Candidate §14 + §26 Database #6 | PREPARED / DEPENDENCY BLOCKED | exact physical assertions wait for #6; no recovered-fragment promotion |
| A12 | Engineering dependency | Candidate §19 + §26 Engineering #9 | PREPARED / DEPENDENCY BLOCKED | exact tools/thresholds/DoD wait for #9 |
| A13 | UNKNOWN_OUTCOME / recovery | Candidate §16 | PREPARED / DEPENDENCY BLOCKED | accept failure-injection model; exact persistence assertions wait for DB/owner decisions |
| A14 | idempotency | Candidate §15 | PREPARED / DEPENDENCY BLOCKED | semantic API rules available; durable physical representation waits for #6/API harmonization |
| A15 | repository/bootstrap family | Candidate §6 + §20 | PREPARED / DEPENDENCY BLOCKED | determine which repository-tool evidence is accepted after PR #42 review/merge |
| A16 | build/toolchain family | Candidate §18–19 | PREPARED / DEPENDENCY BLOCKED | exact runtimes/tools/thresholds wait for Engineering/DevOps decisions |
| A17 | 85 proposal-case intake | Candidate §22 | PREPARED / OWNER CHOICE | triage REUSE/MERGE/SPLIT/DISCARD/BLOCKED/NEW TEST only after underlying owner decisions |
| A18 | partial selection / CI reporting | Candidate §24 | PREPARED | accept/amend reporting envelope; partial run must not equal full PASS |
| A19 | regression policy | Candidate §21 | PREPARED | accept/amend bug→regression requirement |
| A20 | first CODE-BASELINE evidence | Candidate §25 | PREPARED / OWNER CHOICE | accept/amend minimum evidence before MASTER may establish first CODE-BASELINE |

## 4. Coverage-index verification

The current derived coverage index is tied to exact canonical source blobs:

    API 1.0     = 4893c5191b4928ecfa5a70f4dc14b39a8939fcea
    IPC-B/1     = fec9c74e8413db822662e47c76c40486e282334f
    Permission  = 81f3d425f36470167aba4bfb7ba069268626342f

Verified inventory:

    API source rows = 86
    unique TEST-API references = 86
    IPC source rows = 47
    unique TEST-IPC references = 47
    ACTIVE permission rows = 82
    permissions represented by API = 82
    missing active permissions = 0
    unknown API permissions = 0
    contract/test suffix mismatches = 0

This removes manual identifier recounting from QA's review task.

It does not prove expected outcomes or execution.

## 5. What is already sufficiently drafted

The candidate already contains complete drafting sections for:

- baseline metadata and lifecycle;
- source authority;
- canonical test-record structure;
- execution evidence;
- test levels;
- repository/bootstrap;
- USER/ADMIN separation;
- API;
- IPC-B;
- control boundary;
- permissions/authorization;
- N01–N26;
- TEST-SEC-WEB-001→040;
- Database;
- idempotency;
- failure/UNKNOWN_OUTCOME/recovery;
- audit/outbox;
- DevOps/platform;
- toolchain;
- repository guardrail evidence;
- regression;
- proposal-case intake;
- status transitions;
- CI reporting;
- first CODE-BASELINE gate;
- dependency matrix;
- final-publication checklist.

Therefore another general-purpose QA draft would mainly duplicate existing preparation.

## 6. What QA still owns

TESTS / QA must still decide:

1. publication provenance and version/content-change status;
2. final filename;
3. final lifecycle/test-record/evidence/taxonomy semantics;
4. exact mapping/organization of canonical tests;
5. treatment of N01–N26 and TEST-SEC-WEB-001→040;
6. disposition of the 85 proposal cases;
7. criteria for READY/BLOCKED and CODE-BASELINE test evidence;
8. final source-ready/blocker statement.

QA may amend the candidate before approval.

## 7. What QA must not decide for another owner

QA must not fill missing behavior by guess:

- DB physical schema, indexes, FK/check/transaction/recovery details → Database #6;
- Engineering tools/thresholds/DoD/review gates → Engineering #9;
- HTTP methods/routes/DTO/status maps → API owner;
- control socket protocol/worker path → API/Security/DevOps;
- timeout/deadline phase details → API/IPC/DevOps;
- deployment topology / positive override ceiling → Security/DevOps;
- concrete runtime/framework/toolchain → Engineering/DevOps.

Those tests remain BLOCKED or conditional until owner decisions are normative.

## 8. Publication readiness conclusion

The current candidate is:

    STRUCTURALLY COMPLETE ENOUGH FOR OWNER REVIEW = YES
    COMPLETE ENOUGH FOR AUTOMATIC PROMOTION = NO
    QA OWNER APPROVAL = MISSING
    EXTERNAL DEPENDENCIES = PRESENT
    CANONICAL specifications/tests ARTIFACT = ABSENT
    APPLICATION TEST EXECUTIONS = 0

Recommended next QA action:

    REVIEW A01–A20
    → ACCEPT/AMEND/DEFER
    → incorporate accepted owner decisions/dependencies
    → publish complete final artifact in specifications/tests/
    → record provenance/version/content change
    → update issue #7 and MASTER gate

No additional broad QA discovery round is needed unless TESTS / QA identifies a specific missing domain.
