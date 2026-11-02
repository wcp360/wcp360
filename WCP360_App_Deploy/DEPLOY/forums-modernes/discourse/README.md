# Discourse candidate recipe

**Status: authoring pilot only. No image/application/runtime pins are resolved, no provider lifecycle is implemented, and no installation is available.**

[recipe.json](recipe.json) binds Discourse / Forums modernes / Application / DEPLOY. Its local authoring identity and directory create no Core operation or USER entitlement. [configuration.schema.json](configuration.schema.json) allows only bounded metadata and registered resource/secret references; no Docker socket, image override, Compose, hook, path, host port or backend URL input.

## Upstream workflow and requirements

Discourse is Ruby/Rails, not PHP. The [official installation documentation](https://github.com/discourse/discourse/blob/main/docs/INSTALL.md), checked 2026-11-02, supports its Docker-based workflow and currently names PostgreSQL 18+, Redis 7 and Ruby 4.0. A future recipe release must pin the corresponding upstream revision, image/build, application, plugins and dependency versions together. WCP360 remains a separately qualified third-party integration.

The first candidate preserves the upstream bundle, internal Nginx and private HTTP ingress to the Core edge. When Core owns public TLS, remove competing container TLS/ACME and public port publication through the reviewed adapter. The [coexistence guide](https://meta.discourse.org/t/run-other-websites-on-the-same-machine-as-discourse/17247) is the reference. Actual socket/volume identities, host exposure and resource budgets remain provider decisions.

The [current install guide](https://github.com/discourse/discourse/blob/main/docs/INSTALL-cloud.md) allows SMTP or Discourse ID identity setup; preserve the chosen mode. This pilot requires a registered SMTP service and scoped credential reference when smtp is selected. That constraint is not a universal upstream SMTP requirement. Never reuse the panel database, credentials or public administration origin.

Root inside the qualified multiprocess container, daemon rootfulness and privileged mode are separate issues. No blanket container user flag or extra host authority is granted. Preserve MessageBus behavior, configured uploads/headers, background workers and supported restart/rebuild semantics.

## Lifecycle plans

| Phase | Plan |
| :--- | :--- |
| install | [install](lifecycle/install.md) |
| configure | [configure](lifecycle/configure.md) |
| update | [update](lifecycle/update.md) |
| uninstall | [uninstall](lifecycle/uninstall.md) |
| backup | [backup](lifecycle/backup.md) |
| restore | [restore](lifecycle/restore.md) |
| inspect | [inspect](lifecycle/inspect.md) |
| reconcile | [reconcile](lifecycle/reconcile.md) |

The [upstream template](https://github.com/discourse/discourse_docker/blob/main/samples/standalone.yml) contains build hooks and persistent data; it is reference material, not tenant-importable executable input. Application backups may omit uploads and do not install plugin code. See [backup documentation](https://meta.discourse.org/t/create-download-and-restore-a-backup-of-your-discourse-database/122710), the [schema guide](../../../schemas/README.md) and [Core provider boundary](../../../../WCP360_Core/src/Module/Platform/design/containers/README.md).
