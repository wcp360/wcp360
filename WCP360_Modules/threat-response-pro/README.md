# Threat Response Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.threat-response-pro` |
| Category | `security` |
| Core owners | IdentityAccess, Platform, Observability, Operations, Hosting |
| Source catalogue keys | `security.siem`, `security.crowdsec`, `security.threat-intel`, `automation.event-rules` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced response playbooks that correlate selected security signals and propose scoped containment sequences. Reviewed automation can coordinate existing session, account, traffic or application operations with evidence and recovery tracking.

## Retained Core boundary

Core retains baseline alerts, incident handling, session revocation, access blocks and controlled system effects. This module adds optional response coordination; third-party intelligence, a rule match or commercial entitlement never grants an action permission.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
