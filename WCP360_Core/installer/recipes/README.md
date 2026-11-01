# Installation recipes

**Status: a non-root development checkout preparation helper is implemented; no qualified production host installer is provided.**

[candidate-install.sh](candidate-install.sh) prepares an existing complete, trusted repository checkout: generate a private local environment if missing, install locked Composer dependencies, build the Go planner, prepare assets and run migrations against the configured database. It requires the PHP 8.4 runtime and tools to exist already. Its no-option path refuses root and does not install OS packages, PostgreSQL services or systemd units. The default local database configuration is the candidate SQLite workbench; an explicit process `DATABASE_URL` can select the disposable PostgreSQL workbench used by CI.

The `--plan` and `--check-runtime` options execute only the corresponding [read-only preflight command](../preflight/README.md), before checkout writes or migrations. The source-build profile and the real PostgreSQL 17 check are documented in the [Core runtime matrix](../../../documentation/development/CORE-RUNTIME-MATRIX.md). A successful readiness report is not a production installation or qualification result.

Production recipes belong here as reviewed orchestration for an approved installation plan and selected compatible components.

Select pinned qualified artifacts and the canonical resources/services templates. Preserve explicit identities and package/file ownership, durable progress and operation boundaries. Do not duplicate service defaults, invent privileged primitives or resolve arbitrary dependencies on a production host.

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
