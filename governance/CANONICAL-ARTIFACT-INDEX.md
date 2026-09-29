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
| Database | `WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md` | FINAL APPROVED / READY / BLOCKER NONE | PENDING — issue #6 |
| Tests / QA | final SC-M01 Test Baseline v1.0 artifact | NOT YET MATERIALIZED | PENDING — issue #7 |
| Engineering | `WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md` | FINAL APPROVED / READY / BLOCKER NONE | PENDING — issue #9 |

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
