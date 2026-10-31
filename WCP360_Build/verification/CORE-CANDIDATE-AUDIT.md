# Core candidate source and execution audit

Audit date: 2026-10-31. Status: implementation evidence, not independent review or production qualification.

## Source inventory and decisions

| Source | Immutable revision | Finding | Decision |
| :--- | :--- | :--- | :--- |
| Current organized repository | 7eddd41a4ef62dd6a3c737481b304f67bca4f5ba | Core/SDK/Build were structural documentation; no runnable Symfony bootstrap or dependency lock. | Implement a separately identified development candidate in the prepared ownership locations. |
| Full platform candidate, PR #109 | 284daabc8d57a65d3ad11e326baee32898e64254 | Dependency-free PHP policy/domain library, derived registries and 86 typed handler ports; no Symfony, real persistence, HTTP server or privileged runtime. | Run unchanged tests and canonical-source checks in an ignored isolated audit checkout. Reuse four session-policy/type classes with mechanical namespace adaptation and recorded provenance. Do not expose unimplemented handler ports as completed APIs. |
| Domain foundation, PR #110 | 31fd3fe4d9203d9e5dbc2b43843c6c9ec74f2940 | Go policy/contract consistency code, layered on the inert daemon. | Preserve the branch and its review identity. Do not promote pure policies as a running broker. |
| Inert daemon, PR #108 | 71deefa2976a7005983d410907dc2050287be05f | Narrow inert CLI foundation with a specific qualification contract. | Leave its head/acceptance evidence unchanged. The new wcp-plan tool is explicitly separate and has no daemon/listener/privileged effects. |
| File Manager package | Existing standalone package | Single-owner alpha, not a multi-tenant authorization boundary. | Do not embed its PHP entry points into Core or imply tenant safety through packaging. |
| Browser prototype | WCP360_Prototype | UI simulation; not the backend authority or a real installation. | Keep it separate. Implement real candidate views in Core templates/assets. |

## Verified locally

- PHP 8.4.27 portable x64 NTS downloaded from the PHP project's official Windows distribution and verified with its SHA-256.
- Composer 2.11.1 verified against the published SHA-256; real dependency resolution and lock produced.
- Go 1.27.3 Windows amd64 archive verified with the official SHA-256; planner unit tests, CLI schema tests, go vet and compilation pass.
- Unchanged PHP candidate: 31/31 tests pass. Registry checks pass for 82 permissions, 86 API contracts/actions, 30 action domains, 23 errors, 47 IPC operations, 54 physical tables and eight session-policy rows.
- Canonical input Git blob fingerprints match. The current database publication contains 54 physical table headings; an older 52-table description must not be treated as current physical coverage.
- New Symfony candidate: the application-server suite reports 51 tests / 125 assertions, with 50 passing and one Windows-only skip for the hidden-input console interaction. The complete wizard interaction is assigned to Linux CI. Coverage includes real form authentication, forged CSRF rejection, cross-context/cross-tenant denial, SQL composite-FK integrity, idempotency, audit failure rollback, audit immutability, permission revocation, job interruption and detached plugin lifecycle.
- Kernel startup, container injection checks, Twig syntax, asset compilation and Composer validation/platform checks pass. Composer reports no known dependency advisories at the audit time; this is not a source security audit.

## Application-server candidate

The hosted-site selector adds PHP-FPM defaults, opt-in FrankenPHP Worker plans, explicit compatibility acknowledgement, revision-checked ADMIN settings and stable idempotency across default changes. The added migration preserves old sites on PHP-FPM. Go preserves the original FPM wire shape and emits a separate worker schema with dedicated Unix identities/sockets. See [application-server design and verification](../../documentation/development/APPLICATION-SERVERS.md).

A separate immutable-image Linux job validates actual FrankenPHP configuration, persistent requests, application cleanup, worker recycling and filesystem/socket isolation. Its definition is not a successful-run claim: use the exact run attached to the current PR head. Production activation and Ubuntu/AlmaLinux qualification remain unimplemented.

## Target-platform evidence

The first complete [successful Linux workflow](https://github.com/yyogas/wcp360/actions/runs/41231366927) tested source f3ce52c15ece85b03f750d59ed1fe4ad717c47c0: 34 application tests / 81 assertions pass against PostgreSQL 17 on Debian 13/PHP 8.4. The real Nginx/PHP-FPM HTTP probe, other-user file-read denial, hidden-file rejection, missing-PHP rejection and backup/upgrade/empty-database restore rehearsal also pass.

The suite was then expanded with the unavailable-package disable/uninstall regression and enrollment command checks. Consult the latest run linked from [PR #116](https://github.com/yyogas/wcp360/pull/116) for that exact head; the earlier 34-test result is not automatically reassigned to later changes. Windows/SQLite results are not Debian/PostgreSQL evidence.

The workstation has no local Docker installation or WSL distribution. Its preinstalled PHP 8.3/XAMPP was left unchanged. Downloaded toolchains, candidate audit copies, Composer dependencies, compiled binaries, frontend assets and generated state remain ignored local build/runtime output.

## Review and promotion

All eight authoritative specification inputs and the baseline manifest remain unchanged. No existing branch is merged or deleted, and no prior E3 evidence is reassigned. The new implementation branch is a draft for applicable owner handoffs, independent Engineering/Security review and further runtime implementation.

No production CODE-BASELINE, qualified release archive/binary, signature, version directory or latest pointer is created. The release gate refuses stable publication. See the [run guide and seven-stage acceptance table](../../documentation/development/EXECUTABLE-CANDIDATE.md).
