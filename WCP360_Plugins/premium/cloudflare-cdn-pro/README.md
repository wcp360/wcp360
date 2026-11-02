# Cloudflare CDN and edge connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare optional Cloudflare CDN routing, reviewed performance/compression policies and scoped cache operations for explicitly owned sites.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.cloudflare-cdn-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `dns-cdn-domains` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `domains.cloudflare`, `domains.cdn-edge` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Core ingress, TLS ownership and application authorization remain authoritative. Native compression stays free Core; the all-traffic Nginx-to-Apache/.htaccess profile does not acquire an external cache by default. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Prove authenticated/private paths, cookies and uploads remain outside any unqualified cache policy.
2. Scope purge operations to the owned site and distinguish provider acceptance from observed invalidation.
3. Test TLS/origin conflicts, disabled integration and provider outage while existing hosting remains available.
4. Keep response caching disabled for the all-traffic Apache profile unless a separately admitted profile proves protected assets, nested .htaccess, authorization revocation, cookies, private responses and tenant cache-key/purge correctness.
5. Qualify origin TLS, origin exposure and bypass paths, trusted client-IP provenance and selected IPv4/IPv6/UDP/HTTP3 behavior before changing an admitted edge profile.
6. Review compression negotiation, Vary/ETag, purge propagation, stale-response behavior and rollback independently; compression is not permission to make content public and no speedup or DDoS capacity is guaranteed.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
