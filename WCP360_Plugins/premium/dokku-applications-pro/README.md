# Dokku application interoperability

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare selected Dokku application inventory and a reviewed Core-mediated deployment interoperability profile.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.dokku-applications-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `application-platforms` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `apps.fleet-management`, `delivery.git`, `runtime.workers` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Dokku OSS local deployment behavior and Dokku Pro multi-server UI are distinct profiles. A plugin receives no SSH shell, arbitrary Dokku commands or direct Docker access. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify the exact upstream distribution, transport and capability profile; do not infer multi-server orchestration from OSS local scheduling or optional Pro interfaces.
2. Keep initial import/observation read-only and route any future structured application operation through a bounded admitted Core adapter with pinned host identity and app ownership.
3. Exercise deployment failure, worker interruption, secret redaction and retained-data recovery; never install upstream plugins, execute arbitrary hooks or take over active Core resources.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

OSS local scheduler/SSH behavior and the separately documented Pro multi-server UI are different upstream profiles. Neither establishes a WCP360 multi-node scheduler, safe generic SSH bridge or automatic cluster support.

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [Dokku remote commands](https://dokku.com/docs/deployment/remote-commands/)
- [Dokku scheduler management](https://dokku.com/docs/deployment/schedulers/scheduler-management/)
- [Dokku Pro multiple servers](https://pro.dokku.com/docs/features/multiple-servers/)
