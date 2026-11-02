# Discourse: configure

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting, Platform, Operations and relevant Data/IdentityAccess owners. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require an admitted version tuple, resource ownership, observed provider/container state and a matching revision. Validate registered domain, capacity/storage/SMTP references and private secret bindings; the authoring input cannot carry Compose, URLs, ports or shell commands.

## Intended target and effects

Review application-specific configuration and decide which values require an upstream rebuild. Preserve internal Nginx, MessageBus behavior, selected identity/email flow, explicit upload/header limits and Core-owned public TLS. Configuration must survive the approved rebuild workflow rather than being an untracked edit inside a container.

## Failure and recovery

Retain the old configuration and compatible recovery references. Interrupted rebuild/configuration must be observed before another apply. Do not restore old configuration blindly after a database/application migration or reveal generated secrets in diffs.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). The [upstream standalone template](https://github.com/discourse/discourse_docker/blob/main/samples/standalone.yml) requires rebuilding after changes; it is not importable tenant execution input.
