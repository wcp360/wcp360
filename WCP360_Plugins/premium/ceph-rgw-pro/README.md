# Ceph RGW object-storage connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare tenant-scoped Ceph object-gateway destinations for reviewed application objects and remote backup transfers.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.ceph-rgw-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `storage-platforms` |
| Core owners | [BackupRestore](../../../WCP360_Core/src/Module/BackupRestore/README.md), [Files](../../../WCP360_Core/src/Module/Files/README.md), [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `storage.ceph`, `storage.object-storage`, `backup.s3` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

This proposal targets an admitted RGW profile, not unrestricted Ceph cluster administration, raw RADOS access, disks or host commands. Local storage and recovery remain free Core. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify a selected gateway/API profile with explicit tenant, account, bucket, prefix and credential ownership; do not infer complete behavior from an S3-compatible label.
2. Verify multipart integrity, versioning, encryption-key recovery and any retention/object-lock capability individually against the selected deployment.
3. Exercise interrupted transfer, quota exhaustion and retained-backup restore; bucket/object deletion and data purge require separate authorization and cannot follow connector removal automatically.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
