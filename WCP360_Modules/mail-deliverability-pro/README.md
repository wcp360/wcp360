# Mail Deliverability Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.mail-deliverability-pro` |
| Category | `mail` |
| Core owners | Mail, Hosting, Observability, Operations |
| Source catalogue keys | `mail.dmarc`, `mail.bimi`, `mail.smtp-relays`, `mail.mailguard` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced multi-domain deliverability reporting with DMARC aggregate analysis, qualified relay comparisons, reputation evidence and reviewed BIMI-related readiness checks. Reports distinguish observations from promises about message delivery.

## Retained Core boundary

Core retains SPF/DKIM/DMARC configuration checks, key rotation, basic delivery diagnostics, outbound abuse controls and local protection. This module adds historical external-report analytics and optional connector orchestration; it cannot sell or guarantee delivery.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
