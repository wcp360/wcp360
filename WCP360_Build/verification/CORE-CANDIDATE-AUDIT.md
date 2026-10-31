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
- New Symfony candidate: 34 tests, 81 assertions pass on PHP 8.4.27 with SQLite fixtures. Coverage includes real form authentication, forged CSRF rejection, cross-context/cross-tenant denial, SQL composite-FK integrity, idempotency, audit failure rollback, audit immutability, permission revocation, job interruption and detached plugin lifecycle.
- Kernel startup, container injection checks, Twig syntax, asset compilation and Composer validation/platform checks pass. Composer reports no known dependency advisories at the audit time; this is not a source security audit.

## Target-platform evidence

The supplied GitHub workflow runs the new candidate against real PostgreSQL 17 on a Debian 13/PHP 8.4 image, followed by a real Nginx/PHP-FPM HTTP probe and backup/restore rehearsal. Read its actual run result before declaring these checks passed. Windows/SQLite results are not Debian/PostgreSQL evidence.

The workstation has no local Docker installation or WSL distribution. Its preinstalled PHP 8.3/XAMPP was left unchanged. Downloaded toolchains, candidate audit copies, Composer dependencies, compiled binaries, frontend assets and generated state remain ignored local build/runtime output.

## Review and promotion

All eight authoritative specification inputs and the baseline manifest remain unchanged. No existing branch is merged or deleted, and no prior E3 evidence is reassigned. The new implementation branch is a draft for applicable owner handoffs, independent Engineering/Security review and further runtime implementation.

No production CODE-BASELINE, qualified release archive/binary, signature, version directory or latest pointer is created. The release gate refuses stable publication. See the [run guide and seven-stage acceptance table](../../documentation/development/EXECUTABLE-CANDIDATE.md).
