# WordPress: install

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting, Platform, Tenancy, Data, Files and Operations. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require an admitted release/artifact, supported PHP/extensions and MySQL/MariaDB provider, authorized account/domain/database bindings, capacity and a private first-run path. Check the target is uninitialized; an existing or foreign installation must not be overwritten. None of these release/provider prerequisites is satisfied by the draft.

## Intended target and effects

Plan owned application files, PHP identity/profile, datastore grants, configuration/salts and first administrator initialization. Resolve secrets privately; do not put passwords into a public manifest, process listing or logs. Configure HTTPS/routing and inspect installation/login before publishing readiness. The Nginx/Apache/FPM target profile is not yet an implemented Core choice.

## Failure and recovery

Record which resources were created versus reused. Interrupted database/user initialization needs observation before retry; an existing admin account is not silently reset. Preserve data and quarantine an incomplete application rather than leaving an unclaimed public installer.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). WP-CLI documents [core installation](https://developer.wordpress.org/cli/commands/core/install/); a future typed adapter may use a pinned tool, not arbitrary WP-CLI arguments or source hooks.
