# WCP360 — Canonical Artifact Index

Reference: `WCP360-BM-0001 FINAL`

This index identifies which artifacts are currently implementation authority from GitHub.

## Canonical implementation artifacts present in main

| Domain | Artifact | Version | Status |
|---|---|---:|---|
| API | `specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | 1.0 | FINAL / SOURCE-READY |
| IPC | `specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md` | WCP360-IPC-B/1 | FINAL / SOURCE-READY |
| Security | `specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | v1.0 | FINAL / SOURCE-READY |
| Security | `specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | v1.0 | FINAL / SOURCE-READY |
| DevOps | `specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md` | v1.0 | FINAL / SOURCE-READY |

## Remaining SC-M01 canonical evidence

| Domain | Required artifact | Owner publication | GitHub import |
|---|---|---|---|
| Database | `WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md` | FINAL APPROVED / READY / BLOCKER NONE; prior final body existence confirmed | PENDING — exact owner body / owner republication — issue #6 |
| Tests / QA | final SC-M01 Test Baseline v1.0 artifact | FINAL OWNER ARTIFACT NOT YET PUBLISHED; owner candidate exists | PENDING — owner review/publication — issue #7 |
| Engineering | `WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md` | FINAL APPROVED / READY / BLOCKER NONE; historical candidate evidence recovered | PENDING — exact final owner body / owner republication — issue #9 |

Master gate: issue #13.

## Current gate

```text
5 / 8 REQUIRED IMPLEMENTATION ARTIFACTS
PRESENT AND CONSUMABLE FROM GITHUB

SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

No missing artifact may be reconstructed from superseded drafts, candidate outputs or general knowledge.


## Non-canonical recovery / preparation aids

These files are versioned to reduce owner rediscovery but are **not implementation authority**:

| Domain | File | Status |
|---|---|---|
| Database | `governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md` | incomplete historical final-artifact recovery |
| Tests / QA | `governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md` | self-contained owner candidate |
| Engineering | `governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md` | historical Phase G/H3-C candidate recovery |

They do not change the 5/8 count.

## Current owner action

```text
DATABASE
→ exact final republication OR explicit owner amendment/version impact

TESTS / QA
→ review/amend owner candidate
→ publish final v1.0 canonical artifact

ENGINEERING
→ exact final republication OR explicit owner amendment/version impact
```
