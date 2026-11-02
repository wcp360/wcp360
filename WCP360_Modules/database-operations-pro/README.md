# Database Operations Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.database-operations-pro` |
| Category | `data` |
| Core owners | Data, Operations, BackupRestore, Platform |
| Source catalogue keys | `data.replication`, `data.postgres-extensions`, `data.external-databases` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced database topology workflows covering replication planning, replica initialization, lag review, controlled promotion and qualified extension campaigns. Engine-specific capabilities and external-service limits remain explicit.

## Retained Core boundary

Database creation, users/grants, credential rotation, import/export, baseline diagnostics and ordinary backups stay in the free Core. Database Operations adds optional advanced orchestration and never receives the control-plane PostgreSQL credentials.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
