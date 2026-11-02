# Portfolio Migration Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.portfolio-migration-pro` |
| Category | `transfers` |
| Core owners | Transfers, Tenancy, Hosting, Files, Data, Mail, Operations, BackupRestore |
| Source catalogue keys | `apps.fleet-management`, `mail.mailbox-migration`, `backup.transfers`, `automation.playbooks` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced coordinated migrations for many accounts or application portfolios, with provider-specific import plans, staged synchronization, dependency waves and cutover rehearsals. External-panel/format adapters remain separately selected and qualified.

## Retained Core boundary

Core retains ordinary instance transfers, resource mapping, supported import/export and recovery. This module adds optional portfolio planning and bulk coordination; it cannot replace resource owners, claim arbitrary source compatibility or remove the only recovery route.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
