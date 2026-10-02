# WCP360 — Project Status

**Reference date:** 2026-10-02  
**Repository:** `yyogas/wcp360`  
**Main branch:** `main`  

## 1. Baseline state

```text
WCP360-BM-0001 = FINAL

DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4

MODULE REGISTRY VERSION = v1.0
PERMISSION REGISTRY VERSION = v1.0
SECURITY BASELINE VERSION = v1.0
DB MODEL VERSION = v1.0
API CONTRACT VERSION = 1.0
IPC CONTRACT VERSION = WCP360-IPC-B/1
TEST BASELINE VERSION = v1.0
DEVOPS BASELINE VERSION = v1.0
OS SUPPORT MATRIX VERSION = v1.0
OBSERVABILITY BASELINE VERSION = v1.0
PERFORMANCE BASELINE VERSION = v1.0
ENGINEERING RULESET = ENG-WCP-RULESET-0001 v0.2

CODE-BASELINE = NONE
```

## 2. Documentation publication

```text
MATERIALIZED PROJECT/LIBRARY MARKDOWN = 147
MIRRORED TO GITHUB = 147
MISSING = 0
```

Locations:
- `documentation/library-mirror/`
- `documentation/library-mirror/MANIFEST.md`
- `documentation/MATERIALIZED-MARKDOWN-INVENTORY.md`
- `documentation/design-system/V2.5.3.36.4/`

The physical canonical DOC archive `WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation.zip` is still not materialized as a retrievable Project/Library object. Its publication is tracked separately by issue #16 and is not substituted with an older archive.

## 3. Canonical implementation artifacts in GitHub

```text
5 / 8 PRESENT AND SOURCE-CODE CONSUMABLE
```

Present:
- API Contract 1.0;
- IPC Contract WCP360-IPC-B/1;
- Permission Registry v1.0;
- Security Baseline v1.0;
- DevOps Baseline v1.0.

Remaining:

### Database

```text
ARTIFACT =
WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md

OWNER STATUS =
FINAL APPROVED CONTENT = YES
PHYSICAL CONTRACT = INCLUDED
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE

REMAINING =
exact file body materialization/import
```

Input support: `governance/handoffs/SC-M01-DB-CANONICAL-INPUT-PACK.md`  
Tracking: issue #6.

### Tests / QA

```text
TEST BASELINE VERSION = v1.0
FINAL SC-M01 CANONICAL TEST ARTIFACT = NOT MATERIALIZED / NOT CONFIRMED
```

QA input support already committed:
`governance/handoffs/SC-M01-QA-CANONICAL-INPUT-PACK.md`

Extracted input:
- 86 TEST-API references;
- 47 TEST-IPC references;
- 82 active permissions;
- N01–N26 privileged negative-security catalog;
- TEST-SEC-WEB-001 → TEST-SEC-WEB-040;
- DevOps/platform validation obligations.

Input support: `governance/handoffs/SC-M01-QA-CANONICAL-INPUT-PACK.md`  
Tracking: issue #7.

### Engineering

Logical identity:
```text
ENG-WCP-RULESET-0001 v0.2
```

Exact final owner export:
```text
WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md
```

Owner status:
```text
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

Remaining:
exact file body materialization/import.

Input support: `governance/handoffs/SC-M01-ENGINEERING-CANONICAL-INPUT-PACK.md`  
Tracking: issue #9.

## 4. SC-M01 gate

Tracking: issue #13.

```text
SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Resume requires:

```text
8 / 8 PRESENT
8 / 8 FINAL APPROVED
8 / 8 HISTORICAL CONTENT EXCLUDED
8 / 8 READY FOR SOURCE CODE CONSUMPTION
```

Closing SC-M01 evidence does not itself create a CODE-BASELINE.

## 5. Initial platform

```text
Debian GNU/Linux 13 (trixie)
amd64 / x86_64
PRODUCT ROOT = /usr/lib/wcp360
```

Runtime identities:
```text
wcp360-user
wcp360-admin
wcpd
wcp-privd / root minimal
wcp360-worker
wcp360-control
wcp360-priv
```

## 6. Privileged architecture

```text
backend
→ wcpd (non-root)
→ bounded authenticated IPC
→ wcp-privd (minimal root)
→ explicit privileged primitive
```

Generic privileged execution remains forbidden.

## 7. Active tracking issues

```text
#6  DB canonical artifact materialization
#7  QA final SC-M01 Test Baseline artifact
#9  Engineering canonical export materialization
#13 SC-M01 8/8 master gate
#16 physical DOC-BASELINE v4.0.21.23 publication
```

Duplicate workstream issues #21–#24 were closed to avoid parallel tracking authorities.

## 8. Canonical repository guidance

Use:
- `governance/AUTHORITY-PRECEDENCE.md`
- `governance/WORKSTREAMS.md`
- `governance/SC-M01-RESUME-CHECKLIST.md`
- `governance/CANONICAL-ARTIFACT-INDEX.md`

Documentary mirror content does not automatically become implementation authority.


## 9. Owner-support packs

To reduce rediscovery without replacing owner authority, MASTER has versioned three input packs:

```text
governance/handoffs/SC-M01-DB-CANONICAL-INPUT-PACK.md
governance/handoffs/SC-M01-QA-CANONICAL-INPUT-PACK.md
governance/handoffs/SC-M01-ENGINEERING-CANONICAL-INPUT-PACK.md
```

These are explicitly non-authoritative and cannot satisfy issues #6, #7 or #9 by themselves.


## 10. Current readiness / remediation — 2026-10-02

Detailed current-state matrix:

`governance/SC-M01-CURRENT-READINESS-2026-10-02.md`

Audit tracking:

```text
AUDIT-WCP-0100 FINDINGS = 18 OPEN
ISSUE #41 = CURRENT REMEDIATION TRACKER
```

Current work packages:

```text
PR #42 = repository guardrails / tests / CI
         OPEN — E1+E2 complete on current candidate — E3 independent review still required

PR #43 = SC-M01 owner-decision pack
         MERGED AS P1 NON-NORMATIVE EVIDENCE — no owner decision accepted
```

PR #43 currently converts the implementation-critical semantic gaps into bounded owner decisions for:

- API transport / DTO / HTTP mapping;
- authorization parent/action/primitive binding;
- backend/worker → wcpd control boundary;
- IPC timeout/deadline/idempotency/UNKNOWN_OUTCOME semantics;
- USER/ADMIN web/session isolation;
- permission-resolution versus final Security authorization composition.

Its centralized matrix is:

`governance/proposals/SC-M01-OWNER-DECISION-MATRIX.md`

Current proposal inventory:

```text
TRACKED DECISION POINTS = 47
OPEN SEMANTIC OWNER DECISIONS = 45
RECOVERED OWNER CONSENSUS REQUIRING REPUBLICATION = 1
ALREADY DEFINED IN CURRENT CANONICAL ARTIFACTS = 1
LOCAL QA SCENARIOS PROPOSED = 85
APPLICATION TESTS EXECUTED FROM THOSE CASES = 0
```

These proposals were merged for traceability under REVIEW-MERGE-POLICY. Their merge does not ratify any D/T/C/H/S/F decision, does not add a ninth artifact to the SC-M01 gate and does not alter the official baselines.

Recovered H3-CM M1 evidence narrows the control-boundary gap:
- C01 physical control socket = recovered owner consensus awaiting current owner republication/confirmation;
- C02 transport access != application authorization = already defined by current DevOps/Security;
- C03–C07 remain open semantic decisions.

### Missing artifact recovery status

Targeted recovery from project conversations, Library files and GitHub history confirms:

```text
DATABASE #6
= final/source-ready owner metadata confirmed
= exact body still not materialized

QA #7
= Test Baseline v1.0 metadata approved
= final SC-M01 canonical test artifact not materialized / not confirmed

ENGINEERING #9
= final/source-ready owner metadata confirmed
= exact body still not materialized
```

Historical recovery gives additional evidence that the DB final contract contained the schemas
`wcp_core`, `wcp_iam`, `wcp_infra`, `wcp_hosting`,
`wcp_commercial`, `wcp_ops`, `wcp_audit`, and `wcp_backup`,
plus named core/operations objects. This evidence is **not sufficient to reconstruct
the exact owner artifact** and therefore does not close #6.

### Current readiness

```text
CANONICAL EVIDENCE = 5 / 8
CONTRACT REMEDIATION = OWNER REVIEW REQUIRED
REPOSITORY GUARDRAILS = OPEN PR / TESTED ON CURRENT MAIN / E3 NOT YET RECEIVED
SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

No generic documentation re-audit is requested. The next useful progress is owner decision/materialization, followed by the existing cross-check and resume checklist.


### Latest repository-guardrail evidence

PR #42 current synchronized candidate:

`a9fb5645a214f50414f6d9bd26ed7f2b49e3ad77`

GitHub Actions run:

`37072620136 = SUCCESS`

Verified merge-test evidence:

```text
BASE MAIN = 0aabae81fe6509b7e6567ef5ab12ecc1b9e86b1d
BASE FILES = 280
CANDIDATE FILES = 297
MERGE-TEST FILES = 297
MIRROR = 147 / 147
AUDIT FILES = 4 / 4
TARGETED SECRET FINDINGS = 0
BRANDING ORIGINALS = NOT_PUBLISHED
```

This remains E1 automated evidence plus E2 same-author verification. It does not satisfy E3 or branch-protection enforcement.
