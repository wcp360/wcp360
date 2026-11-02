# WCP360 — Engineering Ruleset: Owner Decision Draft (SC-M01)

> Historical integration note (2026-11-02): this dated branch document is retained
> as development-source evidence. Its original decisions, pending states, path
> literals and execution claims describe the recorded snapshot; they do not
> override the current Core implementation or grant owner approval, independent
> E3 review, a CODE-BASELINE, deployment or production qualification. See the
> [tooling integration guide](../maintenance/BRANCH-TOOLING-INTEGRATION-2026-11-02.md)
> for current paths and separate current/historical CI scopes.
**Date:** 2026-10-26  
**Status:** PROPOSED RULES — ENGINEERING OWNER REVIEW REQUIRED — NON-CANONICAL  
**Tracking:** #9, #13, AUDIT-WCP-0102, AUDIT-WCP-0113  
**Historical identity:** ENG-WCP-RULESET-0001 v0.2 (full exact final export not recovered).  
**Candidate version:** not assigned; Engineering and MASTER must decide.  
**CODE-BASELINE:** NONE; SC-M01 PAUSED; SC-M02 NOT AUTHORIZED.

## 1. Authority boundary

This draft is a **new proposed decision set**, NOT a reconstructed, previously approved final v0.2 export and NOT executable implementation authority.

Inputs:
- governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md
- governance/evidence/SC-M01-ENGINEERING-REPUBLICATION-CONFORMANCE.md
- governance/handoffs/SC-M01-ENGINEERING-OWNER-REPUBLICATION-CHECKLIST.md
- governance/SC-M01-SCOPE.md
- governance/REVIEW-MERGE-POLICY.md
- specifications/api/, specifications/ipc/, specifications/security/, specifications/devops/ (existing canonical owner artifacts)
- governance/proposals/SC-M01-TOOLCHAIN-BUILD-DELTA-v0.1.md (proposal, NOT final)

The final Engineering owner may use this to ratify/modify decisions, but must document any real delta and version impact. Cancelling Sprint 0 also cancels assumptions that PHP/Go versions, frontend framework, Node usage or package manager are already approved.

## 2. Accepted constraints from other owners (not newly set by Engineering)

- WCP-USER and WCP-ADMIN remain distinct security surfaces; browser UI visibility is not authorization.
- Backend → wcpd non-root → authenticated bounded IPC-B → wcp-privd minimal root → explicitly allowlisted privileged primitive; no direct backend/root shortcut.
- No arbitrary command, shell.exec, caller-supplied executable, generic systemd/sudo proxy, arbitrary signal or unbounded file write.
- Permission/action/job/event/audit-event/IPC-operation/error-code are typed distinct identifiers even if their strings resemble each other.
- account_id, organization_id, tenant_id are different; control-plane PostgreSQL differs from hosted engines.
- Initial target Debian 13 amd64 and package-managed product root /usr/lib/wcp360; service identities from DevOps v1.0 remain authoritative.
- Any late/uncertain privileged outcome follows UNKNOWN_OUTCOME / reconcile rather than blind redispatch.
- Mandatory durable audit/outbox is part of pure control-plane DB mutation atomicity.
- No CODE-BASELINE until MASTER checks 8/8 SC-M01 owner artifacts and applicable component contracts.
- E0 authored, E1 automated, E2 author verification, E3 independent technical review are distinct; E2 cannot be represented as E3.

## 3. ENG-R01..ENG-R20 — candidate decision-by-decision rules

All 20 rules in this section have **status PROPOSED / NOT OWNER-RATIFIED**. Engineering must return ACCEPT / AMEND / REJECT / BLOCKED for each.

### ENG-R01 — Component and dependency graph

**Propose:** organize logical concerns into distinct surfaces (WCP-USER, WCP-ADMIN), transport adapters, application services, authorization and policy services, domain logic, persistence ports/adapters, job/outbox worker, wcpd IPC client/server, wcp-privd typed primitive dispatcher. Domain/application logic cannot import UI presentation or root primitive implementation. Shared libraries cannot assume WCP-ADMIN privilege merely because both surfaces use them.

**Explicitly defer:** exact folders, module/package names and final deployment graph until selected language/framework and Architecture/Security contracts are approved. Generated project skeleton is NOT authorized now.

**Verify:** static dependency rule prohibits UI→root, backend→wcp-privd and worker→privileged socket direct imports/calls.

### ENG-R02 — Naming / namespaces

**Propose:** one declared naming convention per chosen language and artifact kind. Enforce distinct *types* for permission_id, action_id, job_type, event_id, audit_event_id, ipc_operation_id and error_code. No prefix/wildcard authority matching, implicit aliases or UI-generated permission identifiers. Registration with canonical owner precedes implementation.

**Verify:** negative tests swap two same-looking identifiers and must fail closed.

### ENG-R03 — Typed errors, results and boundaries

**Propose:** recoverable/domain errors use a defined typed result; programming defects remain distinguishable and must not become public detail leaks. Map transport failures only at API/IPC adapter boundaries. Preserve exact canonical errors, correlation IDs and authorization context; forbid catching every exception then returning generic success.

**Invariant:** timeout != definite failure; retry != reconciliation; rollback != compensation; execution success != verified success.

**Verify:** failure-before-dispatch vs after-dispatch uncertain outcome have distinct tests.

### ENG-R04 — Structured logging and redaction

**Propose:** structured logging with timestamp, level, service/component, request/correlation/job IDs when applicable, stable error code, event category and bounded safe context. Logs do not include passwords, secret values, tokens, private keys, sensitive cookies, DB credentials or unredacted IPC payloads. Audit events and operational logs have different authority/retention.

**Explicitly defer:** exact log schema, required field names and sinks to OBSERVABILITY / SECURITY owner approval.

### ENG-R05 — Configuration and secrets

**Propose:** typed, schema-validated, fail-closed config on startup; no secrets in source/test fixtures/build output or CLI arguments. Read-only product root; runtime state under owner-approved locations. Document environment/file/secret-store precedence and reject ambiguous security-sensitive override order.

**Explicitly defer:** exact loader, file format, override precedence and secret-storage mechanism until DevOps/Security ratification.

### ENG-R06 — Language/framework/runtime

**Decision needed (NOT RECOVERED):** explicit backend language/framework, frontend/runtime strategy, wcpd/wcp-privd Go/version or alternative, PostgreSQL major/client and supported toolchain versions. No selection can be imported from cancelled Sprint 0.

**Propose review rubric:** Debian 13 availability, security maintenance, separate USER/ADMIN policies, deterministic dependency locks, stable IPC protocol, packaging, operational support and test coverage. Owner must record the selected choice and rejected alternatives.

### ENG-R07 — Formatters, linters and static analysis

**Propose:** pinned tool versions and lockfiles; deterministic format and lint/type/static checks for each adopted language; CI fails on violations, with documented exceptions linked to an issue and expiry.

**Explicitly defer:** exact program names/versions until ENG-R06/F01–F10 accepted. Do not invent a historical v0.2 check name.

### ENG-R08 — Complexity and duplication

**Propose:** independent review of complexity, tight coupling, duplicated policy implementations and generated-state divergence. Refactor repeated permission checks into a tested policy boundary, not UI code.

**Explicitly defer:** numeric cyclomatic/duplication ceilings until measured/ratified. Lack of a number is not permission to ignore complexity.

### ENG-R09 — Unit/integration/coverage requirements

**Propose:** each critical permission, API/IPC operation, DB transaction/fencing rule and failure branch maps to owner-approved TEST IDs with positive and negative execution. No fake PASS, no blanket coverage percent as substitute for risk-based assertions.

**Explicitly defer:** numeric coverage threshold to QA/Engineering joint decision.

### ENG-R10 — Required CI checks

**Propose:** mandatory classes: reproducible dependency/build, format+lint/static, test selection and evidence report, API/IPC schema compatibility, canonical permission mapping, forbidden privileged operation scan, secret-pattern check, migration dry-run/rollback classification once migrations are authorized, provenance/artefact checks. Merge policy must enforce actual required checks.

**Explicitly defer:** exact workflow names/runner/tools and branch ruleset changes until DevOps/toolchain and GitHub administration controls are approved.

### ENG-R11 — Reviews and approvals

**Recovered current policy:** E0 authoring, E1 automated, E2 same-author verification, E3 independent technical review. Same account/assistant cannot attest E3 by relabeling its own comment.

**Propose:** mandatory exact-SHA independent review for implementation and critical tooling; review must list affected owner contracts and unresolved comments. Where a mono-maintainer exception is desired, that is a separate explicit governance decision, not implicit self-approval.

### ENG-R12 — Definition of Done

**Propose:** linked requirement/contract IDs; correct surface/role/tenant boundaries; negative auth/security tests; deterministic error/idempotency behavior; typed observability; no secret leaks; passing actual applicable CI; migrations approved when relevant; docs/changelog updated; E3 review and accepted release gates. Missing component-specific canonical contract means NOT DONE.

### ENG-R13 — Generated code and artifacts

**Propose:** generated sources have declared generator authority and are reproducible from tracked inputs; no editing generated output as a substitute for updating source; generated artifacts retain source version, reproducibility command and change review.

**Explicitly defer:** toolchain-specific generated code paths.

### ENG-R14 — External dependencies

**Propose:** dependency addition requires purpose, owner, version/lock, license/security review, support horizon, build provenance and update strategy. Disallow network-time unpinned downloads, unreviewed install-time scripts and dependency changes without diff/lock review.

### ENG-R15 — Concurrency and state ownership

**Propose:** no in-memory-only idempotency/audit/lease authority for durable effects; revision and fencing are enforced by DB contract; stale workers and late replies cannot win over newer state; Unknown Outcome requires reconciliation not blind retry. Prefer explicit cancellation/timeouts and race tests.

**Explicitly defer:** precise DB claim algorithm to Database/API owner, not Engineering.

### ENG-R16 — Secure coding / privileged execution

**Propose:** mandatory threat-review of privileged operations, peer authentication, schema validation, path and resource identity resolution, tenant checks and TOCTOU windows. wcp-privd only dispatches exactly allowlisted operations. Disallow generic shell/code execution and wildcard capability mapping.

**Verify:** wrong peer, wrong tenant/surface, wrong action, malformed frame, forged identity and forbidden op produce NO privileged effect with audit/security evidence.

### ENG-R17 — Performance-aware review

**Propose:** profile or benchmark before consequential optimization; review N+1 queries, unbounded pagination, queue saturation, I/O blocking and resource caps. No optimization bypasses authorization, atomicity, audit or isolation. Capacity targets remain Performance-owner decisions.

### ENG-R18 — Build/release quality

**Propose:** versioned/reproducible artifacts, checksums/provenance, documented rollback class, test scope and exact SHA. Installer/upgrade must not claim safe rollback after irreversible migration. Package-managed application content stays distinct from tenant data and runtime state.

**Defer:** concrete packaging/build commands and signing provider to DevOps owner.

### ENG-R19 — Authority and precedence

**Propose:** canonical owner artifacts and MASTER baseline manifest override proposals and historical recovery evidence for implementation. Ambiguity or missing contract is a HANDOFF, not a design decision embedded in a PR. Every exception records owner, rationale, tests and version impact.

### ENG-R20 — SC-M01 consumption and CODE-BASELINE

**Propose:** consume only 8/8 materialized, owner-final, SHA-identified and cross-checked canonical artifacts; then inspect semantic decision readiness for the precise foundation component, not unrelated future modules. First code must be on a reviewable branch with tests/CI and independent Engineering review. CODE-BASELINE stays NONE until an independently approved implementation package is accepted by MASTER.

## 4. Mandatory owner responses for F01–F10 toolchain choices

| F-ID | Concrete decision to ratify | Current state |
|---|---|---|
| F01 | Backend language/framework/runtime versions and support | UNDECIDED |
| F02 | wcpd and wcp-privd implementation toolchain/version | UNDECIDED |
| F03 | PostgreSQL major/client and migration tool | UNDECIDED |
| F04 | Trusted dependency sources / lock policy | UNDECIDED |
| F05 | Frontend build/runtime and Node/no-Node decision | UNDECIDED |
| F06 | Build/lint/test entry points | UNDECIDED |
| F07 | Concrete static analysis / CI check names | UNDECIDED |
| F08 | Build image, runner and hermeticity policy | UNDECIDED |
| F09 | Config/example/generated files authority | UNDECIDED |
| F10 | Build artifact identity, digest/SBOM/provenance/signature policy | UNDECIDED |

These are decision requests, not approved defaults. Engineering owns Engineering criteria; DevOps owns build/packaging; Security owns trust/secret-policy; QA owns test assertions; Architecture Review resolves cross-domain conflicts.

## 5. Owner approval response template

Engineering must provide, for ENG-R01..R20:
- ACCEPT / AMEND / REJECT / BLOCKED;
- final normative wording;
- rejected alternatives and rationale where consequential;
- required QA test IDs / negative cases;
- owner HANDOFFs to DevOps, Security, API/IPC, Database and MASTER;
- exact final source provenance/version/digest.

Choose an honest publication mode:

**EXACT REPUBLICATION:** prior retained exact final v0.2 body exists; publish unchanged with evidence and CONTENT CHANGE = NONE.

**OWNER AMENDMENT/RECONSTRUCTION:** original final body not available; newly ratified content; declare CONTENT CHANGE = YES, new version impact and required MASTER ratification. Do NOT call it byte-faithful historical v0.2.

Only the owner-approved complete export under specifications/engineering/ counts toward #9. This proposed draft does not.

## Gate

#9 remains OPEN. #13 stays 5/8; SC-M01 PAUSED; SC-M02 NOT AUTHORIZED; CODE-BASELINE NONE.
