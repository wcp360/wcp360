# Docker Management Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.docker-management-pro` |
| Category | `infrastructure` |
| Core owners | Platform, Hosting, Tenancy, Files, Data, Operations, Observability, BackupRestore |
| Source catalogue keys | `compute.containers`, `compute.images`, `compute.rolling-operations`, `backup.restore-tests` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced Docker/OCI and admitted Compose workload orchestration: reviewed images/build provenance, application groups, networks, ports, volumes, secrets, effective budgets, health/logs, coordinated updates and restore. Qualified Portainer, Coolify or Dokku connectors can supply declared managed-resource capabilities; they are distinct external products, not interchangeable Docker engines.

## Retained Core boundary

Essential native hosting, storage, isolation, permissions and any future qualified Docker/provider foundations remain open source. This module adds advanced workload orchestration through Core contracts; it never hands a tenant or package the host Docker socket, unrestricted daemon API, privileged containers, raw root commands or Core credentials.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
