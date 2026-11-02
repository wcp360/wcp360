# Additional ACME authority connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare an explicitly selected additional ACME authority/account policy using already admitted challenge providers.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.external-acme-authority-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `certificate-providers` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [IdentityAccess](../../../WCP360_Core/src/Module/IdentityAccess/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `domains.acme-authorities`, `domains.dns01` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Core ACME, automatic renewal, wildcard DNS-01 and baseline TLS remain free. This proposal adds authority-specific interoperability and does not duplicate Cloudflare/Route53 DNS drivers or become a competing certificate writer. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify an admitted authority endpoint, account policy, trust chain and any external-account-binding requirements; keep private/account keys in durable scoped secret storage without selecting a vendor or promising unlimited issuance.
2. Use existing admitted DNS-01 provider scopes for authorized wildcard names, measured TXT propagation and concurrent-order-safe cleanup; no broad DNS credentials or deletion of another challenge is allowed.
3. Exercise rejected orders, rate limits, expiry, renewal and restart recovery with persisted order/account state; avoid new issuance on every restart and preserve installed certificates during provider outage or removal.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

No additional authority is selected. ACME issuance, renewal and wildcard DNS-01 remain baseline Core capabilities; any additional authority/EAB profile must be qualified with the existing scoped DNS providers.

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [ACME challenge types and DNS-01](https://letsencrypt.org/docs/challenge-types/)
- [ACME integration guidance](https://letsencrypt.org/docs/integration-guide/)
