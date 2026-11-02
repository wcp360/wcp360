# Diagnostic Toolbox Pro

**Planning proposal: design / PRO / installable: false / source model: undecided.** No runtime package or premium license is delivered.

| Field | Proposed value |
| :--- | :--- |
| Planning ID | `module.diagnostic-toolbox-pro` |
| Category | `automation` |
| Core owners | Observability, Platform, Hosting, Data, Mail, Operations |
| Source catalogue keys | `automation.playbooks`, `support.reports`, `security.forensics`, `observe.synthetics` |
| Selected package dependencies | None; Core contracts and qualified providers remain prerequisites. |

## Proposed optional scope

Advanced guided diagnostic cases combining named bounded checks, evidence comparison and reviewed repair proposals across service owners. It packages reusable investigation runbooks rather than exposing a terminal.

## Retained Core boundary

Core retains baseline diagnostics, local checks, safe error reporting and ordinary repair operations. The Toolbox adds optional guided workflows; developer tools, arbitrary shell/SQL execution and root process control are outside its product interface.

PRO entitlement is separate from package trust/compatibility and current Core permissions. Hosted services and independent Core control/recovery must survive expiry, provider failure, disablement and removal.

## Before implementation

Follow the [package acceptance](docs/ACCEPTANCE.md), [exact planning record](package.proposal.json) and [authoring template](../_template/README.md). The [machine catalogue](../CATALOG.json) is authoritative; this record is its materialized entry. Existing keys refer to the unchanged [149-entry design catalogue](../../PLUGIN_CATALOG.md), not installable packages or grants.

The [Core AGPL-3.0-only decision](../../WCP360_Core/LICENSING.md) does not select this package's license or establish a legal compatibility conclusion. Review package source terms, dependencies and upstream rights under the [editions strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) before distributing an implementation. No price, duration or licensing endpoint is defined.
