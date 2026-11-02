# Discourse: inspect

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting and Observability, with Platform/Data for admitted observations. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require registered instance/tenant ownership and bounded provider metadata access. This authoring pilot has no installed forum. Do not expose Docker API credentials, full environment, database dumps, user content or private logs.

## Intended target and effects

Compare desired and observed image/app/plugin/dependency versions, private ingress, TLS ownership, volumes, actual resource budgets, jobs/readiness and MessageBus. Distinguish provider unavailable, uninitialized forum, drift, partial update and unknown state. Treat upstream update UI changes as observations requiring reconciliation.

## Failure and recovery

Read-only inspection neither rebuilds nor repairs a forum. Unknown health/version or resource-budget evidence must not be reported as ready. Keep redacted correlation/recovery references for an authorized next action.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). Core OS recognition or a responding container does not qualify the complete Discourse lifecycle.
