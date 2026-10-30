# WCP360 — Engineering / Code Review — Canonical Implementation Export

**Ruleset:** ENG-WCP-RULESET-0001  
**Historical reference:** v0.2 (metadata confirmed; exact final body unavailable)  
**Current owner decision:** v0.3 — reconstructed and substantively amended, 2026-10-28  
**Status:** ENGINEERING OWNER APPROVED — CROSS-DOMAIN AND INDEPENDENT IMPLEMENTATION REVIEWS REMAIN REQUIRED  
**Scope:** SC-M01 first implementation foundation only; not implementation authorization.  
**Baselines:** DOC v4.0.21.23; DESIGN V2.5.3.36.4; CODE NONE. Cancelled Sprint 0 and v4.1 excluded.

## Provenance and version integrity

This file is **not** a byte-identical republication of the historical v0.2 final export. Its exact body was not recovered. Sources: the Phase E/G/H3-C Engineering decisions retained in the owner conversation; `governance/recovery/SC-M01-RECOVERED-ENGINEERING-V0.2-EVIDENCE.md`; `governance/handoffs/SC-M01-ENGINEERING-OWNER-REPUBLICATION-CHECKLIST.md`; `governance/handoffs/SC-M01-ENGINEERING-CANONICAL-INPUT-PACK.md`; `governance/REVIEW-MERGE-POLICY.md`; `governance/SC-M01-SCOPE.md`. The source evidence is historical/non-normative; this document is a new Engineering owner decision. **CONTENT CHANGE = REAL DELTA; VERSION IMPACT = MINOR INCREMENT v0.2 → v0.3** because previously unspecified normative review rules are added. The filename retains v0.2 solely as the required #9 export locator; consumers must use the version declared inside this file, not infer it from its filename. MASTER must explicitly accept this mismatch before counting the Engineering artifact in its baseline manifest.

## Authority and precedence

MASTER baseline membership and ratified specialized owner contracts outrank Engineering preferences. Security owns authorization, identities and privileged confinement; API/IPC owns transport and operation semantics; Database owns physical data model; DevOps owns platform and release mechanisms; QA owns test catalogue and execution evidence; Observability owns telemetry taxonomy; Performance owns measured targets. Engineering controls implementation quality and rejects violations; it does not redefine those contracts. Missing or contradictory authority blocks only the affected component, with HANDOFF or CONFLICT. A historical candidate or PR proposal is not implementation authority.

## ENG-R01 — Repository and layer architecture — OWNER DECISION

Use explicit independently testable component boundaries for WCP-USER, WCP-ADMIN, backend, wcpd, wcp-privd, workers and shared libraries. Within an application component, allowed conceptual direction is transport/UI → application orchestration → domain policy/ports; infrastructure adapters implement ports, never dictate domain policy. Composition roots wire dependencies. Cross-process communication uses approved typed contracts; no direct imports or calls that bypass IPC trust boundaries. Exact directory paths are DevOps/Source Code design choices subject to Engineering review, not guessed canonical paths. Dependency cycles across modules/layers are forbidden. Separate control-plane persistence from hosted-engine and tenant contents.

## ENG-R02 — Naming and conventions — OWNER DECISION

Names must express bounded domain concepts; avoid generic Manager/Helper/Utils and ambiguous IDs. Preserve distinct `permission_id`, `action_id`, `job_type`, `event_id`, `audit_event_id`, `ipc_operation_id`, `error_code`, and distinguish persisted `audit_record_id`. Use the language's official idiomatic naming conventions, enforced consistently within each package; do not invent per-language exact case rules before language ratification. Public APIs, serialization fields and database identifiers follow their owning contracts, even when language idioms differ. Renames across public boundaries require owner-approved compatibility policy.

## ENG-R03 — Error handling — OWNER DECISION

No swallowed exceptions, broad success fallback or sensitive stack trace disclosure. Validate at boundaries, map internal failures to approved typed error codes, preserve cause and correlation internally, and redact external responses. Unknown authorization or operation mappings fail closed. Distinguish FAILED, PARTIALLY_SUCCEEDED, UNKNOWN_OUTCOME, TIMED_OUT, CANCELLED, COMPENSATION_REQUIRED and RECONCILIATION_REQUIRED where applicable. Timeout is not proof of non-execution; retry is not reconciliation; rollback is not compensation; execution success is not verified success. Do not blindly retry destructive effects after lost acknowledgement.

## ENG-R04 — Logging — OWNER DECISION

Use structured timestamped records with severity, component, operation, outcome and correlation/request/job IDs where available and appropriate. Follow Observability taxonomy for domain, operational, security, job and audit events; log is not durable audit. Never log credentials, full tokens, private keys, session cookies, sensitive tenant contents or raw privileged payloads. Keep high-cardinality tenant/resource/request/correlation/job identifiers out of metric labels by default; use traces/logs with retention and redaction instead. Required schema details belong to Observability.

## ENG-R05 — Configuration and secrets — OWNER DECISION

Typed, schema-validated configuration loaded through a single component-owned boundary, with explicit precedence of packaged defaults → approved site configuration → approved environment overrides (where allowed by DevOps); secrets are references resolved by approved runtime mechanisms, never defaults or plaintext committed values. Reject unknown security-sensitive keys, invalid types and missing mandatory settings at startup. Separate immutable `/usr/lib/wcp360`, authoritative control-plane PostgreSQL, reconstructable `/var/lib/wcp360`, ephemeral `/run/wcp360`, reconstructable `/var/cache/wcp360`, and observed physical state. Runtime must not mutate package-managed assets. Exact configuration paths, file ownership and secret provider remain DevOps/Security decisions.

## ENG-R06 — Language/runtime/framework/build toolchain — CROSS-DOMAIN BLOCKED FOR SPECIFIC IMPLEMENTATIONS

Debian GNU/Linux 13 trixie amd64 is the initial supported target. **No exact post-Sprint-0 PHP, Go, Node, framework, compiler, database client, migration runner or package manager version is established by recovered Engineering v0.2.** Do not reuse cancelled Sprint 0 defaults. Engineering requires DevOps to ratify a toolchain matrix with exact version, source, support lifetime, compatibility and build reproducibility before a component requiring it is coded. Engineering will review compatibility and reject floating major versions. This unresolved selection is not falsely declared complete.

## ENG-R07 — Formatters, linters and static analysis — OWNER POLICY; TOOL CHOICE PENDING

Each adopted language must have a deterministic formatter, lint/static analysis, dependency/security scanning and documented invocation; CI must run them against the pinned toolchain. Fail on formatter drift, type errors where typed analysis exists, and high-confidence security findings; exceptions require documented risk ownership. Exact products and versions require the ratified language/toolchain and DevOps CI decision. No invented historical tool choice.

## ENG-R08 — Complexity and duplication — OWNER DECISION

Reject oversized multi-responsibility functions, hidden effects, deeply nested branching and duplicated security/authorization logic. Every newly introduced complex branch must be testable and justified. No universal numeric cyclomatic, cognitive-complexity or duplication percentage is canonical; record baseline measurements after tool selection, then propose numeric thresholds through Engineering/QA review. Until then reviewer judgement with documented findings is mandatory, not a waived gate.

## ENG-R09 — Tests and coverage — OWNER DECISION

Critical security, tenant, privileged, data integrity and recovery behaviors require positive and negative tests traceable to their requirement. Every bug fix requires regression coverage. Unit, API/IPC contract, DB integration, adversarial security, concurrency and recovery suites apply by component. No invented global coverage percentage; uncovered critical branches are blockers regardless of aggregate coverage. Implementation-dependent results remain PLANNED/READY/BLOCKED until executed against exact code SHA; no fabricated PASS/FAIL.

## ENG-R10 — CI required checks — OWNER DECISION

Required semantic check classes for affected PRs: provenance/baseline validation; formatting/lint/static checks; build; relevant unit/contract/integration/security tests; secret and dependency checks; publication/inventory checks; packaging and rollback checks when applicable. Names of GitHub required status checks are established only after jobs exist and pass on main, then enforced by DevOps. Local hooks are bypassable and do not replace server-side checks. PR #42 E1/E2 does not establish this contract or independent E3.

## ENG-R11 — Review and merge — OWNER DECISION

Follow `governance/REVIEW-MERGE-POLICY.md`: E0 authoring, E1 automated verification, E2 same-author verification, E3 genuinely independent technical review. R1 repository tooling requires E1+E3; I1 implementation E1+E3; S1 additionally Security; L1 relevant DevOps/QA/Security. C1 owner contract requires explicit owner approval/provenance/E2 and cross-domain review as applicable. Record reviewer identity, exact SHA, scope, findings, reservations and decision. No fictional reviewer; no hard-coded review count without enforceable independent reviewer availability. No merge with unresolved critical/blocker findings; exceptions must be formally approved by the owning authority and never called E3.

## ENG-R12 — Definition of Done — OWNER DECISION

A change is DONE only when scope and requirement/module IDs are traceable; active owner contracts and baseline membership verified; architecture/security/data boundaries preserved; error/failure and audit/observability behavior specified; applicable tests implemented and actually executed against the exact candidate SHA; negative and regression tests included; lint/build and relevant CI checks pass; dependencies/secrets reviewed; migrations and recovery evaluated where relevant; docs updated; required independent review and specialized approvals recorded; no unresolved blocking finding; and MASTER's milestone gate accepts the result. An authored file or green CI alone is not DONE.

## ENG-R13 — Generated code and artifacts — OWNER DECISION

Generated outputs must identify their source, generator version, deterministic command and whether tracked. Source templates/contracts remain authoritative. Do not hand-edit generated outputs without updating the source and regeneration process. Review generated diffs for unexpected behavior and secrets; do not commit machine-local caches, credentials or nondeterministic build outputs as source. Packaging exceptions belong to DevOps.

## ENG-R14 — Dependencies — OWNER DECISION

Dependencies flow through approved interfaces and never invert trust boundaries. No UI → privileged broker, worker → privileged broker, cross-tenant direct data access or domain → transport/driver coupling. Every significant third-party addition records purpose, alternative, license, maintenance, supply-chain exposure, version and rollback. Pin direct and transitive dependencies through approved manifests/locks, verify integrity where supported, and document updates. DevOps/Security own repository/source allowlists, SBOM and vulnerability acceptance mechanisms.

## ENG-R15 — Concurrency — OWNER DECISION

Identify shared mutable state, transaction boundary, lock ownership/order, cancellation, timeout, retry, idempotency, stale-worker/fencing risk and recovery behavior. No unmanaged fire-and-forget tasks, infinite retries or destructive double-dispatch. Test races, concurrent requests, lost acknowledgements, crash/restart and TOCTOU on sensitive operations. PostgreSQL is logical control-plane authority; observed system state requires reconciliation, not optimistic success claims.

## ENG-R16 — Secure coding — OWNER DECISION

Review authentication and explicit fail-closed authorization across role, permission, entitlement, ownership, delegation, tenant and policy context as applicable. Separate WCP-USER/WCP-ADMIN identities, sessions and authorization. Enforce backend → wcpd(non-root) → authenticated bounded IPC → wcp-privd(minimal root) → explicit primitive; never direct PHP/Web privileged broker access. Forbid shell.exec, arbitrary command/executable/signal/path, generic sudo/systemctl proxy and equivalent capability. Check injection, traversal, SSRF, CSRF, XSS, IDOR, confused deputy, secrets, IPC peers, filesystem confinement and TOCTOU. Security owner approves sensitive contracts; Engineering verifies implementation.

## ENG-R17 — Performance — OWNER DECISION

Require bounded work, resource cleanup, cancellation, avoidance of N+1 queries, unbounded memory/queues and high-cardinality metric labels. Performance regressions need evidence and measured owner-defined targets, not invented SLOs. Do not trade integrity, authorization or audit durability for throughput. Performance owner approves capacity benchmarks.

## ENG-R18 — Release quality — OWNER DECISION

Before release, verify deterministic build provenance, supported Debian 13 amd64 target, dependency inventory, package contents/permissions, secret-free artifacts, migration compatibility, backup/restore and upgrade/rollback/recovery evidence where applicable, vulnerability decisions and release notes. DevOps owns installer/systemd/signing/deployment decisions; Engineering reviews code/package consistency. No release or code baseline merely because a document is published.

## ENG-R19 — Rule precedence and exceptions — OWNER DECISION

Applicable MASTER baseline manifest and approved specialized contracts govern semantics; this ruleset governs implementation quality; approved QA/DevOps policies govern test execution and release mechanisms. Stricter security requirements prevail within Security authority. Contradictions become WCP360 CONFLICT; absent contracts become WCP360 HANDOFF and component BLOCKED. Exceptions require written rationale, owner approval, scope, expiry/review point and tests; never silently waive authorization or privileged boundary.

## ENG-R20 — SC-M01 consumption — OWNER DECISION

This ruleset is documentation, not SC-M01 resume. The eight owner artifacts must be accepted by MASTER, and SOURCE CODE may begin only after explicit resume. Limit implementation to `governance/SC-M01-SCOPE.md`: bootstrap, separated surface entry points, backend/daemon boundaries, approved IPC adapters, DB migration framework with approved physical schema, config/secrets, test harness and deterministic build foundation. No broad functional modules, no SC-M02, no cancelled Sprint 0/V4.1, no invented DB/IPC contracts. First CODE-BASELINE requires later tests, E3 review, specialized sign-offs and MASTER verification.

## Required cross-domain handoffs / unresolved choices

- **DevOps + Architecture + Security:** exact PHP/Go/Node/framework/compiler and package-manager versions, build commands, runtime and build isolation; F01–F10 toolchain decisions.
- **DevOps + QA + Engineering:** exact formatter/linter/static analyzer versions, stable CI job names and branch protection activation; quality measurement baseline.
- **QA + Engineering:** justified numerical coverage/complexity/duplication policies if adopted; test-ID mapping.
- **Security + API/IPC + DevOps:** any concrete IPC/privileged operation details not already ratified.
- **Observability:** precise structured log schema, audit durability and telemetry retention.
- **Performance:** evidence-backed capacity and latency limits.

These dependencies block affected component implementation or enforcement configuration when necessary; they do not grant Engineering authority to invent another owner's contract.

## Owner decision and publication gate

**ENGINEERING OWNER APPROVAL:** YES — for this reconstructed v0.3 normative Engineering content.  
**HISTORICAL v0.2 EXACT REPUBLICATION:** NO.  
**VERSION IMPACT:** v0.2 → v0.3 (minor, additive normative rules and unresolved selections made explicit).  
**HISTORICAL CONTENT INCLUDED:** NO as authoritative text; provenance references are disclosed.  
**READY FOR SOURCE CODE CONSUMPTION:** YES, subject to MASTER acceptance of v0.3 and component-specific owner contracts.  
**BLOCKER:** MASTER must accept v0.3 as successor to v0.2 in the baseline manifest; toolchain selections remain component-level dependencies.  
**INDEPENDENT E3 REVIEW:** NOT PERFORMED; required separately for applicable R1/I1/S1/L1 changes.  
**PRODUCT TESTS:** NONE EXECUTED; no PASS/FAIL claims.  
**CODE-BASELINE:** NONE. **SC-M01:** PAUSED. **SC-M02:** NOT AUTHORIZED.
