# Platform

**Status: executable development candidate for ROOT server observations, Workbench HTTP routes and application-server defaults, with shared platform preflight code; general node administration is not implemented.**

Node inventory, global platform settings, selected service/network adapters and typed agent ports.

Start here when changing: **Node/service settings, approved agent integration and infrastructure policy.**

Preparation for growth is documented in [container providers](design/containers/README.md), [capacity/node topology](design/scalability/README.md) and [optional AI/MCP integrations](design/ai-mcp/README.md). These are design reservations; they add no installed provider, remote agent, public endpoint or new authority. The [Core structure](../../../STRUCTURE.md) maps the collaborating resource owners.

## Implemented candidate paths

| Concern | Actual source and scope |
| :--- | :--- |
| ROOT service observations | [ServiceOverview](Application/ServiceOverview.php), a fixed read-only systemd observation port and ROOT browser/API; bounded collection with explicit uncertainty and no service control. |
| Root server overview | [ServerOverview](Application/ServerOverview.php), a narrow read-only local observation port and browser/API routes; current ADMIN checks, memory/storage and no system effects. |
| Application-server defaults | [ApplicationServerSettings](Application/ApplicationServerSettings.php) stores the default for new hosting plans, checks current administrator authority and rejects stale revisions. Existing sites retain their selected runtime. |
| Runtime-settings interface | [RuntimeSettingsController](UI/Http/RuntimeSettingsController.php) shares dedicated ROOT forms/API, guarded reads and revision/audit writes; retains the dashboard POST contract. |
| Workbench HTTP interface | [WorkbenchController](UI/Http/WorkbenchController.php) exposes candidate authentication pages, dashboard, plan/job actions and the reference plugin lifecycle. Dedicated settings routes live in RuntimeSettingsController. It delegates resource decisions to the owning services; `/health` checks the panel database connection. |
| OS and runtime preflight | Shared [TargetPlatform](../../Shared/Platform/TargetPlatform.php), [RuntimePlan](../../Shared/Platform/RuntimePlan.php) and [RuntimeReadiness](../../Shared/Platform/RuntimeReadiness.php) support Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS recognition, read-only PHP 8.4 planning and actual PostgreSQL 17 readiness checks. |

Target recognition, native package probes and userspace tests do not qualify privileged host operations. The disposable activation VM gate remains Debian 13 only; these helpers do not widen it or install system packages/services.

## ROOT service observations

The [service observation guide](SERVICES.md) documents the new read-only Platform use case, fixed nine-family/fourteen-unit catalogue and bounded systemd adapter. ROOT browser/API routes distinguish validated unit state, missing observations and unavailable collection; service health and control remain explicitly unverified/unavailable. Current ADMIN checks surround collection, all selectors are refused and no system-control permission or provider installation is added. Native systemd-host qualification remains separate from parser, adapter-contract and HTTP fixture checks.

## Root server overview

[ServerOverview](Application/ServerOverview.php) implements the server root administrator's read-only capacity view through the narrow [ServerObservation](Application/ServerObservation.php) port. [LocalServerObservation](Infrastructure/LocalServerObservation.php) reuses existing [LinuxHostInventory](../../Shared/Platform/LinuxHostInventory.php) parsers and the five-target catalogue. It skips all procfs network-table reads and projects only OS family, recognized target, architecture (at most 64 bytes), nullable memory total/available bytes and nullable total/free bytes for the filesystem containing `/var`. No process UID, hostname, listener map, managed path inventory, credentials or database URL is returned.

[ServerController](UI/Http/ServerController.php) exposes `GET /workbench/server` and `GET /workbench/v1/server`. The WCP360 ROOT sidebar, command palette and dashboard link the page. Browser/API authentication, current active ADMIN checks before collection and immediately before return, existing optional/enabled MFA, no-store and safe error boundaries remain enforced. Anonymous browser/API requests retain login/401 behavior. A hosting user named `root` still has only USER authority. Unknown query fields, paths, commands or selectors fail with 400; mutating methods are unsupported. HEAD returns no body.

The [view](../../../templates/platform/server.html.twig) displays observed capacity and a timestamp, storage occupation with an accessible meter, explicit refresh and an audit link. Unknown measurements remain null and display unavailable, including Windows preview mode; zero available memory/free space remains a real zero. The scope is **current process namespace**, not a whole-host or cgroup-budget guarantee. Values are observations, not service health, tenant quotas, native-install admission or authority to apply a change. No fake CPU/load/service status or background polling is added.

OS-release reads now stop after 65,537 bytes and reject input over 65,536; catalogue reads stop after 16,385 and retain the existing 16,384-byte limit. Existing `/etc` precedence, valid release symlinks, strict target recognition and standalone PHP 8.1 compatibility remain. The original full preflight collection still includes network observations by default. No reader accepts a browser-supplied file path or shell command.

[Service/revocation definitions](../../../tests/Integration/ServerOverviewTest.php), [ROOT/USER/MFA/browser/API journeys](../../../tests/Integration/ServerJourneyTest.php), [actual restricted ADMIN-role check](../../../tests/Integration/CandidateDatabaseRolesTest.php), [host inventory checks](../../../tests/Unit/LinuxHostInventoryTest.php) and [platform bounds](../../../tests/Unit/TargetPlatformTest.php) cover the slice. Current-source execution is recorded in draft PR #116; local executable tests remain stopped. Existing ADMIN identity-read grants suffice; no schema, migration, grant, dependency, authentication mechanism or service account changes are introduced.

## ROOT global runtime settings

The ROOT sidebar, command palette and dashboard expose `GET /workbench/settings`, a dedicated view of the existing default used by new hosting plans. It reuses [ApplicationServerSettings](Application/ApplicationServerSettings.php), the existing shared selector and the current PHP-FPM / FrankenPHP Worker policy. The report distinguishes the stored default/revision from the operator's Worker-planning flag; this flag is not evidence of installed services, runtime readiness or application compatibility.

[RuntimeSettingsController](UI/Http/RuntimeSettingsController.php) owns the existing `POST /workbench/settings/application-server` route, replacing the previous Workbench action rather than registering a second writer. Existing dashboard submissions keep their route name, CSRF intent and 303 dashboard redirect. The dedicated form selects only the fixed `view=settings` return view. A stale revision returns 409 with the current value/revision and asks the operator to review and submit again; no action is automatically replayed. Success redirects to the dedicated page with accessible feedback. Unknown navigation/query/body fields are refused.

The same controller exposes `GET` and `POST /workbench/v1/settings/application-server`. The fixed report is `{schema, settings: {application_server, revision}, capabilities: {frankenphp_worker_plans}}`. Read requests have no query parameters; HEAD has no body. Writes use the existing browser cookie authentication, exact Origin and the `application-server` CSRF intent in `X-CSRF-Token`; this is not a bearer-token API. They require JSON at most 4,096 bytes with only a concrete `application_server` and positive `revision` (integer or canonical decimal string, at most nine digits). A successful write returns the current report; stale revisions return the shared JSON 409 error. Clients must inspect current state and explicitly resubmit, not retry blindly.

Both transports require fully authenticated current ADMIN authority; a USER named `root` remains unauthorized. Optional MFA remains optional, while enabled MFA and the existing recent-proof gate protect writes. Authorized inspection rechecks active ROOT authority before and after the settings read. Changing a default uses the existing locked authorization, revision compare-and-swap and mandatory audit transaction. Browser/API writes attach the shared server-generated request reference to `candidate.runtime.default.changed`; client reference headers are ignored. Default audit INSERT-only grants remain sufficient.

Saving the default affects only new plans that inherit it. Existing site selections and the panel's own runtime remain unchanged; Worker site plans still require their per-application compatibility acknowledgement. No installation, service restart, Go dispatch, schema/grant/dependency change or new general configuration engine is introduced. An unset Worker flag prevents selecting Worker and lets ROOT explicitly restore PHP-FPM if an earlier Worker default is stored. Read/failed-write errors use the shared safe/no-store HTTP boundary. A failed audit rolls back the default and revision; a response failure after commit requires inspecting current state/audit rather than assuming no change.

[Service/revocation cases](../../../tests/Integration/RuntimeSettingsTest.php), [browser/API/MFA journeys](../../../tests/Integration/RuntimeSettingsJourneyTest.php), the [existing dashboard regression](../../../tests/Integration/ApplicationServerUiTest.php), [inheritance cases](../../../tests/Integration/ApplicationServerTest.php) and [restricted PostgreSQL-role cases](../../../tests/Integration/CandidateDatabaseRolesTest.php) define the checks. Exact-source CI evidence remains in [draft PR #116](https://github.com/wcp360/wcp360/pull/116); local executable checks remain stopped. General hierarchical configuration and production integration remain open.

## Shared hosting web-stack catalogue

The [adopted web-server strategy](../Hosting/WEB-SERVER-STRATEGY.md) sets Nginx → Apache event → PHP-FPM as the target default for new general-purpose installations. The current candidate catalogue described here still defaults to Nginx/FPM. Its values and persisted assignments are not reinterpreted; [the web-server roadmap](../Hosting/WEB-SERVER-ROADMAP.md) requires an explicit new topology/version and qualified installation/migration before changing effective behavior. Managed native/container applications and Docker/Discourse also need their own provider lifecycle.

The [fixed catalogue](../../Shared/Platform/WebStackCatalogue.php) separates a complete hosting topology from a PHP application engine. It is shared by standalone installer inspection, ROOT settings and the hosting-plan form. `GET /workbench/v1/web-stacks` returns the candidate catalogue to a fully authenticated current ROOT account, with the existing optional/enabled MFA boundary, no query parameters, no-store and body-free HEAD. The existing settings report keeps its schema and projection unchanged.

Classic is the default and maps to stored `php_fpm`. The existing opt-in `frankenphp_worker` maps to Advanced: Nginx fronts a per-site Caddy/FrankenPHP socket. Standalone Modern and Apache Legacy have no durable submission value. Modern has a separate inert preview driver, described below. Apache now has two generated application preview recipes (FPM and an HTTP proxy to FrankenPHP), documented in [application profiles](../Hosting/APPLICATION-PROFILES.md); it still has no durable engine selection or native activation. The Worker-planning flag cannot enable either as a persisted hosting-plan selection. The frontend offers only the two existing engine values, labels their actual stacks and displays the full catalogue separately. Forging an unavailable stack as an engine remains a refusal, not a fallback.

The read-only catalogue distinguishes plan implementation, operator opt-in, isolated candidate activation, native installation, migration and multi-PHP. Native installation, general migration and managed multi-version PHP remain unavailable. Existing plan/1–2, persisted assignments, compatibility acknowledgements, site/panel separation, installer apply behavior and database grants are unchanged. The catalogue grants no effect authority and performs no service/package/configuration changes.

[Catalogue/standalone cases](../../../tests/Unit/WebStackCatalogueTest.php), [ROOT/USER/MFA/API journeys](../../../tests/Integration/WebStackJourneyTest.php) and the existing [restricted-role case](../../../tests/Integration/CandidateDatabaseRolesTest.php) define the checks. Current-source execution belongs to draft PR #116; local executable tests remain stopped. See [Hosting's development direction](../Hosting/README.md#web-stack-profiles-and-development-direction) for the separate preview driver and the remaining durable-plan and controlled migration requirements.

## ROOT standalone Modern preview

The settings page also offers a separate [Modern preview](../Hosting/README.md#standalone-modern-preview). Its browser POST and cookie-authenticated API prepare only the two plan/3 artifacts; they never save the default, create a site/job or activate a runtime. Existing Worker-planning opt-in, an explicit application-compatibility acknowledgement, current ROOT authority, recent authentication, exact Origin and the separate `modern-plan-preview` CSRF intent protect the request. MFA remains optional; an enabled factor must be completed.

Each call generates a new synthetic UUIDv7 and shows the fixed, unreserved `127.0.0.1:8082` HTTP listener. No resource ID, path, port or apply argument can be supplied. Caddy's admin API and automatic HTTPS are off. Modern's catalogue selection and native-installation flags remain unavailable: this preview is neither a durable plan nor a qualified public listener. The existing settings response schema, global default, persisted engine values, grants and runtime activation paths are unchanged.

## Remaining scope

Node inventory, general hierarchical settings, service/package/storage administration, SSH/network/firewall adapters and a complete production API remain to be implemented and qualified. The current global runtime default is one candidate setting, not a complete configuration subsystem.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Service restart/validation is a registered bounded operation; PHP does not execute an arbitrary shell.

- [Core navigation index](../../../NAVIGATION.md)
- [Core roadmap and completion evidence](../../../ROADMAP.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
