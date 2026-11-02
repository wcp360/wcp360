# Blesta provisioning connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare an explicitly mapped Blesta billing integration for authorized hosting service requests.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.blesta-provisioning-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `billing-business` |
| Core owners | [Tenancy](../../../WCP360_Core/src/Module/Tenancy/README.md), [Setup](../../../WCP360_Core/src/Module/Setup/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `commerce.blesta` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Billing credentials cannot impersonate root, mutate arbitrary subscriptions or introduce alternate host execution paths. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify the selected Blesta instance and product-to-Core-plan mapping with explicit organization and tenant ownership.
2. Exercise duplicate provisioning requests, partial failure, retry and remote cancellation without creating duplicate hosting resources.
3. Keep local operator recovery available when billing is disabled and require a separate retained-data or purge decision.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
