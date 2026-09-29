# WCP360 — Parallel Workstreams

These are coordinated parallel workstreams, not independent authorities.

The connected assistant remains the single GitHub executor; owner-domain decisions remain with their WCP360 specialized discussions.

## WS-01 — Database Canonical Import

Owner authority: `WCP360 — DATABASE / DATA MODEL`

Tracking: issue #6

Input:
- DB MODEL VERSION = v1.0
- final owner publication metadata
- exact canonical physical contract

Output:
- `specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md`

Gate:
- exact owner artifact only;
- no reconstructed DDL;
- no migrations;
- source-ready.

## WS-02 — Tests / QA Canonical Publication

Owner authority: `WCP360 — TESTS / QA`

Tracking: issue #7

Input:
- TEST BASELINE VERSION = v1.0
- approved SC-M01 test requirements

Output:
- final SC-M01 canonical test artifact under `specifications/tests/`

Gate:
- no fabricated PASS;
- implementation-dependent tests remain PLANNED / READY until executed.

## WS-03 — Engineering Ruleset Import

Owner authority: `WCP360 — ENGINEERING / CODE REVIEW`

Tracking: issue #9

Input:
- `ENG-WCP-RULESET-0001 v0.2`
- exact final owner artifact

Output:
- `specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md`

Gate:
- final owner content only;
- no candidate ruleset substitution.

## WS-04 — Documentation Corpus Publication

Owner authority: `WCP360 — Documentation projet détaillée`

Tracking: issue #16

Input:
- physical `WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation.zip`

Output:
- exact Markdown corpus under `documentation/baselines/v4.0.21.23/`
- file manifest + SHA-256

Current preserved evidence:
- PR #15: complete 42-Markdown historical consolidated snapshot.

Gate:
- do not substitute Design System archive;
- do not silently normalize content.

## WS-05 — SC-M01 Master Gate

Owner authority: `WCP360 — MASTER / Project Control`

Tracking: issue #13

Current state:

```text
5 / 8 SC-M01 implementation artifacts present in GitHub
SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Resume condition:

```text
8 / 8 PRESENT
8 / 8 FINAL APPROVED
8 / 8 HISTORICAL CONTENT EXCLUDED
8 / 8 READY FOR SOURCE CODE CONSUMPTION
```

## Execution policy

Workstreams may progress in parallel.

No workstream may:
- redefine another owner's contract;
- change `WCP360-BM-0001 FINAL`;
- authorize SC-M02;
- assign a CODE-BASELINE;
- reintroduce cancelled Sprint 0 or V4.1;
- import secrets or production credentials.
