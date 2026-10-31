# WCP360 — Engineering Ruleset v0.3 proposed full owner-review body

**Date:** 2026-10-27  
**STATUS:** **COMPLETE REVIEW-CANDIDATE, NOT FINAL OWNER APPROVAL / NON-CANONICAL P1**  
**Candidate logical identity:** ENG-WCP-RULESET-0001 v0.3 ONLY IF ENGINEERING OWNER RATIFIES AN AMENDMENT  
**Historical existing version:** ENG-WCP-RULESET-0001 v0.2 FINAL *metadata only*; original complete body not retrievable.
**Required owner:** WCP360 — ENGINEERING / CODE REVIEW. Related: DevOps, Security, QA, Audit/Architecture Review, MASTER.
**Baseline reference:** WCP360-BM-0001 FINAL; DOC v4.0.21.23; DESIGN V2.5.3.36.4; CODE NONE.
**Source/provenance:** 
- All 20 ENG-R candidates copied from `governance/proposals/SC-M01-ENGINEERING-RULESET-OWNER-DECISION-DRAFT-2026-10-26.md`, Git blob `0814cd708d834229f96978fc4dff5909a6a1949d`;
- F01..F10 technology candidates + release/DoD sections copied from `governance/proposals/SC-M01-ENGINEERING-F01-F10-CANDIDATE-DECISION-v0.3.md`, Git blob `4a4a3891303821d64de153f6210e7e7303a9eb9f`;
- Canonical Security v1.0, API 1.0, IPC-B/1 and DevOps v1.0 remain higher-authority sources than either proposal.

## A. Publication decision: retained exact v0.2 or new owner-amended v0.3

**A — historical exact republication:** only if ENGINEERING produces a retained complete original v0.2 with independently verifiable provenance and identical body. Then `CONTENT CHANGE = NONE` and use that original body. This candidate does **not** constitute it.

**B — explicit owner amendment (recommended for review):** ENGINEERING accepts/amends these 30 candidate rules into a complete normative replacement, declares `CONTENT CHANGE = YES`, exact real delta and version impact, and requests MASTER to ratify a new version (v0.3 is a proposal, not an assigned approved baseline). Without that decision this document remains noncanonical.

Neither choice may silently resurrect cancelled Sprint 0 or V4.1, or invent previously approved numeric quality thresholds.

## B. Accepted constraints from other owners (not newly set by Engineering)

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

## C. ENG-R01..ENG-R20 — candidate decision-by-decision rules

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

## D — Concrete candidate decisions for F01–F10

Every row is **PROPOSED FOR OWNER ACCEPTANCE**; none is FINAL.

| ID | Recommended owner decision | Required independent/owner checks | Outcome if rejected |
|---|---|---|---|
| **F01** | PHP 8.4 / Laravel 13 application services behind separate `wcp360-user` and `wcp360-admin` PHP-FPM identity/session boundaries. Shared domain code only without a shared authorization surface. | Security S01–S04 topology/cookie/CSRF/Origin; Architecture Review; Design compatibility; selected actual Laravel patch+Composer lock. | Owner chooses and versions another framework; no silent manual auth implementation. |
| **F02** | Non-root `wcpd` + root-minimal `wcp-privd` built using Go 1.27 maintained patch line, with tested Go 1.27.3 initial *candidate* and pinned verified upstream/toolchain digest. The R&D Go 1.24.13 runner is NOT acceptance evidence for production. | DevOps provenance/signature/reproducibility; Security compiler/crypto maintenance; test on Debian 13 amd64; Go race/negative handling. Compare distro-maintained Go vs upstream signed package and document risk acceptance. | Explicit owner choice of supported/backported Go with proof and version delta. |
| **F03** | PostgreSQL 17 **control plane** (Debian13 native) with separately managed credentials, roles and backup/restore; hosted tenant engine managed resources remain distinct. | Database owner #6 physical model, FKs, transaction/locking/migrations; DevOps upgrade/rollback and supported driver. | Database owner selects supported major and records compatibility. |
| **F04** | Official Debian trusted signed APT channels, approved Composer/Packagist mirror and pinned checksummed Go module supply; immutable `composer.lock` / `go.sum`; no unreviewed release-time downloads/installer scripts. | Security dependency ownership, provenance, license scan, CVE policy, offline/reproducible build and mirroring. | Owner supplies equally auditable, signed registry strategy. |
| **F05** | No Node.js process in WCP360 production/runtime by default. If build-time frontend assets are needed by Design V2.5.3.36.4, use a currently supported Node 24 LTS pinned CI builder with dependency lock, publish static assets only. | Design owner decides bundler/asset compilation; DevOps audits Node version/provenance/lock/cached clean build. | Explicit no-Node static pipeline or other supported build-only tool. |
| **F06** | Candidate build commands, subject to owner toolchain choice: PHP `composer validate --strict` and reproducible `composer install`; Go `go test ./...`, `go vet ./...`, `go test -race ./...` for applicable concurrency; QA SC-M01 suites by accepted IDs. No auto-fetch of unapproved dependencies. | Exact `composer.json`, pinned dependencies and CI package roots first; full build commands must be recorded once executable code is authorized. | Owner chooses equivalent reproducible entry points. |
| **F07** | Candidate quality check families: PSR-12 formatter, PHPStan/Larastan compatible with adopted framework, typed PHP 8.4 static analysis, Go `gofmt`, `go vet`, vetted pinned static scanner, reference/contract drift, secret scan and forbidden privileged-op tests. | Engineering chooses **exact** tools/versions/check names, lint/rule thresholds, false-positive waiver policy and DoD; QA owns negative assertions. | No arbitrary threshold/tool inherited from Sprint 0. |
| **F08** | Pinned, ephemeral **Debian 13 amd64** build and test environment with non-root CI steps, approved network/mirror limits, protected promotion/signing stage isolated from untrusted PRs. | DevOps validates runner image digest, CPU/race coverage, cache independence and reproducibility; Security reviews PR token permissions/secrets. | Document supported equivalent target with compatibility proof. |
| **F09** | Typed, startup-validated config with clear precedence, per-consumer secret access, immutable `/usr/lib/wcp360` product tree, approved `/etc/wcp360` secure config and `/var/lib/wcp360` operational state, no runtime config-to-shell interpolation. | DevOps/Security ratify config format/precedence, secret storage/rotation/revocation and generated assets ownership; never log secrets. | Owner specifies alternative with same fail-closed properties. |
| **F10** | Reproducible artifact output with Git SHA, toolchain+dependency lock digest, SHA-256 artifact integrity, SBOM/provenance and signing by isolated release identity. Release exactly the artifact CI verified; keep rollback/data-migration compatibility classified. | DevOps signs off package format/signature/trust root/reproducibility and upgrade/rollback boundary; Security key custody. | Owner supplies alternate fully auditable signature/provenance scheme. |

## E — Mandatory security/architecture separation for first M01-01

The runtime stack may only be chosen if it **preserves**:

```text
WCP-USER ≠ WCP-ADMIN  (OS identity / secrets / cookies / sessions / CSRF / authz)

user/admin backend → wcpd (non-root, control/orchestration)
                 → bounded authenticated IPC-B (wcpd only)
                 → wcp-privd (minimal root)
                 → registered typed privileged primitive

account_id ≠ organization_id ≠ tenant_id
CONTROL-PLANE POSTGRESQL ≠ HOSTED DB ENGINE / CUSTOMER DATA
```

Before implementing real control transports, separate API/IPC/Security/DevOps must settle C01/C03/C06/C07. Before job/idempotency/audit persistence, Database #6 must be a full owner-approved physical model. Before a 12-operation parent-binding dispatcher, Security/API #91 must ratify exact `permission_id / origin_action_id / ipc_operation_id` tuples, not string-prefix, OR or generic runtime fallback.

No `shell.exec`, arbitrary commands/executables, generic `sudo/systemctl`, arbitrary privileged filesystem paths or unrestricted plugin execution.

## F — First implementation feasibility audit (M01-00/M01-01 only)

| Foundation item | Supported enough to implement **after** owner final + global gate? | Missing exact check |
|---|---|---|
| Repository project/bootstrap structure | Yes, if this F01–F10 and ENG-R rules are accepted, and QA #7 is final | Approved source tree, dependency lockfiles, exact test entry points, independent review and MASTER GO |
| Two PHP web entrypoints | Conditional | Security S01–S04 deployment topology, CSRF and cookie/session namespace; Design mapping |
| Go privileged IPC-B parser skeleton | Conditional | F02 version, authenticated socket peer, canonical framing/limits, Security/E3; R&D #94 cannot substitute production code |
| Backend→wcpd control client | **NO** until C03/C06/C07 ratified | Control wire framing/peer/trusted context and durable job ownership |
| PostgreSQL migrations | **NO** until DB #6 final | No executable DDL from reconstructed fragments |
| Root privileged effects | **NO** until full Security/API bindings and tests | #91, exact target/primitives and E3 review |
| Release/installers | **NO** by default as M01-01 | Separate DevOps release/installer authorization and rollback evidence |

## G — Candidate engineering review/DoD semantics

**Propose** the following explicit, enforceable owner gates, without guessing numeric thresholds:

1. Every implementation PR references an accepted requirement/module/contract/security decision and the exact owner-approved version and SHA.
2. Type-check static tools, formatting and unit/contract/security tests use pinned versions and actual CI paths.
3. Every changed privilege boundary has negative tests: wrong surface/tenant/permission/job/operation, forged/stale context, malformed frames and no-effect assertions.
4. No added DB table/column/constraint without Database model owner approval and versioned migration.
5. No source secret or production credential, dynamic privileged commands, false success on `UNKNOWN_OUTCOME`, cross-tenant leakage, or direct backend/worker→root path.
6. Required QA test evidence is tied to exact SHA/environment/attempt history. Quarantined/blocked required tests cannot be silently passed.
7. E1 automated checks + **E3 independent Engineering technical review** for R1/I1/S1 code; Security/DevOps/Database owner reviews as applicable. Same-author E2 does not count as E3.
8. Artifact/version/provenance/compatibility/rollback evidence accompanies approved release scope. Numeric thresholds require explicit Engineering/QA owner decision, not an inferred v0.2 requirement.

## H. Owner disposition register — every item pending

| Decision set | Source status | Owner disposition | Normative publication condition |
|---|---|---|---|
| ENG-R01 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R02 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R03 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R04 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R05 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R06 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R07 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R08 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R09 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R10 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R11 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R12 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R13 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R14 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R15 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R16 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R17 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R18 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R19 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| ENG-R20 | PROPOSED only | **PENDING Engineering ACCEPT / AMEND / REJECT** | Include precise normative wording, source, tests and version impact |
| F01 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F02 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F03 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F04 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F05 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F06 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F07 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F08 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F09 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |
| F10 | PROPOSED option, not final toolchain | **PENDING Engineering + DevOps and impacted owners** | Pin version/digests/tools/run commands only after owner ratification |

## I. Required owner acceptance conditions for first source-code use

- Engineering must issue all 20 ENG-R decisions and all 10 F toolchain decisions **under its own authority**, with exact versions/thresholds/check names where applicable. Phrases like "defer", "TBD" or "owner decides" do not create implementation authority for that feature.
- DevOps ratifies Debian 13/amd64 toolchain/build/packaging provenance, service identities, installed/runtime layouts, backup/release/rollback and required CI checks. Security checks supply-chain secrets, runtimes, system API boundary, separation WCP-USER/WCP-ADMIN, strict privileges.
- QA links actual TEST IDs, expected outcomes and tests to accepted rules; no fake PASS, no green CI claim for unevaluated components.
- Architectural exceptions create named ADR/HANDOFF to MASTER and Audit. Source Code never changes a owner decision to unblock itself.
- Independent E3 review is separate from author self-review/E2 and CI/E1; no code/release merge may treat E1+E2 as E3.
- After final owner ratification, publish a **self-contained COMPLETE FINAL** canonical export under `specifications/engineering/` with exact Git SHA, version, content delta, owner approval and accepted full rule wording. If original v0.2 cannot be recovered, do not publish a newly decided v0.3 body as unmodified v0.2.

## J. Current non-approval record

```text
ENG-R01..ENG-R20 source proposal excerpts = 20/20 PRESENT
F01..F10 proposed decision options = 10/10 PRESENT
ENGINEERING OWNER DISPOSITIONS = 0/30 APPROVED IN THIS ARTIFACT
ACTUAL TOOLCHAIN / CI / NUMERIC THRESHOLDS = NOT RATIFIED
SELF-CONTAINED REVIEW-CANDIDATE = YES
FINAL OWNER AUTHORITY = NO
ISSUE #9 = OPEN

SC-M01 IMPLEMENTATION ARTIFACT GATE = 5/8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

**Do not move this proposal under `specifications/engineering/` or count it in 8/8 without ENGINEERING acceptance, version/provenance validation and cross-domain reviews.**
