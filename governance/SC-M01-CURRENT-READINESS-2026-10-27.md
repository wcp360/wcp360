# WCP360 — SC-M01 Current Readiness — 2026-10-27

**Status:** G1 MASTER COORDINATION SNAPSHOT — **NON-CANONICAL / NO IMPLEMENTATION GO**  
**Repository:** `yyogas/wcp360`  
**Main reference examined:** `a90b8a24ad29c5d43f16d83f3e18acd2e661b90b`  
**Parent authority:** `manifests/WCP360-BM-0001.md` (`FINAL`)  
**Owner decision gate:** [#13](https://github.com/yyogas/wcp360/issues/13)  
**Independent audit tracker:** [#41](https://github.com/yyogas/wcp360/issues/41)

This snapshot supersedes dated **coordination status** documents for day-to-day planning. It does **not** alter `WCP360-BM-0001`, implementation contracts, owner final decisions, review policy, or CODE-BASELINE. Older snapshots and cancelled Sprint 0/V4.1 remain historical-only.

## 1. Canonical baseline and global SC-M01 gate

```text
WCP360-BM-0001 = FINAL
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4

REQUIRED OWNER IMPLEMENTATION ARTIFACTS = 8
SOURCE-READY / MATERIALIZED IN MAIN = 5
OWNER FINAL ARTIFACTS STILL MISSING = 3

SOURCE CODE = PAUSED
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

| Owner artifact | Version | Present in main? | Authority / next action |
|---|---|---|---|
| API CONTRACT | 1.0 | YES | Owner final, fixed typed API contracts |
| IPC CONTRACT | WCP360-IPC-B/1 | YES | Owner final, bounded 47-operation privileged protocol |
| PERMISSION REGISTRY | v1.0 | YES | Owner final, 82 active permissions |
| SECURITY BASELINE | v1.0 | YES | Owner final, privilege/tenant/session boundary |
| **DATABASE MODEL** | v1.0 metadata | **NO** | **#6:** exact full body or owner-amended/reconstructed complete physical contract |
| **TEST BASELINE** | v1.0 metadata | **NO** | **#7:** QA owner acceptance A01–A20, final complete artifact and honest provenance |
| DEVOPS BASELINE | v1.0 | YES | Owner final, Debian 13 amd64 and filesystem/Unix/socket baseline |
| **ENGINEERING RULESET** | ENG-WCP-RULESET-0001 v0.2 metadata | **NO** | **#9:** complete retained final body or owner-amended full ruleset/version decision |

**Present** means owner-final implementation artifact exists and is consumable at an approved path; recovered metadata, proposal/approval wording, candidate files or indexed fragments do not count.

## 2. Priority order to unlock the first code

| Rank | Owner / scope | Concrete deliverable required | Current material available | Blocking implication |
|---|---|---|---|---|
| **P0-A** | DATABASE #6 | Complete physical schema with exact PK/FK/composite tenant constraints, nullability, unique/check/delete semantics, indexes, transactions, concurrency/leases/fencing, durable audit/idempotency/outbox and retention/restore | P1 DB-R01..R15 owner draft in main (#84); 36 recovered partial table sections indexed as G0 (#96) | First DB migrations, jobs/leases and persistence code blocked |
| **P0-B** | TESTS / QA #7 | Owner-ratified complete SC-M01 test baseline with A01–A20, test records/outcomes/evidence lifecycle, truthful publication type | Existing 2026-10-03 complete **candidate** + review checklist; P1 A01–A20/QI01..07 recommendations in main (#93); 86/47/82 reference index | First CODE-BASELINE/product QA gate blocked |
| **P0-C** | ENGINEERING #9 | Retained exact final ENG v0.2 body OR explicit complete owner-amended ruleset with F01..F10 toolchain/CI/DoD/security/quality decisions and version impact | P1 ENG-R01..20 proposal (#85) + Debian13 F01–F10 options (#89) in main; historical owner metadata | First buildable application/runtime/repository skeleton blocked |
| **P0-D** | SECURITY + API/IPC #91 | Exact approved 12 parent/provisioning/OR permission→origin action→IPC operation tuples with surface/scope/resource constraints and negative tests | G1/P1 precise owner HANDOFF in main (#95); Security/IPC source mapping 47/47 in focused R1 PR #87 | Those privileged dispatcher bindings blocked (other direct rows still need full checks) |
| **P0-E** | API/IPC + SECURITY + DEVOPS | C01 republication and C03 peer→surface, C06 control wire protocol, C07 business/orchestration/durable intent split as applicable | P1 alternatives in main (#95); inert Python R&D #88 | First functional backend→wcpd control flow blocked |
| **P0-F** | ENGINEERING + independent reviewer / DevOps/Security | Actual E3 exact-SHA reviews of R1 tooling and first implementation; enforce branch checks/protection | PR #42 repository guardrails still open; #86/#87/#88/#94 R1/research validation candidates | Critical R1/I1/S1 merges and first CODE-BASELINE review blocked |
| P1 | Documentation #16 | Physical archive DOC v4.0.21.23 import/provenance | 147-file materialized mirror + Design mirror in main | Tracked separately, **not** SC-M01 global 8/8 gate blocker by current governance |

**Owner decision hierarchy:** FINAL owner source > ratified new owner delta with version impact > P1/G1 proposal > G0 historical recovery. Source Code must not interpret lower-level material as a higher-level authority.

## 3. What has been meaningfully published in main

The following are **G0/G1/P1 non-authoritative** publication merges, never normative owner contract replacements:

- PR #83: owner handoff, independent gate and first-code M01-00..10 dependency plan.
- PR #84: DB-R01..15 owner decision draft, not a physical database model.
- PR #85: ENG-R01..20 ruleset candidate, not a recovered full Engineering final.
- PR #89: F01..10 Debian13 toolchain R&D options, not final PHP/Go/PostgreSQL/Node selection.
- PR #93: QA A01–A20/P1-P2-P3 owner signoff recommendations, not Tests/QA final.
- PR #95: C03/C06/C07 control owner options and 12 privileged parent-binding owner handoff.
- PR #96: strictly derived, line-referenced historical PostgreSQL table-fragment index: 36 named sections in eight schemas; 33 partial typed first blocks, two untyped inventories, one name-only. **No complete table/full model is proved.**

The G0/G1/P1 publication review level is **E0 authored + E2 same-author** under `governance/REVIEW-MERGE-POLICY.md`. E2 must never be reported as independent E3 approval.

## 4. Code and test preparation retained separately, NOT merged

| PR | Candidate | Verified subset and limitations | Review status |
|---|---|---|---|
| #42 | Repo secret/publication guardrails | Existing E1 CI tested, no enforced main protection inferred | R1, E3 missing |
| #86 | 8-artifact source fingerprint validator | 13 non-product negative tests in its last green E1 run; 5 published source pins | R1, E3 missing |
| #87 | API/IPC/permission + Security/IPC typed reference checks | **20 + 21 = 41** E1 tests PASS on `93671d5c5f3971d1eb775f8dfc24f20f1a8ad7bd` in [run 40474749578](https://github.com/yyogas/wcp360/actions/runs/40474749578); 86/47/82 coverage, 12 abstract privilege expressions reported | R1, E3 missing |
| #88 | Inert Python backend→wcpd control parser options | 37 synthetic parser checks, no socket or actual peer credentials | Research + tooling, owner C03/C06/C07 and E3 pending |
| #94 | Pure Go IPC-B request/response framing research | [run 40421435103](https://github.com/yyogas/wcp360/actions/runs/40421435103): `go test` + `go vet` + actual bounded fuzzing (request 11 execs, response 75,421 execs); no daemon/root/OS effect | Research-only, F02 toolchain owner/E3 pending |

Former integration PR **#90 = CLOSED WITHOUT MERGE**, superseded by focused #86/#87/#88 and main P1 docs. Its 91-test E1 combined evidence remains GitHub history but cannot be counted as production QA.

**Do not sum these tests into SC-M01 product QA PASS.** They establish limited reference, framing and inert rejection behavior only.

## 5. Previously confirmed architectural and security invariants

```text
WCP-USER != WCP-ADMIN

account_id != organization_id != tenant_id

CONTROL-PLANE POSTGRESQL
!= HOSTED DB ENGINES
!= TENANT / APPLICATION DATABASE CONTENT

backend → wcpd(non-root)
→ bounded authenticated IPC-B
→ wcp-privd(minimal root)
→ allowlisted typed privileged primitive

shell.exec / arbitrary command / generic sudo/systemctl = FORBIDDEN
unknown permission/action/IPC operation = DENY / NO EFFECT
timeout != definite failure
retry != reconciliation
rollback != compensation
```

Security v1.0 and IPC-B/1 match all **47** declared privileged operation IDs. Security permission expressions contain **35 directly named permission IDs** and **12 owner-dependent parent/provisioning/OR expressions** (issue #91). `server.reboot` and `server.shutdown` each refer to both `requested` and `completed` audit event types (49 event-type refs across 47 operations). No generic privileged execution or automatic mapping is allowed.

## 6. M01-00 / M01-01 readiness decision

**Current answer: NOT AUTHORIZED.** Before the first executable foundation PR:

1. Materialize all three missing complete owner-final DB/QA/Engineering files, with exact version/path/source Git SHA/provenance, honest content-change classification, cross-domain review and owner-ready statements.
2. Independently verify 8/8 in `specifications/`; reconcile `WCP360-BM-0001` version impact where a new owner amendment is adopted.
3. Accept only the component-specific semantic decisions needed for the first M01-00/01 slice; do not globally block on unrelated future-module decisions, but never bypass a real blocker for the chosen component.
4. Enforce code-review policy on the exact implementation candidate (E1 + independent E3). Release/package/install work needs separate DevOps/QA evidence.
5. Record an explicit MASTER `SC-M01 RESUME AUTHORIZATION` attached to an exact Git SHA. Only then begin executable first code.

Until then:

```text
SC-M01 OWNER ARTIFACT GATE = 5/8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

This file is a coordination record, not a new implementation baseline.


## 7. Latest owner handoff inputs (2026-10-27 late update)

Owner decision material was further consolidated in `governance/SC-M01-OWNER-SIGNOFF-INPUTS-2026-10-27.json`, a **G1 noncanonical index of exact Git source blobs**, not an implementation manifest.

The **three P1 candidate packages are now preserved in main**, without owner acceptance:

| Owner | Source-review PR and merge | Exact additional decisions |
|---|---|---|
| DATABASE #6 | PR #100 merged `87e541d2830994207a06d53e4b285c7ea531def2` | 36 named recovered partial tables + DB-X01..10, original exact final v1.0 still unrecovered; v1.1 only a *proposed* amendment if owner approves |
| TESTS/QA #7 | PR #98 merged `55a5421fdf1333e916f6b0ea8e8e429141018ad7` | 133 owner-review TEST-API/TEST-IPC records, all `BLOCKED / NOT_EXECUTED`; preserve N01..N26 and WEB001..040 separately |
| ENGINEERING #9 | PR #99 merged `1c14eb11c42e1517128b9257d98f0d9ffa917ba6` | F01..10 explicit toolchain/CI options; **no runtime adopted**, possible owner-amended v0.3 proposal requires real content/version change declaration |

The Go IPC-B inert R&D PR #94 now checks source-derived **required/optional field names for all 47 IPC-B operations**; E1 GitHub Actions [run 40482150906](https://github.com/yyogas/wcp360/actions/runs/40482150906) passed its tests, `go vet`, and short actual bounded fuzzing. This is **name-level only**, not field-value type validation, identity/authorization/resource verification, daemon implementation or product QA.

The source review bundles **do not count towards 8/8**. Actual final owner artifacts remain absent for #6/#7/#9. Open #91 Security/API parent bindings and necessary C03/C06/C07 decisions remain component-specific blockers for privileged work. E3 independent review remains missing for R1/I1/S1.

```text
G1/P1 OWNER REVIEW PACKETS = PUBLISHED
DB / QA / ENG FINAL OWNER SIGNOFF = MISSING
SC-M01 GATE = 5 / 8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```


## 8. Late 2026-10-27 owner-case and Engineering signoff delta (G1)

This dated addendum does NOT replace the 8/8 gate or any owner contract. The exact source records are indexed in `governance/SC-M01-OWNER-SIGNOFF-INPUTS-2026-10-27.json`.

- **QA P1 #102 merged** as noncanonical proposed **N01..N26** negative test labels (26 exact Security v1.0 source matches) plus 40 WEB ID references with **NEW proposed per-ID scenario mappings**, *not* old source-approved per-ID assignments. Together with QA P1 #98, there are **199 source-linked owner-review records**. All BLOCKED/NOT_EXECUTED, zero actual product QA PASS/FAIL or owner signoff. QA #7 remains OPEN.
- **R1 #87** added a read-only QA review-input source integrity validator + 20 adversarial tests on the 199-case corpus. [GitHub Actions 40587310504](https://github.com/yyogas/wcp360/actions/runs/40587310504) succeeded at `92f563c279b8444d380d745cf9e6f35691b1c641`: 20 API/IPC/Permission + 21 Security/IPC + 20 QA fake-PASS/provenance negative tests = **61 unit tests**. R1 E3 independent review MISSING; do not merge as an approved protected gate from E1 alone.
- **Engineering #9 full 30-decision proposal** (20 ENG-R + 10 F toolchain choices) is collected under open draft [PR #103](https://github.com/yyogas/wcp360/pull/103), with 0/30 owner dispositions approved. The full original v0.2 is still unrecovered and proposed v0.3 is NOT ratified. Source and dependency content are more reviewable but do not count as the eighth owner artifact.
- **Database #6** still lacks original complete v1.0 physical source or a fully owner-ratified complete versioned reconstruction; existing 36 fragment inventory and table decisions do not count as one source-ready DB artifact.
- **Security/API #91** still has 12 parent/provisioning/OR origin-operation bindings without final typed owner approval.
- **No independent E3 reviewers** were registered for open R1 candidates #42/#86/#87/#88/#94 when inspected. This is a separate repository/Engineering review dependency, not a permission to self-approve.

```text
REQUIRED FINAL OWNER ARTIFACTS = 8
MATERIALIZED + APPROVED IN MAIN = 5
OWNER-FINAL DATABASE / TESTS-QA / ENGINEERING = PENDING
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Only domain-owner ratification with exact versioned FINAL artifacts, independent reviews and MASTER's explicit SC-M01 go/no-go can change this status.
