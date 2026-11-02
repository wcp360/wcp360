# Discourse: update

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting, Platform, Operations, BackupRestore and Data. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Pin and review launcher/templates, application, base and resulting images, plugin revisions, Ruby/PostgreSQL/Redis versions and migration compatibility. Detect upstream or in-app update drift, verify coherent backups and reserve temporary disk/build capacity.

## Intended target and effects

A future adapter coordinates drain/read-only policy, bounded build/rebuild and declared migrations, preserving private ingress and volumes. Observe the resulting actual version tuple, jobs, setup/login, topics/search/uploads and MessageBus before ready publication. Do not silently resolve latest or allow competing Core/admin updater jobs.

## Failure and recovery

Reverting an image cannot undo a database-major or application migration. Retain previous artifacts and a compatible complete recovery point, inspect uncertain build/migration outcomes and restore or reconcile according to the admitted procedure.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). Review the [upstream launcher](https://github.com/discourse/discourse_docker/blob/main/launcher) and [PostgreSQL migration template](https://github.com/discourse/discourse_docker/blob/main/templates/postgres.template.yml). Neither is an authorized generic Core operation.
