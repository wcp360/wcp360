# WCP360 — SC-M01 First Code: Dependency-Ordered Delivery Plan

## Current MASTER first-slice decision — 2026-10-28

Reviewed main: f07c34a2ffc0feab9dc4f6a6de2b5999ce4a0543.
Global owner artifact gate: 8/8 ACCEPTED (manifest decision ed8eddac004c453c31ab840cc655b64728fc83ee). DB/QA/Engineering publication issues are resolved. The historical 5/8 sections below are superseded for current planning.

### Selected first implementation scope

Select a narrow M01-01 repository/build/unit-test foundation for the daemon component, after M01-00 pins its accepted inputs. Proposed first PR contents: owner-approved module layout, exact toolchain/dependency metadata, reproducible build and test entry points, a minimal inert executable and unit-test harness. Runtime must expose no listener, positive control dispatch, privileged operation, database connection or external mutation. A no-third-party-runtime-dependency profile is proposed to reduce initial supply-chain scope; DevOps/Engineering must explicitly ratify it.

This selects scope only, not a language/version or implementation authorization. Web/F01, database client/migrations/F03 and frontend/F05 are not activated by this slice. A2 control-wire, A3 durable jobs, A4 authenticated sessions, A5 endpoints, A6 business operations and A7 release/install decisions are deferred until their components are selected. They are not generic blockers for this inert foundation.

### Required owner decisions before code

| Decision | Exact deliverable | Owner | Status |
|---|---|---|---|
| F02 | Implementation language + exact compiler/runtime version; verified source/digest, support/patch policy; Debian 13 amd64 compatibility | DevOps, Engineering; Security for provenance | BLOCKED — no final exact choice |
| F04 | Allowed dependency sources, integrity and lock strategy; explicit adoption/rejection of the proposed no-third-party profile; network/cache rules | DevOps + Security + Engineering | BLOCKED — no activated final policy |
| F06 | Approved module/package roots and copyable clean-checkout build/unit-test commands with output paths | Engineering + DevOps + QA | BLOCKED — no canonical command set |
| F07 | Exact formatter/lint/static/security tools and versions, invocation/failure policy; review-based complexity policy may remain as ENG v0.3 declares | Engineering + QA | BLOCKED — tool products/versions pending |
| F08 | Exact immutable Debian 13 amd64 build/test environment, non-root build identity, network/secret/token/cache boundaries and reproducibility evidence | DevOps + Security | BLOCKED — no pinned environment |

Source evidence: Engineering canonical blob a2d5035454d90f0a252da7041b3a6b239265c557 explicitly leaves ENG-R06 toolchain and ENG-R07 products/versions unresolved. DevOps blob 61edf698c50cf58779b75ba02fba913bf8276c61 fixes the platform/identities but does not select these tools. The main-tree inventory contains no additional final toolchain artifact. Existing F01–F10 proposal blob 4a4a3891303821d64de153f6210e7e7303a9eb9f is review input only; its dated version suggestions must be revalidated by owners, not adopted implicitly.

### Acceptance checks for the first PR

These are proposed verification checks to map to QA's approved foundation families; no new canonical TEST IDs or PASS results are assigned.

1. Clean Debian 13 amd64 checkout builds with only declared pinned inputs.
2. Wrong compiler version or modified dependency-integrity input fails visibly.
3. Formatting/static checks and actual unit tests run using the recorded commands and exact candidate SHA.
4. The executable runs without root, opens no socket, performs no privileged command and mutates no control-plane/tenant state.
5. Evidence records environment/tool versions, selection, attempts, results and artifact digest without secrets; no fictitious API/IPC end-to-end PASS.

Development start and integration are separate gates. Once the five decisions are owner-final and QA's applicable assertions are READY, MASTER can issue an exact-SHA limited resume. The implementation PR then needs E1 and genuine E3 before merge under current policy; no missing E3 is relabeled as approval. Security/DevOps reviews apply to the actual boundary/CI changes. Production packaging, release and SC-M02 are outside this decision.

### Owner completion packet

DevOps should publish one final toolchain addendum under specifications/devops/ with Engineering concurrence, exact F02/F04/F06/F07/F08 decisions, Security provenance/environment checks and QA command/evidence mapping. Return owner identities, version, FINAL APPROVED CONTENT, source-consumption status, content/version impact, actual blockers, canonical path, commit and blob. F01/F03/F05/F09/F10 must be explicitly deferred unless the chosen implementation activates them. Existing canonical Engineering v0.3 remains accepted; do not reopen its publication merely because this component toolchain addendum is outstanding.

### Decision

SC-M01 FIRST EXECUTABLE SLICE = NOT YET AUTHORIZED — A1 TOOLCHAIN DECISIONS REQUIRED.
SC-M01 GLOBAL ARTIFACTS = 8/8 ACCEPTED.
SC-M02 = NOT AUTHORIZED.
CODE-BASELINE = NONE.
PRODUCT TESTS EXECUTED IN THIS READINESS CHECK = 0.

This G1 scope/readiness decision is E0+E2 and closes no independent audit finding.

---

## Historical planning record — 2026-10-26

The following original snapshot is retained for provenance; the current decision above governs current readiness.

**Date:** 2026-10-26  
**Status:** MASTER COORDINATION CANDIDATE / NON-CANONICAL / NO IMPLEMENTATION AUTHORIZATION  
**Reference:** governance/SC-M01-SCOPE.md, WCP360-BM-0001 FINAL, approved API/IPC/Security/DevOps v1.0 contracts, ENG-WCP-RULESET-0001 owner artifact pending.

## 1 — Operating principle

This plan is ready for use **after** the actual SC-M01 resume gate. It is not a new Sprint 0 and cannot override WCP360's owner authority.

There are **three distinct gates**:

1. **Global artifact gate:** 8/8 owner-final artifacts physically committed and SHA/provenance verified. Current state: 5/8, DB #6, QA #7 and Engineering #9 still missing.
2. **Component semantic gate:** each specific coding slice must have explicit owner-approved API/IPC/Security/DB/DevOps decisions required for that slice. The 2026-10-02 owner decision matrix still had 44 OPEN items; unrelated future-module decisions need not universally block a narrow bootstrap.
3. **Review/integration gate:** exact SHA automated checks + E3 independent technical review, and enforced repository merge protections or a formally approved governance alternative. CI E1 does not equal E3.

None of these gates can be satisfied by renaming a draft, by approving our own PR, or by marking unexecuted QA cases PASS.

## 2 — SC-M01 narrow delivery slices

| Slice | Deliverable once authorized | Mandatory prior source / blockers | Review and test evidence |
|---|---|---|---|
| M01-00 | Pin agreed baseline/artifact references in a versioned implementation manifest; define source ownership. | Global 8/8 plus MASTER resume. | Verify all eight owner final paths/versions/SHAs; no historical/cancelled Sprint 0 material. |
| M01-01 | Minimal project/repository bootstrap; reproducible locked dependencies, package/module paths and CI entry points. | Engineering #9 F01/F02/F04–F08; DevOps F03–F10; QA #7; PR #42 guardrails/E3. | Clean checkout build, lockfile tampering failure, static checks, secrets absence. |
| M01-02 | WCP-USER and WCP-ADMIN distinct process/entrypoint/config/session envelopes; no business features. | Security S01–S04, architecture topology approval, Design V2.5.3.36.4. | Wrong cookie/surface/Origin/CSRF tests; no cross-surface privilege or cookie leak. |
| M01-03 | Backend typed context, safe errors/results, correlation IDs, authorization boundary interfaces. | API HTTP H01–H10 for activated operations; Security S05–S09; Engineering; QA. | Negative default-deny, wrong actor/tenant/permission/entitlement and no-effect tests. |
| M01-04 | PostgreSQL connection/config/transaction boundary and **migration framework placement**, no arbitrary schema. | Database canonical #6; DevOps PostgreSQL decision F03; Engineering #9. | DB connect transaction rollback, non-mutation against hosted engines, migration harness tests. |
| M01-05 | Durable intent/idempotency/jobs/outbox adapters, limited to accepted schema and approved state machine. | DB #6 exact uniqueness/locking/indexes, API T05–T07, Security/Observability/QA. | Duplicate key semantic conflict, cross-tenant conflict, audit failure rollback, worker fencing and lost response. |
| M01-06 | wcpd non-root control socket/peer/auth context and strictly typed control message parser. | C01 republication, C03/C06/C07 owner decision, C04 worker path where applicable, Security, DevOps. PR #88 is **R&D only**. | Wrong peer/surface, unknown op, malformed/truncated frames, stolen/revoked context; no privileged fallback. |
| M01-07 | wcp-privd root-minimal **IPC-B validation skeleton** with canonical 47-operation registry metadata; default deny/no real mutation. | IPC-B/1 and Security v1.0 owner artifacts, DevOps socket, Engineering, QA; global gate. | SO_PEERCRED exact UID/service check; hostile JSON, unknown op, wrong target/scope, no generic shell/systemd proxy. |
| M01-08 | Observability envelopes, redaction, health/readiness for the accepted foundation components. | Observability baseline v1.0, Security secrets policy, SRE owner accepted component fields. | Correlation/audit/job IDs, health degraded cases, no password/token/private-key leakage. |
| M01-09 | Build/packaging evidence and test/report generation, not automatically production installer/release. | DevOps v1.0 and F01–F10, Engineering, QA; binary/source provenance. | Debian 13 amd64 build; exact artifact digest; no tenant data in packages; reproducible clean build. |
| M01-10 | Independent Engineering review, QA execution and first CODE-BASELINE candidate. | All activated slices approved + applicable TEST-IDs + exact SHA and independent review E3. | Security/QA negative tests, audits, regression checks, branch controls and MASTER acceptance. |

**Every slice currently = NOT IMPLEMENTED / NOT AUTHORIZED under global SC-M01 PAUSED gate.**

## 3 — Candidate work already in GitHub (not code authorization)

| Source | What it prepares | What it cannot do |
|---|---|---|
| PR #83 | Coordinated owner handoffs and gate checklist | Close owner issues or claim review. |
| PR #84 | DB-R01..DB-R15 physical decision proposal | Replace the exact owner-final DB model. |
| PR #85 | ENG-R01..ENG-R20 engineering decision proposal | Replace the owner-final Engineering ruleset. |
| PR #86 | Five-source-fingerprint read-only validator with negative tests | Verify 8/8 owner approval or E3. |
| PR #87 | Machine-derived 86 API / 47 IPC / 82 permission → TEST references and drift tests | Execute WCP360 product tests or close QA #7. |
| PR #88 | C03/C06/C07 bounded control-protocol options + inert negative parser R&D | Become the official wcpd implementation or control wire contract. |
| PR #89 | F01–F10 Debian 13 toolchain options with upstream EOL/lifecycle evidence | Select the official PHP/Go/Node/DB versions without Engineering/DevOps owner approval. |

All these are separate, reviewable contributions; **do not merge them as a shortcut to the missing owner decisions**.

## 4 — Highest-leverage decision order

A. **Database owner #6:** either exact republication of retained v1.0 physical contract or explicit owner-amended complete contract, with new version/content impact. Close DB-R01..15 with precise fields, constraints, indexes and state transactions.

B. **Engineering owner #9:** either exact v0.2 republication or transparent versioned amendment. Approve F01/F02 and static/CI/runtime rules; check PR #89 for supported Debian 13 versions rather than inheriting cancelled Sprint 0.

C. **TESTS / QA owner #7:** accept/amend A01..A20 and publish complete test baseline; use machine-checked reference coverage PR #87 as an input, but define expected outcomes and fixture/evidence semantics.

D. **API/IPC + Security + DevOps:** ratify C01/C03/C06/C07 control wire, S01–S09 and H01–H10 only for the foundation portions being activated. Do not infer C06 from IPC-B. PR #88 is a comparison/prototype, not an approved protocol.

E. **Audit + MASTER:** independently verify all eight artifacts, final owner decisions required for M01-00/M01-01 and repo protections; record explicit resume authorization. Only then start executable M01-01.

## 5 — Non-negotiable red lines

- No `shell.exec`, arbitrary systemd/sudo/argv/command/file writes or privileged proxy.
- No direct backend/worker→wcp-privd.
- No collapse of WCP-USER/WCP-ADMIN auth/session/privilege boundaries.
- No collapsing account_id, organization_id and tenant_id.
- No tables, migrations, FKs or idempotency persistence structures invented by SOURCE CODE.
- No automatic import of cancelled Sprint 0, V4.1 or outdated /usr/local/wcp360 root.
- No guessed Go/PHP/Node versions based solely on Debian packages without security support decision.
- No fake TEST PASS/FAIL or same-author E3 approval.
- No marking GO based on the percentage of GitHub documents or CI checks.

## 6 — Reporting format per implementation slice

    SLICE ID =
    OWNER =
    CANONICAL INPUTS (exact SHA) =
    GLOBAL GATE VERIFIED =
    SEMANTIC DECISIONS / RELATED OWNERS =
    IMPLEMENTATION PR (SHA) =
    REQUIRED TEST IDS =
    ACTUAL TEST ENVIRONMENT / RESULTS =
    SECURITY REVIEW =
    ENGINEERING E3 REVIEW =
    READY / BLOCKED / ACCEPTED =
    MASTER DECISION =

## 7 — Current conclusion

    REQUIRED ARTIFACTS = 8
    SOURCE-READY IN MAIN = 5
    DB/QA/ENG FINAL FILES = PENDING
    SC-M01 = PAUSED — TARGETED EVIDENCE
    SC-M02 = NOT AUTHORIZED
    CODE-BASELINE = NONE

This plan can guide independent implementation PRs after the gate; it is not itself the gate.
