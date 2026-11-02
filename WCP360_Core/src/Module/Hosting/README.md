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

## Prepared-plan file inspection and downloads

[PreparedPlanDocument](Domain/PreparedPlanDocument.php) owns the existing candidate plan/1 PHP-FPM and plan/2 FrankenPHP field shape, including the original field order and runtime marker. [GoPlanRenderer](Infrastructure/GoPlanRenderer.php) retains its fixed executable, 5-second child limit and 65,536-byte transport capture cap, then delegates document validation. Existing callers retain the same field arrays.

Stored-plan decoding is bounded before JSON parsing (393,216 bytes, allowing PHP's persisted escaping), with depth 16, valid UTF-8, nonempty string fields, no unknown keys and at most 65,536 decoded bytes across fields. Binary control characters are refused; tabs/newlines/carriage returns and printable UTF-8 are preserved. This validates the document envelope, not Nginx/PHP/Caddy/systemd syntax, resource ownership, signatures or deployment safety.

[PreparedPlanFiles](Application/PreparedPlanFiles.php) loads the existing authorized job, inspects output only for SUCCEEDED plans and rechecks current permission before return. It exposes minimal task metadata and fixed artifact descriptors, omitting raw result/fingerprint/idempotency/actor/site fields from the template context. Non-successful results are not interpreted as files. Corrupt/missing/runtime-mismatched successful documents return a generic 503 without reflecting stored data or exception causes. The task page shows escaped per-file previews instead of a raw JSON dump.

[PlanDownloadController](UI/Http/PlanDownloadController.php) exposes `GET /workbench/jobs/{id}/files/{artifact}` with four fixed artifact selectors. PHP-FPM documents provide nginx/php-fpm; Worker documents provide nginx/caddy/systemd. Unsupported selectors, unavailable artifacts and malformed/missing job identities return 404; unfinished tasks return 409; foreign/revoked/inactive USER authority is refused. ADMIN identity does not grant USER download rights. The existing job read now validates UUID syntax before PostgreSQL lookup.

Downloads return exact bounded content in memory, with fixed ASCII `wcp360-{job UUID}-...` filenames, text/plain UTF-8, attachment disposition, byte length, no-store/private and nosniff headers. Preview links disable Turbo interception so the browser handles the attachment natively. HEAD has the same authorization and metadata with no body; range/conditional-cache requests do not create partial/cached responses. There is no arbitrary filename/path, filesystem lookup/write, symlink/archive operation, SQL endpoint or system dispatch.

Read operations do not mutate business state or append business audit. Mandatory current session/tenant checks remain independent of the filename, job UUID or originating request reference. The existing guarded activation workflow remains separate and unchanged; downloading a configuration never authorizes applying it.

[Document definitions](../../../tests/Unit/PreparedPlanDocumentTest.php), [service definitions](../../../tests/Integration/PreparedPlanFilesTest.php), [HTTP journeys](../../../tests/Integration/PlanDownloadJourneyTest.php) and [actual restricted-role checks](../../../tests/Integration/CandidateDatabaseRolesTest.php) cover original/Worker formats, byte/JSON limits, corruption, binary controls, metadata projection, exact export bytes, native navigation, headers, escaping, current state/authority, wrong surface, unsupported paths/methods, legacy browser navigation and no activation. Exact-source CI is recorded in draft PR #116; local executable tests remain stopped. No schema/grant/dependency/new migration is introduced.

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
