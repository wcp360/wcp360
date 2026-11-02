# Nginx Proxy Manager interoperability

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare explicit proxy-host, stream and TLS inventory/import planning for a selected Nginx Proxy Manager deployment.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.nginx-proxy-manager-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `proxy-platforms` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `domains.cdn-edge` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

This proposal does not assume a stable management API or generic clustered load balancer. Native Nginx ingress remains free Core; no automatic port 80/443 takeover or competing TLS writer is admitted. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Start with a reviewed read-only observation/export profile; qualify an exact upstream management contract before proposing automated mutations.
2. Bind proxy hosts, destinations, access lists, certificates and streams to owned resources; reject arbitrary configuration, backend URLs and transfer of active Core-managed ingress without approval.
3. Exercise private-route/access-list behavior, Apache/.htaccess preservation, origin/TLS/client-IP handling and failed cutover recovery; retain prior ingress ownership until the handoff is explicitly complete.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

The upstream guide establishes proxy-host, stream, TLS and access-list concepts. It does not qualify a stable automation API or prove a generic clustered load-balancing contract for this proposal. Initial interoperability remains observation/export; native Core ingress is unchanged.

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [Nginx Proxy Manager guide](https://nginxproxymanager.com/guide/)
