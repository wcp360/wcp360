# Kubernetes Management Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.kubernetes-management-pro` |
| Category | `infrastructure` |
| Core owners | Platform, Hosting, IdentityAccess, Tenancy, Operations, Observability, Files, Data, BackupRestore |
| Source catalogue keys | `compute.kubernetes`, `compute.images`, `compute.node-groups`, `compute.rolling-operations`, `network.service-ha` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced Kubernetes and multi-cluster application orchestration: admitted cluster contexts, namespaces/RBAC, workloads, services/ingress/TLS, persistent volumes, quotas, observations, controlled upgrades and restore. It preserves provider/cluster-specific behavior and does not infer tenant isolation from a namespace name alone.

## Retained Core boundary

Core resource ownership, current permissions, ordinary hosting/security/storage and any future admitted cluster/provider foundations remain OSS. The module adds reviewed advanced orchestration; it cannot issue cluster-admin kubeconfigs to users, expose unrestricted Kubernetes APIs, write Core tables or replace authoritative workload identity.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
