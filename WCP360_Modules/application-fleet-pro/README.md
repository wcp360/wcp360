# Application Fleet Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.application-fleet-pro` |
| Category | `applications` |
| Core owners | Hosting, Tenancy, Operations, Observability |
| Source catalogue keys | `apps.fleet-management`, `apps.verified-updates`, `workspace.dashboards` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Cross-application portfolio views with version/dependency drift, qualified campaign targeting, bounded rollout batches and per-instance reporting. Operators can schedule reviewed maintenance across explicitly selected authorized applications.

## Retained Core boundary

Individual site/application operations, baseline inventories, ordinary bulk authorization and job tracking remain in the Core. Fleet views reuse those authorities and do not introduce a global tenant bypass or infer that every application has an installable recipe.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
