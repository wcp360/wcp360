# WordPress: backup

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: BackupRestore, Hosting, Data, Files and Operations. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require authorized source/destination and key references, a known application/plugin/theme/provider version set and enough bounded backup capacity. Choose an application-consistent point and coordinate writes, cron and queues as needed.

## Intended target and effects

Back up the database and application files/uploads, controlled configuration and required plugin/theme inventory. Record version/digest, ownership, consistency point and protected secret/key references separately. Verify archive/database integrity and retention; do not publish backups under the web root or place them in Git.

## Failure and recovery

An archive command returning success is not a restore proof. Preserve older recoverable snapshots if upload/export fails, reconcile uncertain objects and report missing files/data explicitly. Do not expire the last verified recovery point merely because a new job started.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). [WordPress backups](https://developer.wordpress.org/advanced-administration/security/backup/) require both database and files for a typical complete restoration.
