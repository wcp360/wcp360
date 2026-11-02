# Infrastructure Automation Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.infrastructure-automation-pro` |
| Category | `automation` |
| Core owners | Operations, Platform, IdentityAccess, Observability |
| Source catalogue keys | `automation.playbooks`, `automation.event-rules`, `automation.terraform-opentofu`, `automation.ansible` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced versioned playbooks, reusable workflow parameters and reviewed bulk infrastructure plans. Infrastructure-as-code and configuration-management integrations remain connectors using the same admitted Core contracts.

## Retained Core boundary

Core keeps its scheduler, jobs, API, validation and controlled system operations. This module adds optional workflow authoring and coordination; a workflow cannot register new privileged commands, bypass current policy or treat a dry-run label as proof of no effects.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
