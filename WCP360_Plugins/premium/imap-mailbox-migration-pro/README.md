# IMAP mailbox migration connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare controlled mailbox copying and reconciliation from admitted IMAP sources to explicitly owned destinations.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.imap-mailbox-migration-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `migration-data` |
| Core owners | [Transfers](../../../WCP360_Core/src/Module/Transfers/README.md), [Mail](../../../WCP360_Core/src/Module/Mail/README.md), [IdentityAccess](../../../WCP360_Core/src/Module/IdentityAccess/README.md) |
| Existing catalogue keys | `mail.mailbox-migration` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Native mailbox administration and Core transfer safety remain free; external credentials cannot grant cross-tenant mailbox access. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify selected source and destination profiles with authenticated transport, explicit mailbox rights and secret-reference credentials.
2. Exercise folders, flags, UID changes, duplicate detection and interrupted copies while preserving source messages by default.
3. Bound connection rate, message size and storage usage; provide reconciliation evidence before any operator-approved mailbox cutover or source deletion.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
