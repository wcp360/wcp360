# Platform

**Status: executable development candidate for ROOT server observations, Workbench HTTP routes and application-server defaults, with shared platform preflight code; general node administration is not implemented.**

Node inventory, global platform settings, selected service/network adapters and typed agent ports.

Start here when changing: **Node/service settings, approved agent integration and infrastructure policy.**

## Implemented candidate paths

| Concern | Actual source and scope |
| :--- | :--- |
| Root server overview | [ServerOverview](Application/ServerOverview.php), a narrow read-only local observation port and browser/API routes; current ADMIN checks, memory/storage and no system effects. |
| Application-server defaults | [ApplicationServerSettings](Application/ApplicationServerSettings.php) stores the default for new hosting plans, checks current administrator authority and rejects stale revisions. Existing sites retain their selected runtime. |
| Workbench HTTP interface | [WorkbenchController](UI/Http/WorkbenchController.php) exposes candidate authentication pages, dashboard, plan/job actions, settings and the reference plugin lifecycle. It delegates resource decisions to the owning services; `/health` checks the panel database connection. |
| OS and runtime preflight | Shared [TargetPlatform](../../Shared/Platform/TargetPlatform.php), [RuntimePlan](../../Shared/Platform/RuntimePlan.php) and [RuntimeReadiness](../../Shared/Platform/RuntimeReadiness.php) support Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS recognition, read-only PHP 8.4 planning and actual PostgreSQL 17 readiness checks. |

Target recognition, native package probes and userspace tests do not qualify privileged host operations. The disposable activation VM gate remains Debian 13 only; these helpers do not widen it or install system packages/services.

## Root server overview

[ServerOverview](Application/ServerOverview.php) implements the server root administrator's read-only capacity view through the narrow [ServerObservation](Application/ServerObservation.php) port. [LocalServerObservation](Infrastructure/LocalServerObservation.php) reuses existing [LinuxHostInventory](../../Shared/Platform/LinuxHostInventory.php) parsers and the five-target catalogue. It skips all procfs network-table reads and projects only OS family, recognized target, architecture (at most 64 bytes), nullable memory total/available bytes and nullable total/free bytes for the filesystem containing `/var`. No process UID, hostname, listener map, managed path inventory, credentials or database URL is returned.

[ServerController](UI/Http/ServerController.php) exposes `GET /workbench/server` and `GET /workbench/v1/server`. The WCP360 ROOT sidebar, command palette and dashboard link the page. Browser/API authentication, current active ADMIN checks before collection and immediately before return, existing optional/enabled MFA, no-store and safe error boundaries remain enforced. Anonymous browser/API requests retain login/401 behavior. A hosting user named `root` still has only USER authority. Unknown query fields, paths, commands or selectors fail with 400; mutating methods are unsupported. HEAD returns no body.

The [view](../../../templates/platform/server.html.twig) displays observed capacity and a timestamp, storage occupation with an accessible meter, explicit refresh and an audit link. Unknown measurements remain null and display unavailable, including Windows preview mode; zero available memory/free space remains a real zero. The scope is **current process namespace**, not a whole-host or cgroup-budget guarantee. Values are observations, not service health, tenant quotas, native-install admission or authority to apply a change. No fake CPU/load/service status or background polling is added.

OS-release reads now stop after 65,537 bytes and reject input over 65,536; catalogue reads stop after 16,385 and retain the existing 16,384-byte limit. Existing `/etc` precedence, valid release symlinks, strict target recognition and standalone PHP 8.1 compatibility remain. The original full preflight collection still includes network observations by default. No reader accepts a browser-supplied file path or shell command.

[Service/revocation definitions](../../../tests/Integration/ServerOverviewTest.php), [ROOT/USER/MFA/browser/API journeys](../../../tests/Integration/ServerJourneyTest.php), [actual restricted ADMIN-role check](../../../tests/Integration/CandidateDatabaseRolesTest.php), [host inventory checks](../../../tests/Unit/LinuxHostInventoryTest.php) and [platform bounds](../../../tests/Unit/TargetPlatformTest.php) cover the slice. Current-source execution is recorded in draft PR #116; local executable tests remain stopped. Existing ADMIN identity-read grants suffice; no schema, migration, grant, dependency, authentication mechanism or service account changes are introduced.

## Remaining scope

Node inventory, general hierarchical settings, service/package/storage administration, SSH/network/firewall adapters and a complete production API remain to be implemented and qualified. The current global runtime default is one candidate setting, not a complete configuration subsystem.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Service restart/validation is a registered bounded operation; PHP does not execute an arbitrary shell.

- [Core navigation index](../../../NAVIGATION.md)
- [Core roadmap and completion evidence](../../../ROADMAP.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
