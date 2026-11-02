# Storage Orchestration Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.storage-orchestration-pro` |
| Category | `infrastructure` |
| Core owners | Platform, Files, Tenancy, BackupRestore, Operations |
| Source catalogue keys | `storage.zfs`, `storage.ceph`, `storage.glusterfs`, `storage.object-storage`, `storage.raid-lvm` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced storage-pool and distributed-storage workflows using qualified providers, topology observations, snapshot coordination and controlled migration plans. Per-engine semantics, effective limits and recovery remain explicit.

## Retained Core boundary

Core retains local storage, filesystem quotas, ownership and ordinary backup/restore. This module adds optional advanced storage orchestration and cannot expose arbitrary device paths, mount commands or unqualified replacements for essential Core engine adapters.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
