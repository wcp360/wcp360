# External PostgreSQL migration connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare a reviewed external PostgreSQL migration profile with explicit compatibility and recovery checkpoints.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.external-postgres-migration-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `migration-data` |
| Core owners | [Transfers](../../../WCP360_Core/src/Module/Transfers/README.md), [Data](../../../WCP360_Core/src/Module/Data/README.md), [BackupRestore](../../../WCP360_Core/src/Module/BackupRestore/README.md) |
| Existing catalogue keys | `data.external-databases`, `data.replication`, `backup.transfers` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Core PostgreSQL metadata and basic database management stay free; migration credentials do not authorize unrestricted SQL or host execution. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify exact source and destination server, client, extension, encoding and collation profiles with explicit migration privileges.
2. Review backup consistency, role mapping, large objects, extension availability and replication assumptions before any staged transfer.
3. Rehearse restore and verification on an isolated target; require explicit cutover and rollback decisions without promising zero downtime or universal version compatibility.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
