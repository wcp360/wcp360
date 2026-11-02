# WordPress: update

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting, Operations, BackupRestore, Data and Files. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Select an exact approved application/tool/provider/plugin/theme version set with verified artifacts and compatible data migration. Observe current files/database version, drift and update ownership. Take a coherent recovery point and allocate build/update capacity before mutation.

## Intended target and effects

Drain or enter an application-qualified maintenance path; stage the reviewed release and apply its admitted database update. Coordinate scheduler/background writes and the application's own update mechanism so two writers cannot race. Verify login/admin, public/private pages, REST, permalinks, uploads and the declared plugin/theme set.

## Failure and recovery

Do not equate old files with database rollback. Observe incomplete migration/update state and restore the verified matching database/files set when required. Retain original references and uncertainty after lost acknowledgement; never blindly force-retry an application update.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). WP-CLI's [database update procedure](https://developer.wordpress.org/cli/commands/core/update-db/) is a reference for a future typed adapter. [WordPress backup guidance](https://developer.wordpress.org/advanced-administration/security/backup/) requires a pre-upgrade backup.
