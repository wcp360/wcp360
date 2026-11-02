# VPS / VM Management Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.vps-vm-management-pro` |
| Category | `infrastructure` |
| Core owners | Platform, Tenancy, IdentityAccess, Hosting, Files, Operations, Observability, BackupRestore |
| Source catalogue keys | `compute.proxmox`, `compute.libvirt`, `compute.lxc`, `compute.images`, `network.interfaces`, `storage.raid-lvm` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced VPS and virtual-machine lifecycle orchestration across qualified hypervisors/providers: reviewed templates and cloud-init inputs, guest identities, virtual disks, IP/vNIC policy, effective quotas, snapshots, backups, maintenance and explicitly supported migrations. VM, system-container and provider semantics remain distinct.

## Retained Core boundary

Core single-node hosting, baseline network/storage/security, account policy and future admitted virtualization-provider foundations remain open source. This module coordinates guest operations through owned contracts. Admitted cloud-init may carry guest code/configuration, including authorized guest-root actions, within a qualified profile; guest-root never confers host-root. No payload becomes a host hook, host-path escape, unrestricted hypervisor command or direct Core database write.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
