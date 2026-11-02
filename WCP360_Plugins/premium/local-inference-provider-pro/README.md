# Local inference provider connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare a selected local inference service integration with explicit model, resource and data-isolation policies.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.local-inference-provider-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `ai-mcp` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [ExtensionManagement](../../../WCP360_Core/src/Module/ExtensionManagement/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `automation.ai-readonly`, `automation.ai-missions` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

An AI model and its orchestration agent are separate from Go wcpd; this proposal does not download weights or grant a model host authority. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify one admitted inference endpoint and model artifact with provenance, applicable license, integrity and resource requirements.
2. Bound CPU, memory, accelerator use and request concurrency without promising universal hardware or operating-system support.
3. Exercise cross-tenant prompt isolation and malicious model output; keep root actions unavailable and retain useful Core operation when inference stops.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
