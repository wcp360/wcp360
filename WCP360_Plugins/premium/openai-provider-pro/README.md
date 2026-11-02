# OpenAI assistance provider connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare an optional OpenAI provider adapter for explicitly consented, redacted assistance and reviewed operation proposals.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.openai-provider-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `ai-mcp` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [ExtensionManagement](../../../WCP360_Core/src/Module/ExtensionManagement/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `automation.ai-readonly`, `automation.ai-missions` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

A model response is untrusted content; Core retains identity, permissions, approvals and all host execution decisions. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Select a reviewed provider interface and model policy with explicit data-disclosure consent, retention settings and bounded cost controls.
2. Exercise prompt injection, secret extraction, cross-tenant context leakage and invented operation requests using redacted fixtures.
3. Keep assistance read-only by default; any future tool proposal requires current Core authorization, explicit approval and bounded audited use cases.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
