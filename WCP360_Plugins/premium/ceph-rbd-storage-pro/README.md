# Ceph RBD block-storage connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare block-image-specific inventory and reviewed attachment/snapshot integration for explicitly owned Ceph RBD resources.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.ceph-rbd-storage-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `storage-platforms` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [BackupRestore](../../../WCP360_Core/src/Module/BackupRestore/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md), [Tenancy](../../../WCP360_Core/src/Module/Tenancy/README.md) |
| Existing catalogue keys | `storage.ceph`, `compute.images` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

An RBD image is not a CephFS share or RGW/S3 object. Core controls block-device mappings, guest ownership, fencing and recovery; no host device or cluster-admin interface reaches the plugin. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify a selected image/pool/namespace/client and Core-provider profile with storage capabilities, guest ownership and explicitly supported image features.
2. Validate active consumers, cooperative versus exclusive access and fencing before attachment or recovery; an exclusive-lock feature is not assumed to make arbitrary multi-writer filesystem use safe.
3. Exercise stale clients, interrupted snapshots and ambiguous detach; never break locks, blocklist clients, discard images or purge retained snapshots without separately admitted authority and recovery evidence.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [Ceph RBD exclusive locks and fencing](https://docs.ceph.com/en/squid/rbd/rbd-exclusive-locks/)
