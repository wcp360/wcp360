# WCP360 — Current SC-M01 Readiness — 2026-10-26

**Status:** CURRENT G1 COORDINATION SNAPSHOT — NON-NORMATIVE, NOT AN IMPLEMENTATION APPROVAL  
**Repository:** `yyogas/wcp360`  
**Main as inspected:** `1da10e957529e77c78b2efff55f76b1171f7e1db`  
**Previous reference:** `governance/SC-M01-CURRENT-READINESS-2026-10-04.md`  
**Master issue:** #13; **Audit tracker:** #41.

This coordination file supersedes earlier *readiness summaries for coordination only*. It does **not** replace any owner-approved contract, change the WCP360 Baseline Manifest, close an audit finding, approve a toolchain, or start CODE-BASELINE.

## 1. Immutable implementation gate

```text
WCP360-BM-0001 = FINAL
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4

REQUIRED OWNER IMPLEMENTATION ARTIFACTS = 8
PRESENT / CONSUMABLE IN MAIN = 5
OWNER FINAL ARTIFACTS MISSING = 3

SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

The five source-ready artifacts remain API 1.0, IPC WCP360-IPC-B/1, Permission Registry v1.0, Security v1.0, and DevOps v1.0.

Missing:
- **Database #6:** exact final v1.0 physical owner contract not materialized, or owner-approved complete reconstruction/amendment not yet published.
- **QA #7:** final accepted SC-M01 Test Baseline artifact not materialized.
- **Engineering #9:** exact final ENG-WCP-RULESET-0001 v0.2 export not materialized, or owner-approved amended complete ruleset not published.

The temporary owner decision candidates are NOT substitutes for those files.

## 2. What has actually been consolidated in main

On 2026-10-26 the following non-normative G1/P1 contributions were merged after E0 authored + E2 same-author publication review, as permitted by `governance/REVIEW-MERGE-POLICY.md`:

| PR | Merge SHA | Status / authority |
|---|---|---|
| #83 | `131eb1990f14b4d626f1fdb02c257bba3053f739` | G1 owner handoff/checklist/first-code plan, noncanonical |
| #84 | `286aca541a5b3775a56e5818fa86703d384e01cf` | P1 Database DB-R01..15 proposal, not final DB |
| #85 | `075349fbc589b2d4233137b0f92875f305afcbd9` | P1 Engineering ENG-R01..20 proposal, not final Engineering |
| #89 | `1da10e957529e77c78b2efff55f76b1171f7e1db` | P1 F01–F10 Debian13 toolchain R&D alternatives, not adopted stack |

These files live in `governance/` and are explicitly marked non-canonical; they do not populate `specifications/` or `src/`. These merges have **no independent E3** and claim no owner ratification; E3 is not required merely to archive G1/P1 material under current policy.

## 3. Tested but still unmerged R1/tooling and R&D

- **PR #86 (DRAFT):** read-only eight-input fingerprint checker, 13 negative tests.
- **PR #87 (DRAFT):** derived 86 API + 47 IPC + 82 permission test-reference snapshot/checks, 20 tests; 133 linked test references are all NOT_EXECUTED and are NOT the QA owner baseline.
- **PR #88 (DRAFT):** C03/C06/C07 inert parser research, 37 tests; synthetic identities and candidate wire semantics are not production authorization.
- **PR #90 (DRAFT):** cross-PR integration review with a new Security/IPC 47-operation mapping checker, 19 additional tests. The 21 files copied from previous PRs include material now archived in main. **PR #90 is NOT a release/owner authority** and should eventually be superseded or closed after properly reviewed target PR integration.
- **PR #42 (OPEN):** repository security/CI guardrails, independent E3 and main enforcement still missing.

E1 evidence at exact PR #90 head `b1ae801dbc82f0972ab94a9caf0156b86b0b82f1`:

```text
FINGERPRINT TESTS = 13 PASS
API/IPC/PERMISSION REFERENCE TESTS = 20 PASS
SECURITY/IPC REFERENCE TESTS = 21 PASS
INERT CONTROL PARSER RESEARCH TESTS = 37 PASS
TOTAL NON-PRODUCT E1 UNIT TESTS = 91 PASS
```

GitHub Actions run: https://github.com/yyogas/wcp360/actions/runs/40417038572

All four PR-triggered E1 workflows succeeded at the tested PR #90 SHA. These are repository validation/research tests, not actual WCP360 product QA, security certification, independent E3 or implementation readiness.

## 4. Newly narrowed high-impact security/contract work

- Security v1.0 and IPC-B/1 enumerate the **same 47 exact privileged operation IDs**.
- **35 operations** have directly named permission expressions in the Security matrix. This does not establish all their origin/technical bindings as executable.
- **12 operations** contain composite/approved-parent/OR expressions requiring explicit Security + API/IPC owner decisions before dispatcher code. Targeted owner issue: **#91**.
- `server.reboot` and `server.shutdown` annotate both `requested` and `completed` audit events, resulting in **49 semantic audit-event references over 47 operations**. Do not use the plus-separated text as one `audit_event_id` or count it as a test result.

Required owner input is in:
`governance/handoffs/SC-M01-SECURITY-IPC-PARENT-BINDING-REVIEW-2026-10-26.md` (currently part of unmerged draft PR #90).

Control C03/C06/C07 and toolchain F01–F10 remain proposals, not final component contracts.

## 5. Actionable path to code

1. DATABASE owner #6 reviews the DB-R01..15 proposal now versioned in main, republishes retained final source or explicitly approves a complete amended physical contract with honest version/content delta.
2. ENGINEERING owner #9 reviews ENG-R01..20 and F01–F10 options now in main and publishes a final accepted ruleset/toolchain decision with source provenance and QA/Security/DevOps impacts.
3. TESTS / QA owner #7 ratifies A01..A20 and publishes the final full SC-M01 test baseline. Source-reference tooling can verify identifier coverage but cannot approve expected results.
4. SECURITY + API/IPC + DEVOPS ratify only the exact C03/C06/C07 and #91 parent bindings needed for each implementation slice, documenting negative cases and version impact; preserve fail-closed boundaries.
5. Repository R1 tooling PRs #42, #86, #87 and #90 require E1 + independent E3 before merger into main; the tested state of PR #90 alone cannot satisfy E3.
6. AUDIT + MASTER verify **all eight final owner files in main**, applicable component semantics and exact SHA/reviewer/QA evidence, then explicitly authorize `SC-M01`.
7. SOURCE CODE implements approved `SC-M01` narrow slices in `governance/handoffs/SC-M01-FIRST-CODE-DEPENDENCY-PLAN-2026-10-26.md`, never resurrecting cancelled Sprint 0 or V4.1.

## 6. Scope/authority reminder

```text
account_id != organization_id != tenant_id
CONTROL-PLANE POSTGRESQL != HOSTED DB ENGINES
WCP-USER != WCP-ADMIN

backend → wcpd(non-root) → bounded authenticated IPC-B
→ wcp-privd(minimal root) → allowlisted privileged primitive
```

No arbitrary shell, generic sudo/systemctl, unknown IPC operation, unbounded path, fabricated reviewer, fabricated test PASS or premature release GO.

## 7. Status verdict

```text
DOCUMENTATION/GOVERNANCE CONSOLIDATION = PROGRESS — 4 NONCANONICAL PRs MERGED
REPOSITORY/RESEARCH E1 = 89 TESTS VERIFIED ON UNMERGED PR #90
SC-M01 CANONICAL OWNER EVIDENCE = 5/8
SC-M01 RESUME = NOT AUTHORIZED
CODE-BASELINE = NONE
```

This snapshot is coordination evidence, not an official new implementation baseline.


## 8. Addendum — 2026-10-27 (new evidence only)

The original 2026-10-26 snapshot above remains a dated reference rather than being overwritten.

- **QA owner-review proposal #93** was published as a **P1 NON-CANONICAL** document in main (merge commit `078bdf5d71ab71c9c47094fce4bf7c7f18d87a87`): `governance/proposals/SC-M01-QA-A01-A20-OWNER-DECISION-PACKET-v0.1.md`. It presents all A01–A20 owner decisions and QI-01..07 execution-integrity choices, but is **not** the final `specifications/tests/` owner artifact.
- **Go IPC-B/1 research PR #94** remains **DRAFT, UNMERGED** and non-production. Its source-derived 47-operation parser covers in-memory request/response framing, canonical error codes and syntactic UNKNOWN_OUTCOME→reconciliation conditions. GitHub Actions run [40420580435](https://github.com/yyogas/wcp360/actions/runs/40420580435) succeeded at head `0ae0fe5cf8c1bcb75be940053f12de21ff0e1344` (`go test` + `go vet`, Go 1.24.13 research runner). No daemon, socket, root effect, or privileged dispatch is implemented.
- Owner artifacts remain **5/8**; #6 Database, #7 QA, #9 Engineering missing as source-ready canonical bodies. GitHub #91 and Security/API owner decisions remain open.
- No owner approval, independent E3, code implementation authorization, SC-M01 resume or CODE-BASELINE is implied by these contributions.

```text
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```
