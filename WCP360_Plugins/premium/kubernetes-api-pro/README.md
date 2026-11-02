# Kubernetes API connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare scoped Kubernetes inventory and reviewed application lifecycle requests for selected clusters and namespaces.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.kubernetes-api-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `compute-platforms` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md), [Tenancy](../../../WCP360_Core/src/Module/Tenancy/README.md) |
| Existing catalogue keys | `compute.kubernetes`, `compute.node-groups`, `compute.rolling-operations` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Core owns current authorization and admission. No generic kubeconfig, arbitrary resource YAML, pod exec or cluster-admin capability is delegated to the plugin. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify a selected API revision and admission profile with exact cluster, namespace, service-account and verb/resource RBAC; separately approve every required cluster-scoped action.
2. Reject cross-namespace objects, privilege escalation, unsafe workload identities and unapproved host/network mounts; qualify resource quotas, secrets and network isolation without assuming namespace RBAC alone provides tenant isolation.
3. Exercise rollout interruption, disconnected clusters, storage claims and partial reconciliation; preserve persistent data and avoid claiming a Kubernetes cluster or every OS is supported before qualification.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
