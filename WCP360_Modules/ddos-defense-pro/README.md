# DDoS Defense Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.ddos-defense-pro` |
| Category | `security` |
| Core owners | Platform, Hosting, IdentityAccess, Operations, Observability |
| Source catalogue keys | `security.crowdsec`, `security.waf`, `security.threat-intel`, `domains.cdn-edge`, `network.haproxy`, `network.service-ha` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced DDoS observation and response coordination across qualified upstream scrubbing/edge providers and admitted local L7 controls. It separates network/transport-layer volumetric mitigation from application-layer rate/WAF policy and reports uncertainty, capacity limits and observed outcomes.

## Retained Core boundary

Core retains essential firewall/rate/protection policy, abuse controls, incident handling and network recovery. This module adds optional advanced DDoS workflows; local rules cannot guarantee protection against upstream link saturation, and no premium entitlement grants arbitrary blocking, traffic capture, attack tooling or root/network authority.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
