# Discourse: restore

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: BackupRestore, Hosting, Platform, Data and Operations. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require verified backup scope and an authorized fresh private target with compatible Discourse/PostgreSQL/plugin versions and capacity. Resolve protected settings/secrets; block unintended outbound email or production identity/traffic before validation.

## Intended target and effects

Recreate the qualified upstream deployment and required plugin set, restore database/uploads and protected configuration, then inspect actual version/ownership/health. Test login, topics, search, uploads, jobs and real-time behavior before an explicit domain/TLS/ingress handover.

## Failure and recovery

Never restore a newer backup into an incompatible older application/database blindly. Preserve source and backup; an uncertain destination stays fenced for inspection or a clean re-restore. Application/email settings after restoration must be observed before production enablement.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). Use [official backup/restore guidance](https://meta.discourse.org/t/create-download-and-restore-a-backup-of-your-discourse-database/122710); a usable image without restored data is not recovery.
