# Brevo transactional email connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare an optional Brevo transactional sender/relay profile with scoped delivery feedback and deliberate route changes.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.brevo-email-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `collaboration-support` |
| Core owners | [Mail](../../../WCP360_Core/src/Module/Mail/README.md), [Observability](../../../WCP360_Core/src/Module/Observability/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md), [IdentityAccess](../../../WCP360_Core/src/Module/IdentityAccess/README.md) |
| Existing catalogue keys | `mail.smtp-relays` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Local email, baseline alert delivery and Core mail administration stay free. The connector does not export mailboxes, route arbitrary customer content or silently replace a working local recovery channel. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify an admitted API or SMTP profile, verified sender/domain ownership, scoped credentials, consent and bounded recipient/data policies.
2. Authenticate and bind any delivery feedback under a reviewed webhook profile; distinguish request acceptance, delivery, bounce and suppression without guaranteeing deliverability.
3. Exercise provider failure, duplicates, redaction and credential rotation; removal requires an explicit safe route transition and preserved local alerts, not automatic fallback that resends private data.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [Brevo transactional email API](https://developers.brevo.com/docs/send-a-transactional-email)
- [Brevo secured webhooks](https://developers.brevo.com/docs/secured-webhooks)
