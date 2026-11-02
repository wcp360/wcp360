# Traefik routing interoperability

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare redacted Traefik route observations and a separately qualified dynamic-provider interoperability profile.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.traefik-routing-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `proxy-platforms` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `network.traefik` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Native ingress, TLS and baseline routing remain free Core. The dashboard/API is not treated as a generic write API; the plugin gets no Docker socket, arbitrary configuration file or public admin endpoint. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify a selected API/provider profile and exact entrypoint/router/service ownership; begin with authenticated internal observations and redact sensitive configuration.
2. Admit any future route effects through a reviewed Core/provider writer, not arbitrary labels, files, backend URLs or a fabricated dashboard mutation API.
3. Preserve the target Apache all-traffic/.htaccess contract, TLS ownership and trusted client-IP chain; exercise conflicting writers, failed reload/withdrawal and private-route recovery before handoff.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

The documented dashboard/API exposes observations through GET endpoints and may disclose sensitive configuration. An effectful provider contract must be reviewed separately; this proposal does not invent a dashboard write API.

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [Traefik API/dashboard](https://doc.traefik.io/traefik/reference/install-configuration/api-dashboard/)
- [Traefik file routing provider](https://doc.traefik.io/traefik/reference/dynamic-configuration/file/)
