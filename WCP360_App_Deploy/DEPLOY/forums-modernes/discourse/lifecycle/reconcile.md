# Discourse: reconcile

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Operations, Hosting and Platform, with Data/BackupRestore for migration recovery. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require durable instance/operation identity, current authority, the expected version tuple and exclusion of a still-mutating builder/updater. Observe build/image publication, volume/database migration, upstream launcher drift and public/private ingress state.

## Intended target and effects

Classify completed, known failed/refused, partial and uncertain effects. Reuse known owned resources instead of bootstrapping a second forum or dropping existing data. Continue only a reviewed idempotent provider step; migrations may require an explicit compatible restore/repair plan.

## Failure and recovery

A lost acknowledgement is not permission to rebuild, migrate or restore again. Preserve artifacts, backup references, audit and unknown outcomes. Reconciliation does not promise zero data loss or automatic rollback; apply the documented recovery objective and disclose gaps.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). No general Docker command, shell compensation, lease or fencing implementation is created by this document.
