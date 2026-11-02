# CDN Management Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.cdn-management-pro` |
| Category | `networking` |
| Core owners | Hosting, Platform, IdentityAccess, Operations, Observability |
| Source catalogue keys | `domains.cdn-edge`, `domains.global-routing`, `domains.cloudflare`, `domains.dns-providers`, `observe.synthetics` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced multi-site CDN/edge orchestration with provider-qualified delivery profiles, origin/DNS binding, cache rules, purge workflows, TLS review and source-attributed cache/availability observations. Specific CDN providers remain connectors and retain their actual capability limits.

## Retained Core boundary

Core domain/DNS/TLS authority, ordinary caching/compression, origin security and essential hosting remain free. The current Nginx -> Apache -> FPM target routes all content, including static files, through Apache with edge direct-file/response-cache off by default. This module adds advanced CDN policy coordination only; no protected/private response may move to an edge cache without explicitly admitted, versioned semantics, and no proprietary edge service becomes necessary for Core hosting or recovery.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
