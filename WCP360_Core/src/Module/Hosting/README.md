# Hosting

**Status: executable development candidate for hosting plans and guarded PHP-FPM activation; the complete production hosting lifecycle is not implemented.**

Sites, domains, TLS, web-engine selection and hosted language-runtime assignments.

Start here when changing: **Website creation, virtual hosts, certificates and PHP pool settings.**

## Implemented candidate paths

| Concern | Actual source and scope |
| :--- | :--- |
| Hosting requests | [SubmitHostingPlan](Application/SubmitHostingPlan.php) validates tenant permission, lowercase ASCII `.test` names and idempotency before creating a durable plan job. |
| Runtime selection | [ApplicationServerPolicy](Application/ApplicationServerPolicy.php) selects PHP-FPM by default or explicitly enabled FrankenPHP Worker, with worker compatibility acknowledgement. Global defaults belong to Platform. |
| Configuration plans | [GoPlanRenderer](Infrastructure/GoPlanRenderer.php) calls the fixed, bounded [Go planner](../../../agents/wcpd/candidate/planner/README.md). Existing durable plans retain plan/1 for Classic and plan/2 for Advanced; a separate inert Modern preview uses plan/3. Rendering alone does not activate a site. |
| Modern preview | [PreviewModernHostingPlan](Application/PreviewModernHostingPlan.php) prepares Caddy/systemd text for a new synthetic site identifier, without creating a site, job, audit entry or activation. It requires current ROOT authority and the existing Worker planning opt-in. |
| Physical activation | [SubmitActivation](Application/SubmitActivation.php) and [RunActivation](Application/RunActivation.php) require a separate activation grant, check the planned revision and distinguish failure from an uncertain outcome. Inspection reconciles an uncertain result without blindly replaying the apply operation. |
| Execution boundary | [DisposableExecution](Infrastructure/DisposableExecution.php) restricts this activation experiment to an explicitly prepared disposable container or Debian 13 VM. It does not authorize production execution or privileged execution on every recognized OS. |

## Web-stack profiles and development direction

The [adopted strategy](WEB-SERVER-STRATEGY.md) targets **Nginx → Apache event → PHP-FPM** for new general-purpose installations. All content requests, including static files, pass through Apache under the default compatibility policy. The [web-server roadmap](WEB-SERVER-ROADMAP.md) orders the new profile, native lifecycle, PHP alternatives, application/container backends, Docker/Discourse and performance qualification. This is a target architecture; the new topology is not yet an implemented recipe or selectable durable profile.

The [shared web-stack catalogue](../../Shared/Platform/WebStackCatalogue.php) describes the existing candidate definitions independently of the application-server setting. Its read-only installer entry is `php installer/preflight/runtime-plan.php --web-stacks`; the ROOT workbench exposes the same catalogue through `GET /workbench/v1/web-stacks`. A catalogue entry or inspection result does not establish installed-service support, native installation admission or production readiness.

| Profile | Intended composition | Current boundary |
| :--- | :--- | :--- |
| Classic — current candidate default | Nginx + PHP-FPM, with isolated site pools and eventual managed PHP-version assignment. | The candidate renders this composition and has a separately guarded PHP-FPM activation experiment. Its reviewed runtime uses one PHP series; complete hosted multi-PHP management is not implemented. |
| Modern | Standalone Caddy/FrankenPHP in Worker mode, without a mandatory Nginx edge. | An inert ROOT preview renders a one-site loopback HTTP plan. Durable hosting-plan selection and activation remain unavailable; public listeners, TLS ownership and runtime qualification remain to be implemented. |
| Legacy / compatibility | Apache HTTP Server + PHP-FPM for applications requiring Apache-specific behavior. | Planned and unavailable. A future `mod_php` variant would require a separate explicitly selected and qualified optional profile; it is not a default or an available driver. |
| Advanced / reverse proxy | An explicitly chosen edge proxy and backend combination. The existing candidate is Nginx → Caddy/FrankenPHP Worker over a per-site Unix socket. | Existing Worker rendering is opt-in and plan-only. Other combinations remain planned; enabling the planning flag does not install or activate a backend. |

Existing `frankenphp_worker` assignments and candidate plan/2 retain their original Nginx-edge meaning. They must not be reinterpreted as standalone Modern plans. The current Caddy socket backend disables automatic HTTPS and its admin API, and provides no public Caddy listener or HTTP/3 exposure. Standalone Modern may later offer those Caddy capabilities after explicit configuration and qualification; catalogue membership does not guarantee them or a performance improvement.

Worker applications must explicitly support persistent execution, reset request-specific state between requests and use bounded process recycling. Each tenant site requires its own unprivileged process identity and isolated runtime resources; sharing one persistent application process across unrelated sites is not an accepted shortcut. The panel's own runtime and a hosted site's runtime remain separate choices.

An eventual installation or site/group stack switch must persist a typed profile and its revision, select versioned Go-owned templates and present a configuration diff before applying. It must establish listener and TLS ownership, check capabilities and runtime compatibility, drain existing workers when needed, verify resulting health and retain a controlled recovery path. Existing sites must not change merely because a global default changes. Reviewed agent operations perform bounded effects; no profile accepts arbitrary shell commands, service names or configuration paths.

## Persisted site runtime profiles

[SiteRuntimeProfile](Domain/SiteRuntimeProfile.php) makes the existing durable engine/topology mapping explicit: `php_fpm` is `classic` and `frankenphp_worker` is `advanced`. [Version20261102000200](../../../migrations/Version20261102000200.php) adds `web_stack` and `stack_revision` to the existing site table and backfills those pairs with profile revision 1. The migration preserves existing site IDs/lifecycle revisions and all jobs, result bytes, fingerprints and audit history. It does not introduce another table or broaden the 16-table role policy.

Database guards require non-null metadata, revision 1 and the matching legacy engine/topology pair. PostgreSQL uses constraints; SQLite uses insert/update triggers without rebuilding referenced tables or disabling foreign keys. A final SQLite validation update checks the backfilled rows; PostgreSQL validates them when adding its constraints. New writes must supply an explicit profile rather than falling back to a Classic default. Modern, Legacy, unknown profiles, mismatched pairs and unsupported revisions remain unavailable for durable storage. The typed value object also refuses missing/malformed metadata and canonicalizes only database revision `1` or `'1'`.

[SubmitHostingPlan](Application/SubmitHostingPlan.php) writes the profile explicitly after the existing engine/Worker policy decision; request fields, idempotency fingerprints, global settings, engine enum and public projections retain their original contracts. The worker validates the profile before invoking Go, stored-plan exports validate it before decoding, and the existing PHP-FPM activation path validates Classic metadata before queuing or dispatching its bounded operation. This adds no Modern activation, new stack selector, native-stack installation or API for switching sites.

The worker captures its validated profile and supplies it to the optional publication guard in [HostingPlanQueue](../Operations/Infrastructure/HostingPlanQueue.php). Publication locks the current site row on PostgreSQL, compares the profile and rechecks current authority within the existing transaction. A changed/invalid profile discards the rendered result and uses the existing FAILED/PLAN_FAILED transition with its required correlated audit. The generic two-argument queue contract remains unchanged. Normal terminal transitions still update the site's lifecycle revision; they do not increment `stack_revision`.

Apply this forward-only migration through the normal Doctrine setup maintenance procedure before running the matching source. Stop writers and rendering workers, retain a verified candidate backup/recovery path and use migration tracking; a direct repeat of the schema upgrade is refused. Lossy down-migration is refused rather than dropping profile intent. No local migration or executable test was run for this work.

This is profile storage and validation, without an increment/switch workflow or immutable per-job topology history. An eventual stack migration must snapshot or fence job intent, coordinate existing results/work, establish port/TLS ownership, drain workers, verify health and preserve recovery. The separate Modern preview, its Worker opt-in, optional MFA enforcement and plan/3 document stay unchanged. Existing plan/1 and plan/2 wire formats, downloads and USER/ADMIN/worker grants retain their meaning.

[Value-object definitions](../../../tests/Unit/SiteRuntimeProfileTest.php), [schema regressions](../../../tests/Integration/SiteRuntimeProfileSchemaTest.php), [consumer regressions](../../../tests/Integration/SiteRuntimeProfileWorkflowTest.php) and [restricted-role checks](../../../tests/Integration/CandidateDatabaseRolesTest.php) define migration preservation, invalid-profile refusal, publication drift, export and activation boundaries. These definitions are not executed results; exact-source outcomes belong to [draft PR #116](https://github.com/yyogas/wcp360/pull/116).

## Standalone Modern preview

[ModernPlanPreviewController](UI/Http/ModernPlanPreviewController.php) exposes browser `POST /workbench/settings/modern-preview` from the ROOT settings page and JSON `POST /workbench/v1/web-stacks/modern-preview`. Both require the current active ADMIN-context ROOT identity, `WCP_ALLOW_FRANKENPHP_PLANS=1`, exact same-origin requests, the dedicated `modern-plan-preview` CSRF token and a fresh ROOT authentication proof. MFA remains optional; an enabled factor must be completed. Browser requests use the form token; the API requires `X-CSRF-Token` and a JSON body of at most 4,096 bytes.

Inputs are a lowercase ASCII `.test` hostname and explicit Worker compatibility acknowledgment (`true` or `'1'`). Unknown body/query fields, caller-selected actor/tenant/site identities, file paths, apply flags and unsupported methods are refused. [CandidateHostname](Domain/CandidateHostname.php) shares the existing submission rule. The service generates a fresh synthetic UUIDv7 and checks current ROOT authority before and after rendering; that identifier does not select an existing account or resource.

The separate [Go Modern driver](../../../agents/wcpd/candidate/planner/modern.go) renders `wcp360.candidate.plan/3` with exactly `schema`, `web_stack`, `application_server`, `caddy` and `systemd_unit` in that order. Markers are `modern` and `frankenphp_worker`. [ModernPlanDocument](Domain/ModernPlanDocument.php) explicitly validates this envelope and exposes only Caddyfile and `frankenphp-modern.service` text. Existing [PreparedPlanDocument](Domain/PreparedPlanDocument.php) accepts only its original plan/1 and plan/2 contracts and rejects plan/3; Modern never changes the meaning of a stored Worker assignment.

The generated Caddyfile uses `http://{hostname}:8082` with `bind 127.0.0.1`, automatic HTTPS disabled and the admin API off. Its service has a per-site non-root identity, bounded workers/recycling/memory/tasks, an empty capability set and separate Modern configuration/runtime/state paths. Port 8082 is fixed and unreserved: this is a single-site inert preview, without listener ownership or simultaneous multi-site allocation. There is no Nginx edge or PHP-FPM pool, and no TLS, ACME, HTTP/3, public listener, service installation, start or activation.

The shared renderer preserves its fixed executable/argv, scrubbed inherited environment, five-second timeout and 65,536-byte stdout bound. [PlanDocumentFields](Domain/PlanDocumentFields.php) supplies the shared bounded JSON and decoded-string checks described below. The Modern decoder requires exact fields/order/markers and rejects controls, invalid UTF-8, empty/non-string fields and oversized data. Renderer failures return a generic 503 without child output or exception details; browser code blocks use Twig escaping. Responses are private and non-cacheable.

The response reports `persisted=false`, `host_changes=false`, `activation_available=false` and `listener_allocation=not_reserved`. No durable job/site/default or business audit is written. The preview itself introduces no schema, database grant or dependency change. Existing queue processing, global/default selectors, site inventory and stored-plan downloads remain unchanged. The catalogue still describes durable hosting-plan capabilities, so Modern remains unavailable there and in the application-server selector despite this separate preview being implemented. No capability status, release qualification or production-readiness claim is promoted.

[Document definitions](../../../tests/Unit/ModernPlanDocumentTest.php), [service definitions](../../../tests/Integration/ModernPlanPreviewTest.php) and [HTTP journeys](../../../tests/Integration/ModernPreviewJourneyTest.php) define the relevant boundaries. Go definitions cover strict CLI selection, original wire compatibility, bounded identities/resources and loopback-only artifacts. Test definitions are not execution evidence; exact-source CI is recorded in draft PR #116, with local executable tests stopped.

## Prepared-plan file inspection and downloads

[PreparedPlanDocument](Domain/PreparedPlanDocument.php) owns the existing candidate plan/1 PHP-FPM and plan/2 FrankenPHP field shape, including the original field order and runtime marker. [GoPlanRenderer](Infrastructure/GoPlanRenderer.php) retains its fixed executable, 5-second child limit and 65,536-byte transport capture cap, then delegates document validation. Existing callers retain the same field arrays.

Stored-plan decoding is bounded before JSON parsing (393,216 bytes, allowing PHP's persisted escaping), with depth 16, valid UTF-8, nonempty string fields, no unknown keys and at most 65,536 decoded bytes across fields. Binary control characters are refused; tabs/newlines/carriage returns and printable UTF-8 are preserved. This validates the document envelope, not Nginx/PHP/Caddy/systemd syntax, resource ownership, signatures or deployment safety.

[PreparedPlanFiles](Application/PreparedPlanFiles.php) loads the existing authorized job, inspects output only for SUCCEEDED plans and rechecks current permission before return. It exposes minimal task metadata and fixed artifact descriptors, omitting raw result/fingerprint/idempotency/actor/site fields from the template context. Non-successful results are not interpreted as files. Corrupt/missing/runtime-mismatched successful documents return a generic 503 without reflecting stored data or exception causes. The task page shows escaped per-file previews instead of a raw JSON dump.

[PlanDownloadController](UI/Http/PlanDownloadController.php) exposes `GET /workbench/jobs/{id}/files/{artifact}` with four fixed artifact selectors. PHP-FPM documents provide nginx/php-fpm; Worker documents provide nginx/caddy/systemd. Unsupported selectors, unavailable artifacts and malformed/missing job identities return 404; unfinished tasks return 409; foreign/revoked/inactive USER authority is refused. ADMIN identity does not grant USER download rights. The existing job read now validates UUID syntax before PostgreSQL lookup.

Downloads return exact bounded content in memory, with fixed ASCII `wcp360-{job UUID}-...` filenames, text/plain UTF-8, attachment disposition, byte length, no-store/private and nosniff headers. Preview links disable Turbo interception so the browser handles the attachment natively. HEAD has the same authorization and metadata with no body; range/conditional-cache requests do not create partial/cached responses. There is no arbitrary filename/path, filesystem lookup/write, symlink/archive operation, SQL endpoint or system dispatch.

Read operations do not mutate business state or append business audit. Mandatory current session/tenant checks remain independent of the filename, job UUID or originating request reference. The existing guarded activation workflow remains separate and unchanged; downloading a configuration never authorizes applying it.

[Document definitions](../../../tests/Unit/PreparedPlanDocumentTest.php), [service definitions](../../../tests/Integration/PreparedPlanFilesTest.php), [HTTP journeys](../../../tests/Integration/PlanDownloadJourneyTest.php) and [actual restricted-role checks](../../../tests/Integration/CandidateDatabaseRolesTest.php) cover original/Worker formats, byte/JSON limits, corruption, binary controls, metadata projection, exact export bytes, native navigation, headers, escaping, current state/authority, wrong surface, unsupported paths/methods, legacy browser navigation and no activation. Exact-source CI is recorded in draft PR #116; local executable tests remain stopped. These export routes add no separate grant or dependency; the matching source requires the site-profile migration described above.

## Prepared-site inventory

[ListPreparedSites](Application/ListPreparedSites.php), [SiteListQuery](Domain/SiteListQuery.php) and [SiteListController](UI/Http/SiteListController.php) provide `GET /workbench/sites` and `GET /workbench/v1/sites`. They require the existing USER session, current active-account tenant membership and no explicit block. ADMIN identity/roles and cursors never grant USER resource authority. Each pivot, page and boundary query composes the same IdentityAccess tenant scope, with a final current-authority check before return.

Browser navigation defaults to the first accessible workspace and shows a dedicated empty view when none is accessible. The API requires an explicit `tenant`. Optional fields are `state` (PLAN_QUEUED/PLAN_READY/PLAN_FAILED), `server` (php_fpm/frankenphp_worker), `q` (ASCII hostname fragment), `limit` (25/50/100) and either `after` or `before` (site UUID). Unknown fields, arrays, unsupported states/engines, wildcards and unbounded/conflicting cursor input fail closed. A stored Worker assignment can be inspected even if creating new Worker plans is currently disabled; listing grants no activation capability.

The fixed response contains `items` and `pagination: {limit,next,previous}`. Each item exposes only ID, hostname, preparation state, application server and integer revision. It omits tenant/account IDs, worker acknowledgment, plan output, actor IDs, credentials and request references. No job join, full count, business-state mutation or audit-read grant is required.

Pages use the same deterministic ASCII domain/UUID ordering for selection and cursor comparisons (PostgreSQL C / SQLite BINARY). Missing, foreign and filtered-out pivots share one safe refusal. This is live navigation, not a frozen snapshot: domain/state/runtime changes or deletion can move/invalidate a pivot; restart from the first page when needed. Large-dataset performance qualification remains open.

The WCP360 page has labeled filters, preparation-status feedback, an empty result view, 25/50/100-row pages and task-history links with the current tenant/hostname filter. It clearly distinguishes a prepared plan from actual site deployment/health. The task link performs a hostname-fragment search, not an exclusive resource-history join. `Hébergements` in the sidebar/command palette and the dashboard now open this inventory.

[Shared page bounds](../../Shared/Http/TenantPageQuery.php) are reused by site/job query objects; owner-specific states and projections stay separate. Both views use the [same cursor navigation component](../../../templates/components/data/cursor-pagination.html.twig). No generic SQL executor, new schema, dependency or database grant is introduced.

[Query definitions](../../../tests/Unit/SiteListQueryTest.php), [service definitions](../../../tests/Integration/ListPreparedSitesTest.php), [HTTP journeys](../../../tests/Integration/SiteListJourneyTest.php) and [restricted PostgreSQL-role definitions](../../../tests/Integration/CandidateDatabaseRolesTest.php) cover page traversal beyond 100 rows, insertions, filters/projection, foreign pivots, revocation/inactivity, empty scope, escaping, stored Worker visibility, SQLite, read-only behavior and ADMIN refusal. Existing job-query/browser cases cover shared-component compatibility. Execution evidence belongs to exact-source CI in draft PR #116. This read surface does not implement real-domain admission, DNS inventory, site lifecycle, quotas or production qualification.

## Reusable PHP application profiles

The [application-profile engine](APPLICATION-PROFILES.md) renders six web/PHP combinations for traditional PHP, front controllers and a WordPress reference. It separates application behavior, server topology and runtime mode; WordPress is not a special Core architecture. ROOT browser/API previews use a shared boundary with Modern while preserving separate CSRF intents. This inert family has its own strict envelope; original site/profile/plan contracts remain unchanged. Optional native syntax checks are now available: bounded non-root Nginx tests, Caddy/FrankenPHP adaptation when their fixed providers exist and explicit unavailable/not-run stages with exact artifact hashes. Default previews do not invoke a parser. Live installation, complete native recipe validation, application compatibility and comparative performance still require separate evidence.

## Remaining scope

Production account/site lifecycle, real-domain admission, authoritative DNS, ACME/TLS, runtime/version administration, quotas and full change/recovery workflows remain to be implemented and qualified. The worker plan selector is not a general production FrankenPHP installer or activation service.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Hosting owns Nginx/Apache/PHP-FPM policy. The [service resource index](../../../resources/services/README.md) identifies canonical configuration ownership and links the existing candidate renderers; controllers must not duplicate their configuration text.

- [Core navigation index](../../../NAVIGATION.md)
- [Core roadmap and completion evidence](../../../ROADMAP.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)

The README groups several capability domains here: websites, authoritative DNS, certificates/ACME and hosted PHP/PHP-FPM. Runtime assignment, effective settings, pool health, permitted PHP extensions and a site's Composer/CLI task remain scoped to that hosted project and identity; they are distinct from the Core Composer dependency graph. The [capability map](../../../CAPABILITY-MAP.md) identifies collaborators without creating a second resource authority.
