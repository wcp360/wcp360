# Installation recipes

**Status: a restartable non-root development checkout installer is implemented; no qualified production host installer is provided.**

[candidate-install.sh](candidate-install.sh) prepares an existing complete, trusted repository checkout: generate a private local environment if missing, install locked Composer dependencies, build the Go planner, prepare assets and run migrations against the configured database. It requires the PHP 8.4 runtime and tools to exist already. Its no-option path refuses root and does not install OS packages, PostgreSQL services or systemd units. The default local database configuration is the candidate SQLite workbench; an explicit process `DATABASE_URL` can select the disposable PostgreSQL workbench used by CI.

The shell entry point delegates orchestration to [candidate-install.php](candidate-install.php) and the Setup module's [CandidateInstaller](../../src/Module/Setup/Infrastructure/CandidateInstaller.php). The fixed sequence is:

| Step | Work | Resume behavior |
| :--- | :--- | :--- |
| `composer_install` | Install the reviewed locked dependencies. | Replay safe preparation after admission and binding checks. |
| `dependencies` | Verify the required runtime and installed dependencies. | Recheck the actual environment. |
| `planner_build` | Build the candidate Go planner. | Rebuild generated output. |
| `importmap_install` | Prepare locked frontend dependencies. | Repeat preparation. |
| `asset_compile` | Compile mapped frontend assets. | Regenerate assets. |
| `database_migrations` | Apply candidate schema migrations to the configured database. | Verify successful completion read-only; refuse automatic retry after failure or interruption. |

Run without an option for a fresh checkout. Once a journal exists, inspect `--status` and use `--resume` explicitly. The private journal is `var/installer/candidate-install.json`; a lock covers the full mutating run. Resume checks recorded source, configuration and environment identity rather than trusting step names alone. Existing `.env` is preserved; initial creation is exclusive and private. See [recovery rules](../recovery/README.md) before continuing an interrupted run. No reset flag or automatic migration rollback is provided.

The binding covers all inherited environment values, the selected tools, runtime target, relevant Core sources and dotenv files. Resume requires the same execution environment; even terminal/session environment changes can refuse this deliberately strict candidate. Generated `var/`, `vendor/`, `assets/vendor/`, `public/assets/` and Symfony's `config/reference.php` are excluded. Private dotenv files must belong to the operator and deny group/other access (`chmod 600`). Raw child output is discarded because dependency/bootstrap errors may contain configuration values; only fixed step names and exit codes are reported and recorded. No child-output log is retained.

The `--plan` and `--check-runtime` options execute only the corresponding [read-only preflight command](../preflight/README.md), before checkout writes or migrations. `--status` also performs no mutation and works without Composer dependencies. The source-build profile and the real PostgreSQL 17 check are documented in the [Core runtime matrix](../../../documentation/development/CORE-RUNTIME-MATRIX.md). A successful readiness report is not a production installation or qualification result.

After preparation, enroll an account through `WCP_CONTEXT=setup php bin/console wcp:candidate:enroll`. Graphical first-run setup is not implemented by this recipe.

Production recipes belong here as reviewed orchestration for an approved installation plan and selected compatible components.

Select pinned qualified artifacts and the canonical resources/services templates. Preserve explicit identities and package/file ownership, durable progress and operation boundaries. Do not duplicate service defaults, invent privileged primitives or resolve arbitrary dependencies on a production host.

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
