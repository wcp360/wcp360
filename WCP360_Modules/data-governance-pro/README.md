# Data Governance Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.data-governance-pro` |
| Category | `governance` |
| Core owners | IdentityAccess, Tenancy, Files, Data, Mail, Observability, Operations |
| Source catalogue keys | `governance.data-requests`, `governance.erasure`, `governance.retention-holds`, `governance.residency` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced multi-resource privacy-request cases with discovery scope, approvals, redacted exports, retention holds and coordinated deletion proposals. The owning resource module still determines each supported operation and its recoverability.

## Retained Core boundary

Core keeps essential access control, lawful retention configuration, resource export/deletion and data minimization. Governance adds case coordination; it does not override holds, policy, tenant ownership or the permissions needed for each effect.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
