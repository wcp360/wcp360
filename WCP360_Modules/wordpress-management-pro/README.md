# WordPress Management Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.wordpress-management-pro` |
| Category | `applications` |
| Core owners | Hosting, Data, Files, Operations, BackupRestore |
| Source catalogue keys | `apps.wordpress-toolkit`, `apps.verified-updates`, `security.advisories` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced WordPress portfolio management covering instance discovery, core/plugin/theme inventory, compatible update campaigns, maintenance windows, hardening recommendations and source-attributed vulnerability tracking. Optional tooling is version-qualified and operates under each site's identity.

## Retained Core boundary

Ordinary WordPress hosting, PHP stack selection, files, databases, permissions and recovery remain Core capabilities. This module orchestrates reviewed WordPress-specific workflows; it neither guarantees Worker compatibility nor grants access to premium upstream themes/plugins without their own valid terms.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
