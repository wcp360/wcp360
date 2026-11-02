# AI Assistant Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.ai-assistant-pro` |
| Category | `automation` |
| Core owners | Platform, IdentityAccess, Operations, Observability, ExtensionManagement |
| Source catalogue keys | `automation.ai-readonly`, `automation.ai-missions`, `automation.chatops` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Optional evidence-linked AI assistance for authorized diagnostics, followed only by reviewed action proposals where underlying operations are qualified. Model/provider connectors and MCP transports remain separately admitted integrations.

## Retained Core boundary

Core stays usable without AI, external model access or premium entitlement. The assistant has no independent permissions, root shell, unrestricted tool registry or hidden execution path; generated text and imported instructions never become execution authority.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
