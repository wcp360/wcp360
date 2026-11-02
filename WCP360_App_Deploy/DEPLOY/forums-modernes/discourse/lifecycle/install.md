# Discourse: install

**Status: human authoring plan; not implemented or executable. No new API, IPC operation or agent command is defined.**

Owners: Hosting, Platform, Operations, Tenancy, Data and BackupRestore. Use existing authorized Core use cases/providers when their contracts and implementations exist. The current recipe provides neither that provider nor authority to invoke it.

## Preconditions

Require an admitted Docker/provider profile and exact upstream launcher/template/app/image/plugin/dependency tuple, resources and private owned volumes. Verify domain/ingress/TLS ownership, selected identity/notification mode and absence of another app at the target. No image or release is selected by this draft.

## Intended target and effects

Use a future reviewed adapter for the upstream bundle and internal Nginx, with private ingress behind the Core edge. Initialize the forum and admin admission through its selected upstream flow. Reserve resources without exposing Docker, PostgreSQL/Redis, a public unclaimed setup wizard or arbitrary tenant app.yml/hooks.

## Failure and recovery

Track created versus reused resources. Observe database, bootstrap and first-admin state before any retry; incomplete setup stays fenced/private. Retain data and report uncertain container/build outcomes without granting a generic launcher shell.

Bind every eventual operation to actor/tenant/application ownership, expected resource/recipe/provider revisions and exact admitted artifacts. Preserve idempotency, bounded resources, mandatory audit and observed/uncertain outcomes; never interpret this document as shell steps.

See the [recipe](../recipe.json), [input schema](../configuration.schema.json), [pilot guide](../README.md) and [Core owner](../../../../../WCP360_Core/src/Module/Hosting/design/applications/README.md). Refer to the [official install guide](https://github.com/discourse/discourse/blob/main/docs/INSTALL-cloud.md); WCP360 integration is separately qualified.
