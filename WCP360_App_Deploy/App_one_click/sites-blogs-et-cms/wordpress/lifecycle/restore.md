# WordPress: restore

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: BackupRestore, Hosting, Data, Files and Operations. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require a verified coherent backup, compatible application/PHP/database/plugin versions and a fresh authorized target with capacity. Resolve secrets through protected references and ensure restored jobs/emails cannot affect production before cutover.

## Intended target and effects

Restore database/files/uploads/configuration into isolated owned resources and reconstruct the required version set. Reinject scoped credentials, verify ownership and routing, then test admin/login, private/public content and uploads. A domain change requires an application-aware migration, not arbitrary SQL text replacement.

## Failure and recovery

Keep the production source unchanged until validation and explicit traffic handover. If any restore stage is uncertain, fence the destination and inspect it before retry or cleanup. Preserve the backup and record whether a fresh re-restore is required.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). Use [official recovery guidance](https://developer.wordpress.org/advanced-administration/security/backup/) as a reference; a successful file restore alone is insufficient.
