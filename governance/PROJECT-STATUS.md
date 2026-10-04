# WCP360 — Project Status

**Reference date:** 2026-10-04  
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

QA owner review aid:
`governance/handoffs/SC-M01-QA-OWNER-ACCEPTANCE-CHECKLIST.md`

Extracted input:
- 86 TEST-API references;
- 47 TEST-IPC references;
- 82 active permissions;
- N01–N26 privileged negative-security catalog;
- TEST-SEC-WEB-001 → TEST-SEC-WEB-040;
- DevOps/platform validation obligations.

Input support: `governance/handoffs/SC-M01-QA-CANONICAL-INPUT-PACK.md`  
Tracking: issue #7.

Current evidence indicates that v1.0 metadata existed but no complete prior final QA body has been evidenced. QA must therefore declare publication provenance/content change explicitly; `CONTENT CHANGE = NONE` is not assumed.

The owner candidate was hardened on 2026-10-04 with explicit execution-integrity rules: rerun history, flaky/quarantine handling, deterministic seeds/clocks, parallel fixture isolation and durable evidence retention. It remains non-canonical and issue #7 remains OPEN.

### Engineering

Logical identity:
```text
ENG-WCP-RULESET-0001 v0.2
```

Exact final owner export:
```text
WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md
```

Historical owner metadata:
```text
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

The exact final Engineering body is not currently recovered. The historical `CONTENT CHANGE = NONE` metadata may be reused only if Engineering can republish the exact retained final source with provenance. Any owner reconstruction/amendment must declare a real delta and version impact.

Remaining:
exact file body republication/materialization, or explicit owner reconstruction/amendment if the original cannot be recovered.

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


## 10. Current readiness — 2026-10-04

Detailed current-state authority:

`governance/SC-M01-CURRENT-READINESS-2026-10-04.md`

Audit closure criteria:

`governance/audits/AUDIT-WCP-0100/CLOSURE-MATRIX.md`

Owner decision/harmonization matrix:

`governance/proposals/SC-M01-OWNER-DECISION-MATRIX.md`

Current owner-finalization aids:

```text
Database
→ governance/handoffs/SC-M01-DB-OWNER-REPUBLICATION-CHECKLIST.md
→ DB-R01..DB-R15

Tests / QA
→ governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md
→ governance/handoffs/SC-M01-QA-CANONICAL-COVERAGE-INDEX.md
→ governance/handoffs/SC-M01-QA-OWNER-ACCEPTANCE-CHECKLIST.md
→ A01..A20

Engineering
→ governance/handoffs/SC-M01-ENGINEERING-OWNER-REPUBLICATION-CHECKLIST.md
→ ENG-R01..ENG-R20
```

Current semantic-evidence narrowing in main includes:

- recovered H3CM-X004 timeout evidence: I/O 5s per complete frame read/write; T01 no longer an open design choice;
- canonical Web/session security evidence: cookie/session/CSRF/Origin/CORS core final; only topology/proxy/WSS/wrong-cookie details remain;

- canonical API transport evidence + API Transport Delta v0.2;
- canonical Security composition evidence + Security Web/Authz Delta v0.2;
- canonical timeout/worker evidence;
- canonical control-boundary evidence;
- recovered authorization context / UNKNOWN_OUTCOME evidence;
- recovered traceability/idempotency evidence;
- recovered M1 control-boundary evidence;
- recovered DB/Engineering historical evidence.

These evidence/proposal files are not substitutes for owner normative artifacts.

### Repository guardrails

PR #42 remains open.

Review class:

```text
R1 repository tooling
E1 automated evidence = YES
E2 same-author publication control = YES
E3 independent review = MISSING
```

Latest synchronized/tested candidate before the current status refresh:

```text
head = 273bc13a3ec6567b0ce498b062f1b8effd2b7902
run = 37221317542 SUCCESS
merge-test = f2381aae2673bb0a714f3f47ef3e6e994e226797
candidate files = 326
candidate bytes = 1,540,685
mirror = 147/147
audit = 4/4
targeted secret findings = 0
```

Main advanced afterward with additional documentation/evidence, so a future #42 merge still requires resynchronization to the then-current main plus E3.

Main branch enforcement is not claimed active:

```text
protected = false
required checks = none
```

The installed GitHub integration does not provide Administration permission to configure branch protection/rulesets.

### Current gate

```text
CANONICAL EVIDENCE = 5 / 8

DATABASE #6 = exact owner body missing
QA #7 = final owner artifact missing
ENGINEERING #9 = exact owner body missing

SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Current coordination additionally uses:

- `governance/SC-M01-DECISION-ACTIVATION-MATRIX.md`;
- `governance/reviews/PR42-E3-INDEPENDENT-REVIEW-PACKET.md`;
- `governance/GITHUB-MAIN-ENFORCEMENT-PLAN.md`;
- DB/Engineering republication conformance evidence;
- QA A01→A20 conformance evidence.

The remaining progress is owner publication/harmonization, real independent review and actual GitHub enforcement, not another broad documentation audit.
