# Local licensing and commercial entitlements

**Status: design reservation, recorded 2026-11-02. No local license client, production license protocol or entitlement engine is implemented here.**

[Tenancy](../../README.md) remains the owner of account/tenant plans, subscriptions, entitlements and resource ceilings. This location prepares that existing responsibility; it adds no fourteenth Core module, canonical contract, API identifier, database table or runtime feature. Use the [Core structure](../../../../../STRUCTURE.md) and [capability map](../../../../../CAPABILITY-MAP.md#installation-updates-and-panel-autonomy) for navigation.

## Scope and authority

The intended local client inspects commercial rights for an installed product or optional package and exposes a bounded, redacted result to its authorized consumers. A central license issuer, billing service, checkout and pricing policy remain separate from the installed Core. This reservation selects no vendor, edition, price, trial duration, activation endpoint or online dependency.

| Decision | Existing owner | Boundary |
| :--- | :--- | :--- |
| Commercial entitlement evidence and effective product rights | Tenancy | A commercial right does not grant an actor permission on a tenant or resource. |
| Authentication, scope, current permissions and reauthentication | [IdentityAccess](../../../IdentityAccess/README.md) | Reevaluate current actor/resource authority independently of license status. |
| Package identity, publisher trust, compatibility and lifecycle | [ExtensionManagement](../../../ExtensionManagement/design/packages/README.md) | A licensed product is not automatically an admissible or installed package. |
| Installation, repair, update and supported recovery | [Setup](../../../Setup/README.md) | Consume accepted rights information without making essential recovery depend on the vendor being reachable. |
| Durable refresh/activation work and uncertain outcomes | [Operations](../../../Operations/README.md) | Reuse bounded jobs, idempotency, audit and reconciliation; introduce no second task engine. |
| Hosted site, data, mail or other resource lifecycle | Its existing Core resource owner | License checks cannot transfer ownership or authorize arbitrary system effects. |

License evidence, account entitlements, IAM authorization, package signature verification and installation approval are distinct. A valid package signature authenticates admitted package bytes under its own trust policy; it does not prove a subscription. Conversely, a commercial entitlement does not make an unknown publisher or package safe.

## Signed claims and protocol decisions

A local verifier must authenticate the issuer and the exact accepted claims under a separately reviewed product contract. Decoding a token, finding a signature or receiving an HTTP success is insufficient evidence of commercial rights. JWT is a token format, not a licensing policy: a generic access/session JWT must not become a license merely because a shared token library accepts it.

This reservation chooses no cryptographic algorithm, JWT/JWS format, key source, signing implementation or new token schema. Issuer trust, key distribution/rotation/revocation, accepted algorithms and claims, product/version binding, optional node/account binding, freshness/replay handling and storage must be settled through the [selected contract process](../../../../../../documentation/contracts/README.md). Private signing keys never belong in the installed client.

Keep license secrets and activation material outside Git, generated previews, routine audit, support bundles and AI prompts. Show only the minimum product/right/status metadata authorized for the current surface. Audit the decision and its reference without copying the proof or secret.

## Offline operation, uncertainty and continuity

An unreachable vendor, unconfigured client, invalid proof and expired entitlement are different observations. Preserve the last verified evidence and its age where policy allows, while making uncertainty visible. Never silently turn a missing/unknown result into an active commercial right or invent an expiry.

Offline proof acceptance, refresh intervals, clock-change handling, revocation freshness, bounded grace, transfer and recovery remain unresolved commercial-policy decisions. No grace duration or offline-validity promise is introduced here. A feature requiring unresolved commercial admission stays unavailable for new premium work; that uncertainty does not shut down the essential Core.

The free Core must remain operable with zero optional plugins and no permanent licensing connection. Vendor outage, subscription expiry or trial expiry must not delete customer data, suspend essential hosted services, block local administration/security or remove baseline backup/recovery access. Any restriction on a premium operation requires an explicit product policy and a safe lifecycle decision. A refresh failure must not blindly cancel an operation midway through an irreversible effect.

## Package and resource lifecycle

Use the shared [package design](../../../ExtensionManagement/design/packages/README.md) and [extension resource index](../../../../../resources/extensions/README.md). Keep optional premium implementation and dependencies in independently versioned plugin packages; do not add them to the Core Composer graph or load arbitrary plugin PHP into Symfony.

Define expiry, downgrade, disablement and removal against dependent resources before exposing those actions. Preserve Core-readable inventory and data. Refuse removal of an essential active adapter until an accepted replacement or ownership transfer exists. Paid status never permits a plugin to purge hosted resources or broaden its capabilities.

## Acceptance to define before implementation

| Boundary | Required refusal or recovery evidence |
| :--- | :--- |
| Proof admission | Reject malformed/oversized proofs, unsupported formats, unknown/revoked issuer keys, altered signatures, replay/stale evidence and required product/account/node binding mismatches. |
| Authorization | A valid commercial right cannot authorize another tenant, an inactive actor, a missing permission or an unapproved package. |
| Time and offline state | Exercise clock changes, refresh loss, restart, stale cache, transfer conflicts and expiry under the approved policy; unknown remains distinguishable from valid/expired. |
| Continuity | Vendor outage and trial/subscription expiry preserve essential hosting, local administration, security, retained data and baseline recovery. |
| Lifecycle | Concurrent refresh/disable/update, revoked rights during queued work and lost acknowledgements reconcile without unsafe replay or data deletion. |
| Privacy | Proofs, keys and activation material do not appear in errors, logs, exports, audit payloads, support bundles or AI input. |

These are future acceptance requirements, not executed results. This directory supplies no verifier, credentials, service, dependency or completion credit.