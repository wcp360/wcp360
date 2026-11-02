# Performance Booster Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.performance-booster-pro` |
| Category | `performance` |
| Core owners | Hosting, Platform, Data, Tenancy, Operations, Observability, BackupRestore |
| Source catalogue keys | `runtime.redis`, `runtime.memcached`, `domains.cdn-edge`, `data.sql-analysis`, `observe.synthetics` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced application-aware performance profiles coordinating compatible caches, compression, static/edge delivery, runtime budgets and measured tuning proposals. Profiles distinguish server/runtime/application semantics and qualify correctness before any throughput or latency claim.

## Retained Core boundary

Core retains ordinary caching/compression, native gzip/Brotli where admitted, PHP/OPcache policy, quotas, essential observations and native provider foundations as free capabilities. This module adds comparative measurements, advanced profiles and optimization campaigns only. The current Nginx -> Apache -> FPM target keeps all files under Apache authorization with edge direct-file/response-cache disabled by default; no premium optimization can bypass that authority, tenant isolation or secret-bearing application behavior.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
