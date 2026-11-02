# WCP360 premium business modules

This directory prepares **36 optional business-module proposals in 14 categories**. Every proposal is **design only**, **PRO**, **non-installable** and has **no selected source or licensing model**. There is no implementation, downloadable release, admitted provider or installation command here.

Read the [module catalogue](CATALOG.md) for the proposed scope and each package's acceptance work. Specific protocol/provider connectors belong in the [premium plugin catalogue](../WCP360_Plugins/premium/README.md); a business module and a connector use the same detached extension mechanism.

## Authoritative planning records

| Record | Responsibility |
| :--- | :--- |
| [CATALOG.json](CATALOG.json) | Authoritative collection of complete planning proposals; package IDs and categories are defined here. |
| [CATALOG.md](CATALOG.md) | Generated readable view of that collection. |
| Each package.proposal.json | Exact materialized copy of its catalogue entry, not a second registry. |
| Each package README and docs/ACCEPTANCE.md | Proposed behavior, retained Core boundary and package-specific acceptance. |
| [Planning schema](schemas/package-proposal.schema.json) | Closed proposal structure for module and plugin records; passing it grants no runtime authority. |
| [Authoring template](_template/README.md) | Future detached source layout, data/identity boundaries and implementation preparation. |

Update CATALOG.json first, then regenerate its Markdown view and identical per-package records/documents. Keep their IDs and fields synchronized. Categories are metadata, not extra package-directory levels. Paths derive from module.<slug> as WCP360_Modules/<slug>/.

The source reference is the unchanged [149-entry design catalogue](../PLUGIN_CATALOG.md). Its keys are documentary references, not package IDs, approvals, grants or compatibility results. The seven engine.* Core adapters are not premium packages and are not targeted by these proposals. The [410 App Deploy editorial references](../WCP360_App_Deploy/README.md) are a separate inventory, not 410 qualified recipes.

## Preserve the free Core

All **233 essential Core capabilities**, the thirteen Core owners and the seven essential engine-adapter entries stay in the open-source Core scope. Premium modules add advanced workflows, specialist orchestration or optional integrations; they do not gate ordinary hosting, login, optional MFA, quotas, files, hosted databases, baseline mail/security, local monitoring, local backup/restore, export or recovery.

Core remains the authority for resources, policy, permissions, jobs and admitted effects. A module runs separately with its own reviewed process, identity, data and eventual dependency graph. It cannot inject PHP/Twig/bundles into Core, write Core tables, receive a privileged socket or run arbitrary root commands.

PRO entitlement, package trust/compatibility and current action permission are separate checks. Expiry, provider outage, disablement or removal must preserve existing workloads and independent Core control/recovery. An optional connector failure cannot create a fallback privilege or silently repeat an uncertain effect.

## Licensing and qualification

The selected Core license is **AGPL-3.0-only**; consult [Core licensing](../WCP360_Core/LICENSING.md) and the [editions/premium strategy](../documentation/development/EDITIONS-AND-PREMIUM.md). This does not choose a premium-package license or establish that a particular linkage/distribution arrangement is legally compatible. Source availability, package terms, dependencies and applicable upstream obligations require explicit review before implementation/release.

No price, subscription duration, license-server endpoint or commercial enforcement protocol is defined here. Each proposal retains source_model: undecided. Dependencies are currently empty because no inter-package dependency is selected; the listed Core contracts and qualified providers are still prerequisites, not already available implementations.

A future package must provide qualified versions, provenance, upstream-license review, current-authorization checks, failure/recovery evidence and explicit exit behavior. AppInstall Pro requires complete independently qualified application recipes; a catalogue row alone must never enable installation.

Only static planning artifacts are supplied. No local runtime/test/compiler execution is implied by these documents.
