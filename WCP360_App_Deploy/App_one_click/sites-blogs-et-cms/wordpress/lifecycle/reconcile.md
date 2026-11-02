# WordPress: reconcile

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Operations and Hosting, with affected Data/Files/BackupRestore owners. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require durable operation/instance identity, current authority, expected revisions and evidence that an earlier worker cannot still mutate the same application. Inspect actual file/database/admin initialization state and owned resources before deciding a next action.

## Intended target and effects

Classify known complete, known refused/failed, partially applied and uncertain outcomes. Reconcile already-observed effects without creating a second database/admin user or overwriting a restored site. Continue only a reviewed idempotent step; otherwise expose a bounded repair or restore plan.

## Failure and recovery

Never rerun the whole install/update on uncertainty. Configuration revision alone does not fence application-written file/database changes. Retain audit, recovery point and the exact artifact/provider references; unrecoverable or ambiguous state remains explicit.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). This plan implements no lease/fence, automatic retry or provider compensation.
