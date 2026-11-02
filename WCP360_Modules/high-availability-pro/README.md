# High Availability Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.high-availability-pro` |
| Category | `infrastructure` |
| Core owners | Platform, Hosting, Data, Operations, Observability, BackupRestore |
| Source catalogue keys | `network.vrrp-vip`, `network.service-ha`, `compute.node-groups`, `compute.rolling-operations` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced multi-node service coordination, failover rehearsals, leader/fence evidence and rolling-maintenance workflows. Availability claims are limited to the provider, topology, workload and failure cases actually qualified.

## Retained Core boundary

Core remains a complete standalone panel with essential recovery and ordinary service lifecycle. This module adds optional multi-node coordination; it does not turn templates into a cluster manager or make cross-node authority part of an unreviewed plugin.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
