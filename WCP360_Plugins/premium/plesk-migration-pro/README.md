# Plesk portfolio migration connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare a selected Plesk export integration for explicit resource mapping and staged migration planning.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.plesk-migration-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `migration-data` |
| Core owners | [Transfers](../../../WCP360_Core/src/Module/Transfers/README.md), [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Mail](../../../WCP360_Core/src/Module/Mail/README.md), [Data](../../../WCP360_Core/src/Module/Data/README.md) |
| Existing catalogue keys | `apps.fleet-management`, `mail.mailbox-migration`, `backup.transfers` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

The connector does not convert every extension, permission model or web-server directive automatically; Core retains destination admission. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Pin source export and product profiles and record all supported objects, excluded data and incompatible extension settings.
2. Validate archive paths, tenant boundaries, object ownership and secret-reference handling before destination import plans.
3. Exercise repeatable staging, delta reconciliation and explicit cutover/rollback with source data retained until a separate purge decision.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
