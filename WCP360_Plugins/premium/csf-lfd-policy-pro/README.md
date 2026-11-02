# CSF and lfd policy evidence connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare CSF/lfd evidence import and reviewed clustering-policy interoperability without replacing free Core firewall control.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.csf-lfd-policy-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `security-defense` |
| Core owners | [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `security.threat-intel`, `security.siem`, `automation.event-rules` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

This is optional evidence/coordination, not a paid native firewall driver. No upstream fork is selected, no separate official ClusterCSF product is assumed and no CSF/root command reaches the plugin. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Record source/fork provenance, maintenance policy, artifact integrity and exact supported platform before any qualification; the cPanel-maintained fork is evidence of a distinct source, not automatic WCP360 compatibility.
2. Start with bounded redacted observations/exports; qualify any lfd peer/clustering profile, credentials, fan-out scope and conflicting-writer refusal separately through Core.
3. Exercise stale peer decisions, replay, partitions, expiry, false positives and operator lockout recovery; never propagate every detection as an immediate global block or change update sources automatically.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

The original ConfigServer publisher closed in 2025. cPanel announced a distinct maintenance fork in 2026 focused on critical security and stability fixes, with separate support limits. No fork or package is selected for WCP360. The documented CSF/lfd clustering feature is not treated here as proof of a separate official product called ClusterCSF.

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [cPanel CSF fork maintenance announcement](https://support.cpanel.net/hc/en-us/articles/37654028162071-cPanel-will-provide-its-own-fork-of-CSF-starting-Feb-25th-2026)
- [CSF/lfd upstream fork documentation](https://github.com/cpanel/cpanel-csf/blob/main/etc/readme.txt)
