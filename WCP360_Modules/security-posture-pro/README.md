# Security Posture Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.security-posture-pro` |
| Category | `security` |
| Core owners | Platform, IdentityAccess, Observability, Operations |
| Source catalogue keys | `security.advisories`, `security.waf`, `security.file-integrity` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced cross-service posture review, exception tracking, evidence comparison and source-attributed policy recommendations. Optional scanners and advisory providers feed bounded findings rather than automatically changing server configuration.

## Retained Core boundary

All baseline security, hardening, secrets, tenant isolation and compliance controls required by the 233 capabilities remain free Core duties. This module adds comparative assessments and exception workflows; a PRO entitlement cannot relax the Core security floor.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
