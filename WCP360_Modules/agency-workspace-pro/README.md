# Agency Workspace Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.agency-workspace-pro` |
| Category | `commercial` |
| Core owners | Tenancy, IdentityAccess, Hosting, Observability |
| Source catalogue keys | `workspace.white-label`, `workspace.dashboards`, `support.helpdesk` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced agency and reseller portfolio workspaces with scoped branding, cross-account productivity views, reviewed client handoffs and optional support-system integration. It reuses free Core reseller packages, templates, quotas, assignments and delegation rather than selling an alternative account or hosting-plan authority.

## Retained Core boundary

Core Tenancy retains customer/reseller lifecycle, resource ceilings, plan/package and template builders, quota rules, versioning, assignments, scoped overrides, ordinary delegation and essential UI. This module adds advanced portfolio productivity and branding only; no paid workspace can gate those features, change tenant authority, hide operator identity or become necessary for hosting access.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
