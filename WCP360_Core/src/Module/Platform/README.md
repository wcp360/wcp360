# Platform

**Status: executable development candidate for Workbench HTTP routes and application-server defaults, with shared platform preflight code; general node administration is not implemented.**

Node inventory, global platform settings, selected service/network adapters and typed agent ports.

Start here when changing: **Node/service settings, approved agent integration and infrastructure policy.**

## Implemented candidate paths

| Concern | Actual source and scope |
| :--- | :--- |
| Application-server defaults | [ApplicationServerSettings](Application/ApplicationServerSettings.php) stores the default for new hosting plans, checks current administrator authority and rejects stale revisions. Existing sites retain their selected runtime. |
| Workbench HTTP interface | [WorkbenchController](UI/Http/WorkbenchController.php) exposes candidate authentication pages, dashboard, plan/job actions, settings and the reference plugin lifecycle. It delegates resource decisions to the owning services; `/health` checks the panel database connection. |
| OS and runtime preflight | Shared [TargetPlatform](../../Shared/Platform/TargetPlatform.php), [RuntimePlan](../../Shared/Platform/RuntimePlan.php) and [RuntimeReadiness](../../Shared/Platform/RuntimeReadiness.php) support Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS recognition, read-only PHP 8.4 planning and actual PostgreSQL 17 readiness checks. |

Target recognition, native package probes and userspace tests do not qualify privileged host operations. The disposable activation VM gate remains Debian 13 only; these helpers do not widen it or install system packages/services.

## Remaining scope

Node inventory, general hierarchical settings, service/package/storage administration, SSH/network/firewall adapters and a complete production API remain to be implemented and qualified. The current global runtime default is one candidate setting, not a complete configuration subsystem.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Service restart/validation is a registered bounded operation; PHP does not execute an arbitrary shell.

- [Core navigation index](../../../NAVIGATION.md)
- [Core roadmap and completion evidence](../../../ROADMAP.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
