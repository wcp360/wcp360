# Coraza managed-policy connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare optional reviewed Coraza policy and defensive-observation integration through a qualified Core binding.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.coraza-waf-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `security-defense` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `security.waf`, `security.advisories` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

This is managed policy integration, not a paywall for a Core engine or permission to inject Go code, arbitrary modules, shell hooks or unrestricted rules. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Select and qualify an exact Core binding and Coraza/CRS capability profile instead of assuming interchangeability across Caddy, Nginx, Apache or application runtimes.
2. Stage site-scoped policy changes with body/resource limits, detection/block decisions, private-route fixtures and measured false-positive/rollback evidence.
3. Exercise policy provenance failure, malicious rule input and provider outage while preserving baseline protection and local recovery; make no universal prevention or performance guarantee.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
