# Stripe billing connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare payment-event and subscription-state synchronization with an explicitly selected Stripe account.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.stripe-billing-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `billing-business` |
| Core owners | [Tenancy](../../../WCP360_Core/src/Module/Tenancy/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md), [IdentityAccess](../../../WCP360_Core/src/Module/IdentityAccess/README.md) |
| Existing catalogue keys | `commerce.business-systems` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Billing remains optional; payment events never directly execute host actions, elevate permissions or gate essential local server recovery. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify account-scoped webhook verification, replay protection, event ordering and idempotent reconciliation before state changes.
2. Map products, currencies, refunds, disputes and subscription periods to reviewed business rules rather than inferred provisioning rights.
3. Keep payment credentials and personal data minimized; provider outages or expired entitlements must preserve existing hosting and administrator recovery.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
