# MCP HTTP provider connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare a version-qualified MCP HTTP integration for explicitly admitted external tools and read-oriented resources.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.mcp-http-connector-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `ai-mcp` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [ExtensionManagement](../../../WCP360_Core/src/Module/ExtensionManagement/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `automation.ai-readonly`, `automation.chatops` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

MCP is an optional orchestration protocol, not a replacement for Core permissions or a channel to wcpd, arbitrary stdio launchers or Docker sockets. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Pin a reviewed MCP revision and HTTP transport profile with admitted server identity, issuer, audience, redirect and egress rules.
2. Qualify OAuth state, token audience and resource binding; reject token passthrough, cross-tenant credentials and unapproved destinations.
3. Treat tool descriptions and outputs as hostile content; keep allowlisted capabilities read-only initially and require a new review for any effectful use case.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
