# Collaboration Approvals Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.collaboration-approvals-pro` |
| Category | `workspace` |
| Core owners | IdentityAccess, Operations, Tenancy, Observability |
| Source catalogue keys | `automation.playbooks`, `support.reports`, `workspace.dashboards` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced team review queues, separation-of-duties proposals, change annotations and approval evidence for optional operational workflows. Approvals refer to exact immutable plans and never replace authorization.

## Retained Core boundary

Core retains required confirmations, sensitive-action reauthentication, audit and current permission checks. This module adds optional collaborative coordination; it cannot impose a paid approval gate on essential Core use or grant actions to reviewers lacking underlying authority.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
