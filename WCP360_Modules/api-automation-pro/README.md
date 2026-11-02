# API Automation Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.api-automation-pro` |
| Category | `automation` |
| Core owners | Operations, IdentityAccess, Platform, Observability |
| Source catalogue keys | `automation.event-rules`, `automation.playbooks`, `automation.chatops` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced workflow composition over existing public API/events, with reusable scoped parameters, delivery traces and approved cross-resource automations. Channel- and provider-specific integrations remain separately qualified connectors.

## Retained Core boundary

Core keeps its APIs, SDK contracts, webhooks/events, jobs and ordinary automation primitives. This module adds optional workflow composition and correlation; it cannot invent new public endpoints, grants, arbitrary callbacks or raw privileged effects.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
