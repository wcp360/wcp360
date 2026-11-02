# Discourse: backup

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: BackupRestore, Hosting, Platform, Data and Operations. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require current application/backup authority, a coherent application/database/plugin version tuple and protected destination/key references. Declare whether uploads are local or external and ensure writes/jobs are coordinated for the selected recovery point.

## Intended target and effects

Use an eventual application-specific backup adapter for forum data and required uploads. Also preserve protected configuration and the exact plugin/build/version references required for restoration. Verify integrity, ownership and retention without exposing archives through public ingress or committing them to Git.

## Failure and recovery

A database-only archive is not a full media backup; record omissions explicitly. Keep an older verified recovery point after failed export and reconcile ambiguous remote objects. A stopped/rebuilt container alone does not prove application-consistent data.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). [Discourse backup documentation](https://meta.discourse.org/t/create-download-and-restore-a-backup-of-your-discourse-database/122710) distinguishes optional uploads and separately configured plugin code.
