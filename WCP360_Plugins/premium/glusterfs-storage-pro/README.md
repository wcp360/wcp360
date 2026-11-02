# GlusterFS storage interoperability

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare bounded observations of admitted GlusterFS volumes, peers and recovery state through a Core-owned storage provider.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.glusterfs-storage-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `storage-platforms` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Files](../../../WCP360_Core/src/Module/Files/README.md), [BackupRestore](../../../WCP360_Core/src/Module/BackupRestore/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `storage.glusterfs` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

No gluster shell, arbitrary brick path or cluster-wide root authority is delegated. Basic local storage stays free; distributed storage is not treated as a generic object bucket. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify release maintenance, artifact provenance and an exact Core provider profile before claiming a supported host/API matrix; record peer, volume, brick and tenant ownership.
2. Observe quorum, health and pending/split-brain healing separately from effects; never auto-select an authoritative copy, force healing or detach a peer from a plugin decision.
3. Exercise network partitions, full volumes, mount dependencies and interrupted recovery; preserve source data and require separate backup/recovery review before removal or destructive repair.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

Upstream administration and release pages are evidence inputs. Published documentation and a historical release table do not establish a currently qualified WCP360 package, maintenance commitment or supported OS matrix.

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [Gluster administration guide](https://docs.gluster.org/en/latest/Administrator-Guide/)
- [Gluster split-brain handling](https://docs.gluster.org/en/latest/Administrator-Guide/Split-brain-and-ways-to-deal-with-it/)
- [Gluster published release schedule](https://www.gluster.org/release-schedule/)
