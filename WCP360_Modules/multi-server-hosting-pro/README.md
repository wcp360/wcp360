# Multi-server Hosting Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.multi-server-hosting-pro` |
| Category | `infrastructure` |
| Core owners | Platform, Hosting, Tenancy, IdentityAccess, Operations, Observability, BackupRestore, Data, Mail, Transfers |
| Source catalogue keys | `compute.node-groups`, `compute.rolling-operations`, `runtime.workers`, `network.service-ha`, `automation.playbooks` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced hosting orchestration across admitted servers and separately qualified Web, database, mail, authoritative DNS or mixed node roles: enrollment/trust, placement, scoped routing, capacity groups, maintenance/drain waves and fleet care. Resource owners and role-specific data/recovery semantics stay authoritative; this proposal is not a universal cluster implementation.

## Retained Core boundary

Single-node hosting, identity/tenant boundaries, service control, local recovery and any future admitted node/provider/transport foundations remain open-source Core responsibilities. This premium module adds multi-server workflow intent and coordination only; it has no raw shell, privileged socket, direct Core database access or implicit authority over remote servers.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
