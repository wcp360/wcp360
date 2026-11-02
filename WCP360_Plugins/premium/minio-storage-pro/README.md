# MinIO object-storage connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare a selected MinIO deployment profile for owned object data and remote backup destinations.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.minio-storage-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `storage-platforms` |
| Core owners | [BackupRestore](../../../WCP360_Core/src/Module/BackupRestore/README.md), [Files](../../../WCP360_Core/src/Module/Files/README.md), [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `storage.object-storage`, `backup.s3`, `backup.immutable` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

The proposal does not select a MinIO edition, license or supported API revision. Provider administration, host storage and local recovery stay outside plugin authority. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify the exact product/distribution, API profile and licensing obligations before mapping an admitted tenant, bucket and prefix.
2. Verify integrity, encryption, quota and any versioning/retention capabilities on the selected deployment; do not promise identical S3 or immutability semantics.
3. Exercise unavailable credentials, incomplete uploads and clean-target restores; preserve retained data on disable/uninstall and require explicit authorization for bucket or object purge.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
