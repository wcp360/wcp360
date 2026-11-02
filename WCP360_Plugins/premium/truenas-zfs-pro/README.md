# TrueNAS and ZFS storage connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare a qualified TrueNAS API profile for explicitly owned datasets, shares, snapshots and storage observations.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.truenas-zfs-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `storage-platforms` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Files](../../../WCP360_Core/src/Module/Files/README.md), [BackupRestore](../../../WCP360_Core/src/Module/BackupRestore/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `storage.zfs`, `storage.nfs`, `storage.smb` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Local ZFS/filesystem primitives remain Core-owned and free. The plugin gains no zfs shell, direct disks, arbitrary mount paths or unrestricted storage-appliance administration. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Select a TrueNAS product/API revision and map admitted pools, datasets, shares and tenant quotas to narrowly scoped provider credentials.
2. Separate snapshot, replication and share proposals from pool, disk, mount and encryption-key administration; qualify restore and key recovery independently.
3. Exercise partial replication, quota exhaustion and dataset dependency conflicts; destroying a dataset or snapshot is distinct from disconnecting a share and requires retained-data review.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
