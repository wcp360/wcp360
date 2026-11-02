# Galera cluster observation connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare a selected Galera-capable database cluster profile for bounded health, quorum and recovery observations.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.galera-cluster-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `database-services` |
| Core owners | [Data](../../../WCP360_Core/src/Module/Data/README.md), [BackupRestore](../../../WCP360_Core/src/Module/BackupRestore/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md) |
| Existing catalogue keys | `data.replication`, `data.external-databases` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

A clustered database does not guarantee uninterrupted service or safe automatic failover. Core retains cluster ownership and recovery authority; no plugin bootstrap, arbitrary SQL or host command is admitted. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify the exact engine/Galera/provider topology, credentials, observed members and quorum/replication states without assuming interchangeable MySQL/MariaDB distributions.
2. Keep initial health and flow-control observations read-only; treat partition, stale state and lost quorum as recovery decisions rather than automatically forcing a primary component.
3. Rehearse isolated consistency, state-transfer, backup and whole-cluster recovery scenarios; never bootstrap both sides of a partition or retry an uncertain destructive recovery.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [MariaDB Galera quorum and recovery](https://mariadb.com/docs/galera-cluster/high-availability/understanding-quorum-monitoring-and-recovery)
