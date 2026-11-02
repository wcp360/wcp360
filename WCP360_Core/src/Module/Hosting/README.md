# Hosting

**Status: executable development candidate for hosting plans and guarded PHP-FPM activation; the complete production hosting lifecycle is not implemented.**

Sites, domains, TLS, web-engine selection and hosted language-runtime assignments.

Start here when changing: **Website creation, virtual hosts, certificates and PHP pool settings.**

## Implemented candidate paths

| Concern | Actual source and scope |
| :--- | :--- |
| Hosting requests | [SubmitHostingPlan](Application/SubmitHostingPlan.php) validates tenant permission, lowercase ASCII `.test` names and idempotency before creating a durable plan job. |
| Runtime selection | [ApplicationServerPolicy](Application/ApplicationServerPolicy.php) selects PHP-FPM by default or explicitly enabled FrankenPHP Worker, with worker compatibility acknowledgement. Global defaults belong to Platform. |
| Configuration plans | [GoPlanRenderer](Infrastructure/GoPlanRenderer.php) calls the fixed, bounded [Go planner](../../../agents/wcpd/candidate/planner/README.md). Its candidate templates render Nginx, PHP-FPM or FrankenPHP configuration; rendering alone does not activate a site. |
| Physical activation | [SubmitActivation](Application/SubmitActivation.php) and [RunActivation](Application/RunActivation.php) require a separate activation grant, check the planned revision and distinguish failure from an uncertain outcome. Inspection reconciles an uncertain result without blindly replaying the apply operation. |
| Execution boundary | [DisposableExecution](Infrastructure/DisposableExecution.php) restricts this activation experiment to an explicitly prepared disposable container or Debian 13 VM. It does not authorize production execution or privileged execution on every recognized OS. |

## Prepared-site inventory

[ListPreparedSites](Application/ListPreparedSites.php), [SiteListQuery](Domain/SiteListQuery.php) and [SiteListController](UI/Http/SiteListController.php) provide `GET /workbench/sites` and `GET /workbench/v1/sites`. They require the existing USER session, current active-account tenant membership and no explicit block. ADMIN identity/roles and cursors never grant USER resource authority. Each pivot, page and boundary query composes the same IdentityAccess tenant scope, with a final current-authority check before return.

Browser navigation defaults to the first accessible workspace and shows a dedicated empty view when none is accessible. The API requires an explicit `tenant`. Optional fields are `state` (PLAN_QUEUED/PLAN_READY/PLAN_FAILED), `server` (php_fpm/frankenphp_worker), `q` (ASCII hostname fragment), `limit` (25/50/100) and either `after` or `before` (site UUID). Unknown fields, arrays, unsupported states/engines, wildcards and unbounded/conflicting cursor input fail closed. A stored Worker assignment can be inspected even if creating new Worker plans is currently disabled; listing grants no activation capability.

The fixed response contains `items` and `pagination: {limit,next,previous}`. Each item exposes only ID, hostname, preparation state, application server and integer revision. It omits tenant/account IDs, worker acknowledgment, plan output, actor IDs, credentials and request references. No job join, full count, business-state mutation or audit-read grant is required.

Pages use the same deterministic ASCII domain/UUID ordering for selection and cursor comparisons (PostgreSQL C / SQLite BINARY). Missing, foreign and filtered-out pivots share one safe refusal. This is live navigation, not a frozen snapshot: domain/state/runtime changes or deletion can move/invalidate a pivot; restart from the first page when needed. Large-dataset performance qualification remains open.

The WCP360 page has labeled filters, preparation-status feedback, an empty result view, 25/50/100-row pages and task-history links with the current tenant/hostname filter. It clearly distinguishes a prepared plan from actual site deployment/health. The task link performs a hostname-fragment search, not an exclusive resource-history join. `Hébergements` in the sidebar/command palette and the dashboard now open this inventory.

[Shared page bounds](../../Shared/Http/TenantPageQuery.php) are reused by site/job query objects; owner-specific states and projections stay separate. Both views use the [same cursor navigation component](../../../templates/components/data/cursor-pagination.html.twig). No generic SQL executor, new schema, dependency or database grant is introduced.

[Query definitions](../../../tests/Unit/SiteListQueryTest.php), [service definitions](../../../tests/Integration/ListPreparedSitesTest.php), [HTTP journeys](../../../tests/Integration/SiteListJourneyTest.php) and [restricted PostgreSQL-role definitions](../../../tests/Integration/CandidateDatabaseRolesTest.php) cover page traversal beyond 100 rows, insertions, filters/projection, foreign pivots, revocation/inactivity, empty scope, escaping, stored Worker visibility, SQLite, read-only behavior and ADMIN refusal. Existing job-query/browser cases cover shared-component compatibility. Execution evidence belongs to exact-source CI in draft PR #116. This read surface does not implement real-domain admission, DNS inventory, site lifecycle, quotas or production qualification.

## Remaining scope

Production account/site lifecycle, real-domain admission, authoritative DNS, ACME/TLS, runtime/version administration, quotas and full change/recovery workflows remain to be implemented and qualified. The worker plan selector is not a general production FrankenPHP installer or activation service.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Hosting owns Nginx/Apache/PHP-FPM policy. The [service resource index](../../../resources/services/README.md) identifies canonical configuration ownership and links the existing candidate renderers; controllers must not duplicate their configuration text.

- [Core navigation index](../../../NAVIGATION.md)
- [Core roadmap and completion evidence](../../../ROADMAP.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)

The README groups several capability domains here: websites, authoritative DNS, certificates/ACME and hosted PHP/PHP-FPM. Runtime assignment, effective settings, pool health, permitted PHP extensions and a site's Composer/CLI task remain scoped to that hosted project and identity; they are distinct from the Core Composer dependency graph. The [capability map](../../../CAPABILITY-MAP.md) identifies collaborators without creating a second resource authority.
