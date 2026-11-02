# Privileged Access Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.privileged-access-pro` |
| Category | `identity` |
| Core owners | IdentityAccess, Operations, Observability, Platform |
| Source catalogue keys | `identity.vaults-kms`, `workspace.web-terminal`, `automation.playbooks` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced just-in-time access requests, reviewed delegation windows and evidence around explicitly approved administrative sessions or secret consumers. Any terminal/provider session is separately qualified and restricted to its authorized non-root identity.

## Retained Core boundary

Core owns authorization, credential/session revocation, secrets and the minimal privileged broker. This module adds request/review coordination, not a root terminal, a new permission evaluator or an entitlement-based bypass of existing policy.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
