# Working on WCP360 Core

Use this page to resume development, locate the authoritative description of a concern and finish a bounded change without creating competing inventories.

**Start with [current status](STATUS.md), then [delivery order](ROADMAP.md).** Before changing source or adding/executing CI work, verify the active implementation gate in the [baseline manifest](../documentation/manifests/WCP360-BM-0001.md) and [contribution rules](../CONTRIBUTING.md). A status checkpoint, roadmap stage, existing scaffold, or user goal does not override a paused gate or authorize an implementation slice. When implementation is paused, continue only explicitly permitted documentation/governance work until MASTER selects and authorizes a slice. The [public project overview](../README_GITHUB.md) describes the product scope. Core is the built-in engine of WCP360 Standalone; essential hosting and local recovery must work without optional plugins or a vendor connection.

## Resume in five steps

1. Read [STATUS.md](STATUS.md) for the last verified executable revision, its CI scope, open qualification boundaries and the next candidate checkpoint; verify the active implementation gate in the [baseline manifest](../documentation/manifests/WCP360-BM-0001.md).
2. Read the relevant milestone in [ROADMAP.md](ROADMAP.md) for ordering and acceptance only. A candidate checkpoint, helper, CI scaffold or folder is not implementation authority and does not close a production milestone.
3. Find the owner in [NAVIGATION.md](NAVIGATION.md), then read that module's README and concrete source guide. Use [STRUCTURE.md](STRUCTURE.md) for cross-module growth boundaries.
4. Check applicable specifications and current product decisions. Keep the existing branch and the authorized scope. Development is consolidated on `main` through [PR #116](https://github.com/wcp360/wcp360/pull/116) and [PR #125](https://github.com/wcp360/wcp360/pull/125). Follow the [completed branch register](../documentation/governance/branches/BRANCH-COMPLETION-2026-11-03.md); do not create another branch as an organizational shortcut.
5. Implement one complete bounded slice with validation/refusal behavior, update its owning documents and record exact-source evidence before changing its delivery status.

## Which document owns which answer?

| Question | Maintained source | Update it when |
| :--- | :--- | :--- |
| What is WCP360, who uses it and what is the target stack? | [Public overview](../README_GITHUB.md), with the dated root workspace README kept as historical context | Product scope, target choices or public entry points change. |
| What has actually been verified, and what comes next? | [STATUS.md](STATUS.md) | A new executable checkpoint is verified or the selected next increment changes. |
| What is the dependency order and acceptance for the complete Core? | [ROADMAP.md](ROADMAP.md) | Milestone scope, dependencies or evidenced maturity change. |
| Which of the 233 capabilities is implemented? | [CAPABILITY-MAP.md](CAPABILITY-MAP.md) | The owning behavior and its evidence justify a status/detail change. |
| Where do I edit a feature? | [NAVIGATION.md](NAVIGATION.md) | Source ownership or a public entry point changes. |
| Where do modules, resources and future integrations belong? | [STRUCTURE.md](STRUCTURE.md), [module index](src/Module/README.md) | A boundary or actual source location changes. |
| What resources should be implemented first? | [Resource roadmap](resources/ROADMAP.md) | Cross-family dependencies change. |
| What exists for an individual Linux service? | [Service register](resources/services/ROADMAP.md) and its linked service README | A template, provider, validator, health check or controlled operation is added. |
| Which commands exist and who may use them? | [Command inventory](bin/COMMANDS.md) | A domain command or context restriction changes. |
| What belongs to Community or PRO, and under which license? | [Edition strategy](../documentation/development/EDITIONS-AND-PREMIUM.md), [infrastructure coverage](../documentation/development/INFRASTRUCTURE-PREMIUM-COVERAGE.md), [Core licensing](LICENSING.md) and the two linked package catalogues | Edition boundaries, package proposals or an explicitly authorized license decision change; planning metadata does not enable a package. |
| How are reseller plans and named platform integrations organized? | [Tenancy reseller design](src/Module/Tenancy/RESELLER-PACKAGES.md), [eleven-area integration register](../documentation/development/PLATFORM-INTEGRATIONS-COVERAGE.md) | Plan/override acceptance or optional provider scope changes; design guides do not implement quotas or qualify integrations. |
| Where are dependencies defined? | [DEPENDENCIES.md](DEPENDENCIES.md), real manifests and locks | A component's actual dependency graph changes. |
| How is a candidate run and tested? | [Run guide](../documentation/development/EXECUTABLE-CANDIDATE.md), [testing](../documentation/development/TESTING.md), [quality stack](../documentation/development/QUALITY-SECURITY-STACK.md) | Supported commands or verification environments change. |
| Which contracts and security constraints apply? | [Baseline manifest](../documentation/manifests/WCP360-BM-0001.md), selected owner specifications and [security policy](../SECURITY.md) | Follow their existing authority and review process; this guide does not amend them. |

Domain guides own detail. STATUS links to those guides rather than copying their schemas, fixtures or complete acceptance lists. The roadmap owns ordering; the capability map owns the 233-row register. Neither this guide nor a new directory creates another registry.

Use the [production-readiness review](../documentation/development/PRODUCTION-READINESS-REVIEW.md) to review cross-cutting gaps in watchdogs, updates, isolation, sessions, hosting pages, notifications and recovery. It links existing owners and qualification criteria; it does not replace the capability register, accept contracts or qualify the candidate.

## Source locations

| Work | Existing location and boundary |
| :--- | :--- |
| Business invariants and types | `src/Module/<Owner>/Domain/` |
| Use cases, queries and typed ports | `src/Module/<Owner>/Application/` |
| Persistence and provider implementations | `src/Module/<Owner>/Infrastructure/` |
| HTTP/API, CLI and form adapters | `src/Module/<Owner>/UI/Http/`, `UI/Console/`, `UI/Form/`; call the same authorized use cases. |
| Reusable views and browser behavior | `templates/`, `assets/`, `translations/`; no parallel `presentation/` tree. |
| Symfony application wiring | `config/` |
| Linux profiles and templates | [resources/services](resources/services/README.md); retain existing canonical Go candidate templates until an explicit handoff. |
| System agent and privileged broker | [agents](agents/README.md); separate Go modules and approved typed operations. |
| Application metadata and authored recipes | [App Deploy](../WCP360_App_Deploy/README.md), through the [Hosting boundary](src/Module/Hosting/APPLICATION-CATALOGUE.md). |
| Optional business modules | [WCP360_Modules](../WCP360_Modules/README.md); separate package source and dependencies, not additional internal Core owners. |
| Plugins and integration SDK | Separate [WCP360_Plugins](../WCP360_Plugins/README.md) and `WCP360_SDK/` graphs; PRO connector proposals under plugins/premium; no plugin PHP inserted into Core's container or vendor. |
| Generated libraries and state | Ignored component-local `vendor/`, module caches and `var/`; production secrets and applied configuration stay outside source. |
| Delivery records | [WCP360_Releases](../WCP360_Releases/README.md), after actual qualification. |

The thirteen existing owners cover the current needs. Docker belongs to Platform with Hosting/Operations; local commercial rights belong to Tenancy; package admission belongs to ExtensionManagement; Toolbox belongs to Observability; optional AI/model/MCP integrations reuse the documented Platform/Operations/IAM boundaries. Do not add duplicate Docker, Premium, Licensing or global Functions modules merely to match a technology name.

A resource directory is not a provider. Add actual Domain/Application/Infrastructure/UI or profiles/templates files with their consuming implementation, owned data, identities, bounded operations, failure handling and tests. Do not create empty trees for all catalogue entries.

## Read delivery statements consistently

| Statement | What it establishes |
| :--- | :--- |
| Target or design reservation | Requirement and owner/location; no installed engine or working lifecycle. |
| Implemented candidate | Source for a stated development scope; production contracts or qualification can remain open. |
| Passed check | Its exact assertions, source and environment succeeded. |
| Provider available | Requires admitted artifact/version/target, owned configuration, identities, operations and recovery evidence; a template alone is insufficient. |
| Production qualified | Requires the applicable complete acceptance and release gates; currently not achieved. |

Record verification as `passed`, `failed`, `not_run`, `not_configured` or `not_applicable`, with the reason and exact source. Unknown observations are not inactive services, successful installation or verified health. A parent workflow does not qualify later runtime changes.

## Keep documentation coherent after a change

1. Update the owning module/service guide with actual behavior and remaining limits.
2. Update NAVIGATION only for location changes, the service/resource register for affected providers and CAPABILITY-MAP only for justified capability changes.
3. Update ROADMAP when ordering, acceptance or evidenced progress changes; a helper or README creates no percentage credit.
4. Replace STATUS's checkpoint when the new executable evidence is available, retaining the source/run links and any still-open qualification.
5. Update the public overview when product-facing behavior or requirements change. Keep the 233 capabilities and 149 design entries in their existing registers; App Deploy's 410 editorial references are a separate inventory.
6. Check changed Markdown file links/anchors, preserved requirements and source hashes. Documentation-only changes need appropriate static checks; they do not require starting local services.

Local executable tests remain stopped under the current working instruction. Use the existing remote CI for executable verification; do not infer permission to restart Windows previews or run PHP, Go, browsers, databases or service processes from a documented command.

## Decision and history boundaries

Current product decisions and unresolved production-contract integration are summarized in [STATUS.md](STATUS.md#current-product-decisions). The owner confirmed on November 3, 2026 that administrator MFA is optional per administrator, off by default and enforced for an account once enabled. The frozen canonical baseline still contains mandatory-ADMIN-MFA wording: this known mismatch requires explicit M1 contract review, not silent application of the older requirement or a claim of production acceptance. This guide does not amend that artifact.

The same dated [edition decision](../documentation/development/EDITIONS-AND-PREMIUM.md) gives Free/Community unlimited websites with the essential Core and no premium packages; PRO also has unlimited websites and includes all WCP360 premium modules/connectors after their individual release, admission and qualification. Do not reinstate the earlier 30-site proposal or confuse an edition limit with host capacity, hosting-plan quotas or implementation maturity.

The [owner-decision register](../documentation/governance/decisions/OWNER-DECISION-FREE-SITES-MFA-2026-11-03.md) records these product choices and the M1 contract review still required for the frozen mandatory-MFA wording.

Historical branch/source inventories stay dated. The active candidate, canonical documentary baseline and an eventual production release are separate identities. Preserve imported sources and unrelated working-tree edits; never publish them accidentally with a documentation or Core change.
