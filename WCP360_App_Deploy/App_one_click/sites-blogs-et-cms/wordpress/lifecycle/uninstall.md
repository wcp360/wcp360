# WordPress: uninstall

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting, Operations, BackupRestore, Data and Tenancy. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require explicit current application ownership and dependency inventory. Distinguish instance removal from removing its optional installer/recipe contribution. Confirm retention/recovery policy and prevent deletion of shared or operator-reused databases, credentials or storage.

## Intended target and effects

Drain application traffic/jobs, revoke its owned execution bindings and remove only the selected application's owned runtime/ingress resources through future admitted operations. Retain database/files/uploads/configuration and required version metadata by default. Purge must be a separate explicitly authorized workflow, not a boolean silently inherited from installation.

## Failure and recovery

Before deletion, preserve durable intent and recovery references. After an interruption, inspect each resource instead of assuming all were removed. Keep retained data accessible only to its owner; revoking one application must not revoke another resource's shared credential or domain.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). No Core table, site neighbor, database merely referenced by the app or unrelated service may be deleted.
