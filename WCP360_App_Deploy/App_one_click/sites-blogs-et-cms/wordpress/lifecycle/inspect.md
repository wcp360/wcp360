# WordPress: inspect

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting and Observability, with Data/Files for bounded observations. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require current scope/ownership and a registered instance. The draft recipe has no installed instance to inspect. Limit collection to admitted metadata and avoid credentials, cookies, content or unbounded filesystem/database reads.

## Intended target and effects

Compare desired and observed application/file/database/runtime versions, owned dependencies, routing/HTTPS, update policy and health. Distinguish an uninitialized site, drift, a missing provider and unavailable evidence. Any eventual application diagnostic tool runs under the application scope; it is not a generic PHP/shell executor.

## Failure and recovery

Inspection performs no repair, secret rotation, plugin update or overwrite. Unknown observations remain unknown and do not become ready/installed. Record a redacted evidence reference for later authorized reconciliation.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). Existing Core configuration previews and parser results do not prove an installed WordPress instance.
