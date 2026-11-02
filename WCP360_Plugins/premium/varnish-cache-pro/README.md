# Varnish-compatible managed cache connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare an optional reviewed external HTTP-cache profile with explicit application safety and measured performance evidence.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.varnish-cache-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `performance-cache` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `domains.cdn-edge` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Native web routing, compression and access control stay free Core. The target Nginx-to-Apache all-traffic/.htaccess profile keeps direct-file and response caching disabled; this proposal does not change that. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Select and qualify an admitted cache distribution/API profile and Core binding; do not expose a raw administration socket, arbitrary VCL or backend URL to the plugin.
2. Enable cache handoff only under a separately admitted profile proving protected assets, nested .htaccess, authorization revocation, cookies, private responses and tenant cache-key/purge correctness; otherwise bypass caching entirely.
3. Qualify content encoding, Vary/ETag, freshness, invalidation and rollback independently with reproducible application measurements; compression is not public cache permission and no universal speedup is promised.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
