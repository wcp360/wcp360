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

Advanced service-continuity coordination, failover rehearsals, leader/fence evidence and rolling-maintenance workflows on already admitted nodes/providers. Availability is scoped to qualified topology/workload/failure cases; node enrollment and placement orchestration belong to Multi-server Hosting Pro, while application releases remain deployment workflows.

## Retained Core boundary

Core remains a complete standalone panel with essential recovery, ordinary service lifecycle and open-source node/provider authority when implemented. This module adds optional cross-node continuity coordination; it creates neither a second node registry nor an unreviewed remote root/cluster-control path.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
