# Docker managed operations adapter

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare optional fleet-oriented Docker inventory and reviewed workload operations through an admitted Core provider.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.docker-management-adapter-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `compute-platforms` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md), [Tenancy](../../../WCP360_Core/src/Module/Tenancy/README.md) |
| Existing catalogue keys | `compute.containers`, `compute.images`, `compute.rolling-operations` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Basic container installation, isolation, lifecycle and resource controls remain free Core capabilities. The plugin never receives a Docker socket, broad remote Docker API, broker channel or arbitrary Compose/shell execution. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify an admitted Core container-provider profile, node and tenant ownership; expose only reviewed operation inputs, immutable images and bounded observed state.
2. Qualify rootless and rootful daemon profiles separately with effective CPU, memory, process, storage and network limits. Reviewed in-container UID 0 initialization does not authorize privileged containers, host namespaces or unapproved devices, capabilities or mounts; refuse incompatible profiles.
3. Exercise rolling changes, failed pulls, partial restart and retained volumes; deletion of a container must not silently delete tenant data or bypass backup/recovery decisions.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
