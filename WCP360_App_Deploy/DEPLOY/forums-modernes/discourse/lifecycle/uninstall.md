# Discourse: uninstall

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting, Platform, Operations, BackupRestore and Tenancy. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require explicit instance-removal authority, current ownership/dependency inventory and a reviewed retention/recovery decision. Removing an App Deploy contribution is not permission to stop or delete a running forum.

## Intended target and effects

Drain ingress/background work, stop/revoke owned execution/network bindings and remove only admitted owned runtime resources. Retain forum database/uploads/configuration, plugin/version metadata and backup references by default. Shared engine/network/storage resources must not be deleted merely because the forum references them.

## Failure and recovery

Observe each removed/retained resource after interruption; keep uncertainty explicit. Purge of owned persistent data requires separate explicit authorization and retention/dependency checks. Do not use an unrestricted Docker prune, recursive host delete or launcher cleanup as application uninstall.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). An uninstalled management contribution must leave Core inventory and recovery authority intact.
