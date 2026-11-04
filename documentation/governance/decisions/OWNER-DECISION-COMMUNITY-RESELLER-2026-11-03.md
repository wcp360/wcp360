# Owner product decision: reseller scope outside Community

**Recorded: 2026-11-03. Authority: explicit project-owner instruction during the completeness review. Scope: target product packaging and documentation; no runtime, licensing or canonical-contract change.**

## Purpose and decision

The owner has stated that the open-source version does not need reseller functionality. **Reseller management and reseller-specific delegation are outside the Free/Community product target.** Earlier guides that described reseller package management as a required free Core feature are superseded on this edition boundary.

A separate reseller commercial workflow may be considered later. It is not available or qualified today. Its inclusion in PRO, separate offer, package identity, source model and licence have not been selected by this decision; do not present it as an included or installable PRO package.

## Foundations retained in Core

Free/Community retains the essential hosting foundation: account and tenant identity, customer workspaces, resource ownership, IAM, tenant and workload isolation, ordinary resource plans and enforceable quotas, safe lifecycle administration, audit, baseline alerts, backup and local recovery. Removing a reseller sales/delegation surface must not remove those controls or place essential hosting and retained customer data behind a commercial dependency.

The existing thirteen owners remain. Tenancy owns essential account/tenant policy, ordinary resource assignments and quotas. IdentityAccess owns current actor authority and scope. Hosting, Mail, Data and Files keep resource ownership; Operations and Platform keep controlled effects and capacity boundaries. Any future reseller workflow must use these accepted owners, APIs and effect controls rather than gain direct Core-table access or create another authorization engine.

The distinction concerns reseller portfolios, reseller-specific hierarchical package sales, child-plan administration and delegated reseller workflows. An ordinary hosting plan, quota or customer workspace is not automatically a reseller feature.

## Preserved decisions and authorities

- The [Free/unlimited and optional administrator MFA decision](OWNER-DECISION-FREE-SITES-MFA-2026-11-03.md) remains unchanged: no edition-imposed website-count limit; MFA optional per administrator and enforced once enabled.
- The original Core source remains [AGPL-3.0-only under its existing scoped policy](../../../WCP360_Core/LICENSING.md). Product packaging does not amend the licence text, withdraw granted rights or create a proprietary exception.
- Preserve the [233-row capability inventory](../../../WCP360_Core/CAPABILITY-MAP.md), its labels, statuses and historical counts. This retained inventory contains earlier scope; it is not a promise that all 233 rows belong to Community after this decision.
- Preserve existing source/specifications, imported pages, routes, canonical roles, permission identifiers, contracts, frozen exports and evidence. Reseller-specific production contracts require explicit scope review before implementation; no identifier is removed or repurposed here.
- Existing candidate behaviour, accounts, grants, credentials, entitlements, locks, catalogue JSON and customer data are unchanged. No package, migration, service or Core test is executed by this decision.

## Reconciliation and acceptance

| Concern | Required result |
| :--- | :--- |
| Current edition guides | State that reseller functionality is outside Community; distinguish any optional future commercial placement from available PRO packages. |
| Retained reseller design | Keep complete specifications and refusal requirements, but clearly mark the old free-Core reseller inclusion statement as superseded. Reuse essential account/quota controls independently. |
| Capability map and roadmap | Retain counts, statuses and dated assessment; link this scope delta without manufacturing completion credit or silently recalculating weights. |
| Future implementation | Review exact owner contracts, role/delegation scope, isolation, quotas, concurrency, denial cases and recovery; commercial eligibility does not grant IAM authority. |
| Qualification | Record executed evidence on the exact implementation. Documentation consistency does not close M1/M4 or establish a production release. |

See the [edition strategy](../../development/EDITIONS-AND-PREMIUM.md), [platform integration register](../../development/PLATFORM-INTEGRATIONS-COVERAGE.md), [Tenancy guide](../../../WCP360_Core/src/Module/Tenancy/README.md), [retained reseller design](../../../WCP360_Core/src/Module/Tenancy/RESELLER-PACKAGES.md) and [Core roadmap](../../../WCP360_Core/ROADMAP.md). The existing baseline manifest and `CODE-BASELINE=NONE` remain under their existing authority.
