# Amazon Route 53 DNS connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare hosted-zone mappings and bounded record/challenge changes for a chosen AWS account.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.aws-route53-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `dns-cdn-domains` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [IdentityAccess](../../../WCP360_Core/src/Module/IdentityAccess/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `domains.route53`, `domains.dns01` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Hosted-zone observation is not authority to replace native DNS, seize a domain or select arbitrary account resources. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Bind account, hosted-zone identity and change batch to a current Hosting domain revision.
2. Keep private and public zones distinct and reject ambiguous names or unsupported routing records.
3. Exercise asynchronous change inspection, challenge ownership and safe cleanup after lost acknowledgements.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
