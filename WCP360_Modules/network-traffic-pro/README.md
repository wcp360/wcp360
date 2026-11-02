# Network Traffic Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.network-traffic-pro` |
| Category | `networking` |
| Core owners | Platform, Hosting, Operations, Observability |
| Source catalogue keys | `network.haproxy`, `network.traefik`, `domains.global-routing`, `domains.cdn-edge`, `network.wireguard` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced load-balancer, global-routing, ingress and overlay-network orchestration with reviewed listeners/upstreams, measured checks and staged changes across qualified HAProxy, Traefik, Nginx Proxy Manager or other providers. Provider connectors supply admitted capabilities; CDN cache/content delivery, DDoS response and workload placement retain distinct workflow owners.

## Retained Core boundary

Core retains baseline IP/network/firewall administration, native gateway/provider foundations, management-access recovery and the seven essential engine adapters. This module coordinates advanced traffic policy through accepted owned operations, not arbitrary scans, tunnels, raw firewall commands or a competing routing authority.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
