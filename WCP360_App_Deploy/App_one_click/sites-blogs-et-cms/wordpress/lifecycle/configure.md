# WordPress: configure

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting, Data, IdentityAccess, Files and Operations. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require current ownership, observed application/release/provider state, a matching expected revision, admitted typed settings and resolved scoped secrets. Refuse unsupported native rules, runtime preferences or foreign database/domain references.

## Intended target and effects

Review the settings diff and update only owned configuration through the eventual adapter. Coordinate site URL/HTTPS, database references, PHP selection and native/managed routing. Respect application-owned mutable settings and plugin/theme changes; do not blindly rewrite wp-config.php or .htaccess. The input schema's metadata is not PHP source.

## Failure and recovery

Keep private backups of configuration with controlled access. If configuration publication or health is uncertain, inspect both files and application state before reconciliation. A config rollback is allowed only where compatible with any accompanying data changes.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). Compare native .htaccess and managed Nginx behavior using [official routing guidance](https://developer.wordpress.org/advanced-administration/server/web-server/nginx/).
