# AWX multi-server workflow connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare selected AWX controller inventory and reviewed job-template references for explicitly authorized multi-server workflows.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.awx-controller-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `multi-server-automation` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md), [Tenancy](../../../WCP360_Core/src/Module/Tenancy/README.md) |
| Existing catalogue keys | `automation.ansible`, `compute.node-groups`, `compute.rolling-operations` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Core owns hosting admission, local execution, leases and recovery. The plugin gains no SSH root channel, arbitrary playbook/extra-vars execution or direct privileged broker access. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify one controller/API profile with admitted organization, inventory, credential references and immutable reviewed job-template inputs; do not accept arbitrary playbooks or target expressions.
2. Bind targets to current node/tenant authority and require approval for provider-side effects; WCP host mutations still require admitted Core use cases rather than controller credentials.
3. Exercise staged concurrency, cancellation, stale inventory, partial node completion and lost acknowledgements; preserve workload service and reconcile observed state before retry.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
