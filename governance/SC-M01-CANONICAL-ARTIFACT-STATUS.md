# SC-M01 — Canonical Implementation Artifact Status

Reference: `WCP360-BM-0001 FINAL / SC-M01 TARGETED EVIDENCE`

Last updated: 2026-10-03

## Current implementation gate

```text
SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

## Artifact inventory

| Artifact | Version | Owner | Approved | Present in GitHub main | Source Code consumable from GitHub | Tracking |
|---|---|---|---|---|---|---|
| API Contract | 1.0 | API / IPC / CONTRACTS | YES | YES | YES | issue #4 CLOSED / PR #10 |
| IPC Contract | WCP360-IPC-B/1 | API / IPC / CONTRACTS | YES | YES | YES | issue #5 CLOSED / PR #10 |
| Permission Registry | v1.0 | SECURITY / HARDENING | YES | YES | YES | PR #2 |
| Security Baseline | v1.0 | SECURITY / HARDENING | YES | YES | YES | PR #2 |
| DB Model | v1.0 | DATABASE / DATA MODEL | YES | NO | NO | issue #6 OPEN — publication confirmed, full file import pending |
| Test Baseline / SC-M01 requirements | v1.0 | TESTS / QA | BASELINE YES | NO | NO | issue #7 OPEN — final SC-M01 artifact not yet materialized |
| DevOps Baseline / SC-M01 requirements | v1.0 | DEVOPS / BUILD / RELEASE | YES | YES | YES | issue #8 CLOSED / PR #11 |
| Engineering Ruleset | ENG-WCP-RULESET-0001 v0.2 | ENGINEERING / CODE REVIEW | YES | NO | NO | issue #9 OPEN — publication confirmed, full file import pending |

## Canonical files already in main

```text
specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md
```

## Remaining evidence

### Database

Expected final owner artifact:

```text
WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
```

Publication status is confirmed as final approved / source-ready / blocker none. Conversation-history recovery also confirms that the complete final body was previously published, but the original body is still not fully recoverable.

Non-authoritative recovery evidence:

`governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md`

This evidence must not be imported under `specifications/database/`; issue #6 still requires the exact final owner file or a versioned owner republication/amendment.

### Tests / QA

A final SC-M01-specific canonical implementation-test artifact still needs to be produced/approved by TESTS / QA.

A self-contained non-canonical owner candidate now exists:

`governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md`

The candidate creates no PASS/FAIL and cannot satisfy issue #7 until TESTS / QA publishes its final artifact.

### Engineering

Expected final owner artifact:

```text
WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md
```

Publication status is confirmed as final approved / source-ready / blocker none, but the complete final body remains unavailable.

Historical candidate recovery evidence:

`governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md`

This evidence explicitly separates Phase G/H3-C candidate content from the missing final export and cannot satisfy issue #9.

## Completion gate

SC-M01 may resume only when every required implementation artifact is:

1. final approved;
2. version-identifiable;
3. present in the repository;
4. free of superseded/historical authority conflicts;
5. directly consumable by SOURCE CODE.

Target:

```text
8 / 8 REQUIRED IMPLEMENTATION ARTIFACTS
PRESENT IN GITHUB
READY FOR SOURCE CODE CONSUMPTION
```

Current repository state:

```text
5 / 8 PRESENT AND CONSUMABLE
3 / 8 NOT YET PRESENT IN GITHUB
```

This status file does not change any baseline or owner decision.


## Recovery/support evidence — non-canonical

| Missing domain | Recovery/support | Authority |
|---|---|---|
| Database | `governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md` | historical recovery only |
| Tests / QA | `governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-OWNER-CANDIDATE.md` | owner candidate only |
| Engineering | `governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md` | historical candidate recovery only |

These files reduce rediscovery; they do not increase the 5/8 count.
