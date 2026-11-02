# Billing Orchestration Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.billing-orchestration-pro` |
| Category | `commercial` |
| Core owners | Tenancy, Operations, IdentityAccess, Observability |
| Source catalogue keys | `commerce.usage`, `commerce.whmcs`, `commerce.blesta`, `commerce.fossbilling`, `commerce.hostbill` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced commercial reconciliation between measured usage, external customer/product mappings and reviewed provisioning requests. Provider-specific billing integrations remain connectors; payment processing and pricing stay with the selected external business system.

## Retained Core boundary

Core plans, quotas, ownership, essential hosting, exports and recovery remain independent of billing. This module adds reconciliation and controlled commercial workflow requests; billing events cannot write Core tables, grant permissions or silently stop existing services.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
