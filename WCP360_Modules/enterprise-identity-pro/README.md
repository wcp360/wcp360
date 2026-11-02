# Enterprise Identity Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.enterprise-identity-pro` |
| Category | `identity` |
| Core owners | IdentityAccess, Tenancy, Operations, ExtensionManagement |
| Source catalogue keys | `identity.oidc`, `identity.saml`, `identity.ldap`, `identity.scim` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced federation and provisioning orchestration with reviewed organizational mappings, synchronization plans and identity-source change tracking. OIDC/SAML/directory/SCIM protocol adapters remain separate qualified connectors.

## Retained Core boundary

Local authentication, optional MFA, recovery, permissions and tenant isolation remain Core. This module adds organization-scale workflows and never converts an external claim, group or premium entitlement into authority above current Core policy.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
