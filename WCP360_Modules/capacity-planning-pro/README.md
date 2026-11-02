# Capacity Planning Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.capacity-planning-pro` |
| Category | `commercial` |
| Core owners | Tenancy, Platform, Observability, Operations |
| Source catalogue keys | `commerce.usage`, `compute.node-groups`, `automation.energy`, `hardware.sensors` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced capacity forecasting, scenario comparison and explained placement recommendations from qualified usage observations. Predictions identify assumptions, data freshness and uncertainty instead of claiming measured enforceable limits.

## Retained Core boundary

Core keeps actual quotas, metering, capacity checks and enforcement. Capacity Planning adds forecasts and optimization proposals; it cannot raise a quota, relocate a workload or interrupt service merely because a model suggests a saving.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
