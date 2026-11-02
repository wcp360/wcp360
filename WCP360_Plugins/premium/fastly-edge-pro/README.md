# Fastly edge connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare reviewed Fastly edge mappings, performance/compression policy diffs and bounded cache invalidation for owned sites.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.fastly-edge-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `dns-cdn-domains` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `domains.cdn-edge` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

An optional edge provider cannot replace Core origin ownership or privacy/access rules. Native compression remains free Core and the all-traffic Nginx-to-Apache/.htaccess profile keeps direct-file and response caching off by default. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Bind account, service and candidate configuration version to an owned site and an immutable review.
2. Qualify purge granularity and cache bypass with private sessions and authorization-dependent responses.
3. Exercise failed activation, uncertain rollback and provider outages without changing local origin service.
4. Enable cache handoff only through a separately admitted profile proving protected assets, nested .htaccess, authorization revocation, cookies, private responses and tenant cache-key/purge safety; otherwise bypass cache.
5. Qualify admitted origin TLS, origin exposure, bypass paths, trusted client-IP provenance and selected IPv4/IPv6/UDP/HTTP3 behavior without weakening origin policy.
6. Separate content-encoding negotiation, Vary/ETag and measured performance from cacheability; verify purge observation and rollback without guaranteeing speedup, attack absorption or upstream capacity.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
