# BitNinja defensive interoperability

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare scoped BitNinja observations and reviewed defensive decision interoperability using a selected provider API profile.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.bitninja-defense-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `security-defense` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `security.threat-intel`, `security.siem` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Baseline firewall, WAF and Core security remain free. Product credentials, account reputation and provider claims never become Core authorization or a promise of universal immediate blocking. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify the exact provider API, account/server scope and credential capabilities; begin with redacted observations and explicit sharing consent.
2. Keep detection, account/global challenge, allow and block states distinct; stage effects through an admitted owner with trusted client-IP provenance, expiry, exceptions and operator approval.
3. Record observed, admitted, applied, failed or unknown state per node; exercise delayed propagation, partitions, false positives, revocation and local lockout recovery without live customer attacks.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [BitNinja REST API documentation](https://doc.bitninja.io/docs/rest%20api/apiv2/)
- [BitNinja IP reputation model](https://docs.bitninja.io/docs/ip%20filter/ip_reputation/)
