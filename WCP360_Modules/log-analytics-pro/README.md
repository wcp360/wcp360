# Log Analytics Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.log-analytics-pro` |
| Category | `observability` |
| Core owners | Observability, IdentityAccess, Operations |
| Source catalogue keys | `observe.loki`, `observe.syslog`, `security.siem`, `governance.audit-archive` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced authorized log exploration with correlation, saved investigations, bounded aggregation and export cases. Optional centralized sinks remain connectors; source logs and immutable business audit retain their original ownership.

## Retained Core boundary

Core keeps baseline logs, audit access, retention and diagnostic redaction. Log Analytics adds investigation conveniences, not unrestricted host journal access, a second audit ledger or permission to read another tenant's messages.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
