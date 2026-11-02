# Remote Backup Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.remote-backup-pro` |
| Category | `recovery` |
| Core owners | BackupRestore, Operations, Files, IdentityAccess |
| Source catalogue keys | `backup.sftp`, `backup.s3`, `backup.nfs-destination`, `backup.restic`, `backup.borg`, `backup.immutable` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Remote backup policy orchestration across qualified destinations and engines, with scoped repository bindings, encryption-key references, transfer receipts, retention review and verified restore references. Storage and engine connectors remain independently selected packages.

## Retained Core boundary

Local backup/restore, essential retention controls and independent recovery stay in Core. This module adds optional remote copies and policy coordination; it must not hold the only recovery path or export raw backup keys into logs or package metadata.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
