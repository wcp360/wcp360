# Upstream DDoS scrubbing provider connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare an explicitly selected upstream L3/L4 mitigation provider profile and authorized routing/incident coordination.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.upstream-ddos-scrubbing-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `security-defense` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `network.sdn`, `domains.global-routing`, `security.threat-intel` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Volumetric upstream mitigation is distinct from local L7 WAF, rate limits and firewall controls. This proposal provides no guarantee of attack absorption or customer uplink capacity. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify provider coverage, admitted prefixes/services, ownership, activation authority, routing/tunnel dependencies and IPv4/IPv6/UDP/HTTP3 limits before any proposed diversion.
2. Coordinate origin exposure, trusted client-IP provenance and bypass paths; separate upstream L3/L4 controls from local L7 response and require current approval for routing effects.
3. Exercise false positives, stale mitigation requests, upstream outage and withdrawal/rollback with synthetic isolated evidence only; no live customer attack, capacity promise or guaranteed mitigation result.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
