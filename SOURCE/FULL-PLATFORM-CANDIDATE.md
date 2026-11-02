# WCP360 — full platform implementation candidate

This record describes the historical broad implementation workbench requested by the project owner. The source is now retained as an isolated candidate under SOURCE; the active application is [WCP360_Core](../WCP360_Core/README.md). See the [reconciliation and provenance guide](README.md). Retention does not activate these ports, promote a production baseline or change licensing.

## Historical authority boundary

- Base commit: PR #108 exact candidate `71deefa2976a7005983d410907dc2050287be05f`.
- PR #108 itself remains unchanged and retains its separate independent-E3 merge requirement.
- Canonical specification inputs: API 1.0, IPC WCP360-IPC-B/1, Permission Registry v1.0, Security v1.0, DB Model v1.0, QA v1.0, DevOps v1.0 and Engineering v0.3 successor acceptance.
- `CODE-BASELINE = NONE` until governance/review promotes an implementation.
- This broad branch is **CANDIDATE / NOT PRODUCTION / NOT MERGE-AUTHORIZED AS A WHOLE**.

## Implementation strategy

The platform is split so contract-safe logic can progress without prematurely activating sensitive runtime choices:

1. `SOURCE/platform/` — framework-independent application/domain kernel in dependency-free PHP 8.4 candidate code.
2. `SOURCE/ui/` — dependency-free static UI shell implementing the active Design System's three-layer cockpit, USER/ADMIN separation, light/dark and responsive behavior.
3. `SOURCE/daemon/` — inherited inert Go `wcpd` foundation from PR #108; no new privileged behavior is added here by the broad branch.
4. Generated registries under `SOURCE/platform/contracts/` — machine-readable derivatives of canonical Markdown contracts; they do not supersede specifications.
5. Database, HTTP/session runtime, queues, privileged IPC and Linux adapters remain behind explicit ports until their concrete runtime/toolchain and review gates are activated.

## Non-negotiable security rules

No generic `shell.exec`, arbitrary command, arbitrary executable, arbitrary signal, generic `sudo`/`systemctl`, arbitrary privileged path, caller-declared roles/permissions or silent fallback is introduced.

WCP-USER and WCP-ADMIN remain separate surfaces. Unknown permission/action/contract values fail closed. Privileged effects require explicit typed handlers and later independent Security/Engineering/QA review before production activation.
