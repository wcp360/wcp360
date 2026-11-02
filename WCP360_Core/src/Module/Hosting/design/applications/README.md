# Hosted application lifecycle preparation

**Status: lifecycle design reservation with a separate read-only catalogue candidate. Existing PHP previews and catalogue inspection do not implement a general application installer or container manager.**

[The catalogue bridge](../../APPLICATION-CATALOGUE.md) reads the sibling [AppDeploy workspace](../../../../../../WCP360_App_Deploy/README.md), which owns editorial data and recipe authoring. Core-owned resources, jobs, providers and recovery retain the boundaries below.

Hosting owns each application instance and its ingress/routing intent. Platform owns runtime/container providers, Operations durable changes, Tenancy resource rights, Files storage, Data hosted engines, and BackupRestore recovery. Use the [web-server roadmap](../../WEB-SERVER-ROADMAP.md), [application resource index](../../../../../resources/applications/README.md) and [Core structure](../../../../../STRUCTURE.md).

## One application model

Separate application identity/recipe from topology, runtime mode and provider. A WordPress instance, Symfony deployment, Node service or Discourse deployment can share lifecycle mechanics while keeping application-specific requirements.

| Required descriptor | Responsibility |
| :--- | :--- |
| Identity and ownership | Tenant/account/application ID, resource revision and actual resource owner. |
| Recipe | Version, compatibility, provenance, inputs and declared app/provider/OS support. |
| Runtime | PHP version/extensions, native service or admitted container profile; execution identity and budgets. |
| Ingress | Domain/aliases, approved backend reference, TLS owner, trusted headers, streaming/uploads and timeout policy. |
| Dependencies | Hosted database/cache, storage, secrets, workers/schedules and readiness ordering. |
| Lifecycle | Install/configure/start/suspend/drain/update/inspect/remove with idempotency and observed state. |
| Recovery | Consistent database/files/uploads/configuration restore and supported migration/rollback constraints. |

Recipes contribute requirements, not commands with unlimited authority. Do not duplicate the six current PHP renderer templates or turn arbitrary tenant Compose/YAML into a privileged plan. See [Platform's container boundary](../../../Platform/design/containers/README.md).

The new default topology Nginx → Apache event → PHP-FPM remains an implementation target. Existing Nginx/FPM and Worker assignments retain their meaning. All content passes Apache where native .htaccess is claimed; non-PHP HTTP backends do not need a PHP layer.

## App-specific acceptance

- PHP scripts: handlers, sessions, PATH_INFO, uploads, scheduled jobs, safe failure and no source disclosure.
- WordPress: setup, login/admin, REST, permalinks, .htaccess where available, themes/plugins, updates, cache privacy and restore. A pinned commerce plugin test does not qualify every plugin.
- Frameworks: public-root confinement, environment/secrets, commands/migrations, queue workers, schedules and optional application-qualified Worker state reset.
- Native HTTP apps: admitted provider/identity, bind scope, readiness/drain, WebSocket/SSE, upload limits and service dependencies.
- Discourse: pinned upstream container workflow, its own PostgreSQL/Redis requirements, private ingress and message-bus behavior, identity/notification mode, builds/rebuilds, upgrades and verified application restore.

Unsupported native rules or provider dependencies produce an explicit refusal; the Core does not promise every application on every topology. Advertise a supported tuple only after native syntax, live application, tenant isolation, recovery and performance evidence.

Keep a running resource manageable from Core if its optional installer plugin is disabled. Uninstalling a recipe contribution must not silently delete application data, detach ownership or stop unrelated sites.
