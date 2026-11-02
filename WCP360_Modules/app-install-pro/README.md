# AppInstall Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.app-install-pro` |
| Category | `applications` |
| Core owners | Hosting, Data, Files, Operations, BackupRestore, Tenancy |
| Source catalogue keys | `apps.installer`, `apps.cms-recipes`, `apps.business-recipes`, `apps.verified-updates` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

A curated application installation product with complete versioned recipes: requirements, trusted upstream artifacts, compatible runtime/database combinations, scoped configuration, install, update, backup, restore and dependency-aware removal. The 410 editorial references are discovery input; each deliverable recipe requires independent qualification and upstream license review.

## Retained Core boundary

Core retains all 233 essential capabilities, application/resource ownership, ordinary hosting, local backup/recovery and the seven engine adapters. AppInstall Pro adds qualified optional application recipes and orchestration through detached extension contracts; it creates no privileged executor, legal compatibility decision or automatic installability for catalogue entries.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
