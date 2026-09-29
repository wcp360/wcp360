# WCP360 — Coordinated Workstreams

**Canonical workstream registry**

These workstreams are coordinated by `WCP360 — MASTER / Project Control` and may execute in parallel where possible.

They are not independent authorities. Owner-domain decisions remain authoritative.

## WS-DOC-MIRROR — Materialized Markdown Mirror

Status: **COMPLETE**

Responsibilities:
- mirror every Markdown file currently materialized under the WCP360 Project/Library into GitHub;
- preserve original relative paths;
- preserve historical/candidate/staging/audit status;
- never promote mirror content into implementation authority automatically.

Verified:
```text
SOURCE MATERIALIZED MARKDOWN = 147
MIRRORED SOURCE MARKDOWN = 147
MISSING = 0
```

Evidence:
- PR #25 — full mirror;
- PR #26 — inventory updated;
- PR #32 — integrity manifest.

Paths:
```text
documentation/library-mirror/
documentation/library-mirror/MANIFEST.md
```

## WS-DOC-BASELINE — Canonical Documentation Baseline Materialization

Status: **OPEN — NOT SC-M01 BLOCKING**

Owner:
`WCP360 — Documentation projet détaillée`

Tracking:
issue #16

Required physical source:
```text
WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation.zip
DOC-BASELINE = v4.0.21.23
```

Target:
```text
documentation/baselines/v4.0.21.23/
```

Rules:
- publication/materialization only;
- no broad Documentation re-audit;
- no substitution with an older v4.0.21.x archive;
- no Design System archive substitution;
- preserve exact paths/content;
- historical content remains historical.

The complete Project/Library Markdown mirror does **not** claim to be the physical canonical DOC archive.

## WS-DB — Database Canonical Evidence

Status: **OPEN**

Owner:
`WCP360 — DATABASE / DATA MODEL`

Required artifact:
`WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md`

Tracking:
issue #6

Owner publication metadata:
```text
DB MODEL VERSION = v1.0
FINAL APPROVED CONTENT = YES
PHYSICAL CONTRACT = INCLUDED
HISTORICAL CONTENT INCLUDED = NO
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

Remaining action:
materialize/import the exact owner file body into:
`specifications/database/`.

No reconstruction from audit summaries. No DDL. No migrations.

## WS-QA — Test Canonical Evidence

Status: **OPEN**

Owner:
`WCP360 — TESTS / QA`

Tracking:
issue #7

Required:
final SC-M01 Test Baseline v1.0 implementation artifact under `specifications/tests/`.

QA preparation already available:
```text
governance/handoffs/SC-M01-QA-CANONICAL-INPUT-PACK.md
```

The input pack extracts:
- 86 canonical TEST-API references;
- 47 canonical TEST-IPC references;
- 82 active permissions;
- 26 privileged negative-security cases;
- TEST-SEC-WEB-001 → TEST-SEC-WEB-040;
- DevOps/platform validation obligations.

Rules:
```text
implementation-dependent tests = PLANNED / READY / BLOCKED
fabricated PASS = forbidden
fabricated FAIL = forbidden
CODE-BASELINE = NONE
```

The input pack is not the final QA owner artifact.

## WS-ENG — Engineering Canonical Evidence

Status: **OPEN**

Owner:
`WCP360 — ENGINEERING / CODE REVIEW`

Logical identity:
```text
ENG-WCP-RULESET-0001 v0.2
```

Exact final owner export:
```text
WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md
```

Tracking:
issue #9

Owner publication metadata:
```text
VERSION = v0.2
FINAL APPROVED CONTENT = YES
HISTORICAL CONTENT INCLUDED = NO
CONTENT CHANGE = NONE
READY FOR SOURCE CODE CONSUMPTION = YES
BLOCKER = NONE
```

Remaining action:
materialize/import the exact owner export into `specifications/engineering/`.

No candidate ruleset substitution.

## WS-SC-M01 — Implementation Gate

Status: **PAUSED — TARGETED EVIDENCE**

Tracking:
issue #13

Current canonical evidence:
```text
5 / 8 PRESENT IN specifications/
```

Present:
- API Contract 1.0;
- IPC Contract WCP360-IPC-B/1;
- Permission Registry v1.0;
- Security Baseline v1.0;
- DevOps Baseline v1.0.

Remaining:
- DB Model v1.0;
- Test Baseline v1.0 / SC-M01;
- Engineering Ruleset v0.2 export.

Resume condition:
```text
8 / 8 PRESENT
8 / 8 FINAL APPROVED
8 / 8 HISTORICAL CONTENT EXCLUDED
8 / 8 SOURCE-CODE CONSUMABLE
```

Until then:
```text
SOURCE CODE = PAUSED
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

## Global execution policy

Workstreams may progress in parallel.

No workstream may:
- redefine another owner's contract;
- change `WCP360-BM-0001 FINAL`;
- authorize SC-M02;
- assign a CODE-BASELINE;
- reintroduce cancelled Sprint 0 or V4.1;
- import secrets or production credentials;
- use documentary mirror material as automatic implementation authority.
