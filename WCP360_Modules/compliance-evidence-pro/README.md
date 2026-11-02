# Compliance Evidence Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.compliance-evidence-pro` |
| Category | `governance` |
| Core owners | Observability, IdentityAccess, Platform, Operations |
| Source catalogue keys | `governance.audit-archive`, `support.reports`, `security.advisories` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced evidence cases, control mappings, review assignments and reproducible exports for internal or external assessments. Reports state their sources and limitations and do not claim regulatory certification from a checklist.

## Retained Core boundary

Core retains baseline compliance, security controls, audit integrity, retention and authorized export. This module organizes optional assessment evidence; it cannot change mandatory policy, create a second authoritative audit history or declare legal compliance automatically.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
