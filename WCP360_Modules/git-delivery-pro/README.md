# Git Delivery Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.git-delivery-pro` |
| Category | `delivery` |
| Core owners | Hosting, Files, Operations, IdentityAccess |
| Source catalogue keys | `delivery.git`, `delivery.pipelines`, `delivery.runners`, `delivery.dependency-analysis` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Repository-driven CI/CD with versioned branch/tag policy, isolated builds, immutable artifacts, environment binding and deployment history. Qualified native or Compose-derived plans remain scoped deployment intent; Git/webhook and Portainer/Coolify/Dokku integrations are separately admitted connectors, never implicit host executors.

## Retained Core boundary

Core owns sites, current authorization, admitted deployment effects, secrets and durable jobs. Git Delivery adds optional repository and pipeline orchestration; repository files, Compose syntax and external managers cannot register privileged commands, gain host sockets, modify Core dependencies or create a competing resource owner.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
