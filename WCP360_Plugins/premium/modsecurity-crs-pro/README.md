# ModSecurity CRS managed-policy connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare reviewed OWASP CRS policy provenance, staged changes and defensive findings for an admitted ModSecurity integration.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.modsecurity-crs-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `security-defense` |
| Core owners | [Hosting](../../../WCP360_Core/src/Module/Hosting/README.md), [Platform](../../../WCP360_Core/src/Module/Platform/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `security.waf`, `security.advisories` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

The native WAF/web-server integration and essential security controls remain Core-owned and free. The plugin does not load arbitrary native modules, rules, code or host configuration. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify an admitted Core engine/binding and CRS policy profile with immutable rule provenance, compatibility evidence and explicit site ownership.
2. Exercise detection-first rollout, body/resource limits, application false positives, exceptions, rollback and protected-route behavior before approving blocking changes.
3. Treat findings and remote rules as untrusted; never perform active attacks on customer sites or claim L7 rules prevent an upstream network-capacity DDoS.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
