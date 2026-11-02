# Enhance inventory and migration interoperability

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare scoped Enhance API inventory and migration planning for explicitly authorized organizations, websites and related owned resources.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.enhance-interop-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `migration-data` |
| Core owners | [Transfers](../../../WCP360_Core/src/Module/Transfers/README.md), [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Tenancy](../../../WCP360_Core/src/Module/Tenancy/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `apps.fleet-management`, `backup.transfers` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Enhance remains an external source platform and architecture comparison point, not an imported WCP360 engine or second active controller for Core-managed services. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify a concrete upstream API/schema profile and read-only organization/resource permissions; use explicit source ownership and bounded redacted inventory rather than a generic remote admin proxy.
2. Map supported website, domain, database, mail and backup objects into a dry-run import plan, recording incompatible settings and unresolved secrets instead of claiming universal lossless conversion.
3. Rehearse isolated restore and current-authority checks before explicit cutover/handoff; retain source data and rollback evidence, and refuse simultaneous management of the same host, ingress or database by both panels.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [Enhance API documentation](https://apidocs.enhance.com/)
- [Enhance published OpenAPI contract](https://apidocs.enhance.com/spec/oas3-api.yaml)
