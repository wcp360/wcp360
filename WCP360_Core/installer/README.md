# Installer source and orchestration

**Status: read-only candidate preflight and development helpers exist; no qualified production installer is provided.** Existing versioned shell scaffolds keep their guarded non-operational behavior.

| Source area | Responsibility |
| :--- | :--- |
| [preflight](preflight/README.md) | Read-only discovery and validation before selecting or applying an installation plan. |
| [recipes](recipes/README.md) | Reviewed orchestration for an approved installation plan and selected compatible components. |
| [upgrades](upgrades/README.md) | Version-specific compatibility checks and orchestration for upgrades from explicitly supported installed versions. |
| [recovery](recovery/README.md) | State inspection, bounded resume/repair and recovery procedures for incomplete or failed installation/upgrade steps. |
| [scaffolds](scaffolds/README.md) | Preserve the earlier unqualified guarded shell templates; they are not released installers. |

The eventual autonomous CLI/bootstrap entry point must work before Symfony is installed, verify qualified artifact identity and select an accepted installation plan. Its source can live here while being packaged as an independently runnable installation artifact. Do not add a fake executable or dependency lock just to fill the layout.

The declared targets are Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS, identified through the shared `config/platforms/targets.json` catalogue. Read-only OS detection, PHP 8.4 Core prerequisites, native-package probes and the [source-runtime Core matrix](../../documentation/development/CORE-RUNTIME-MATRIX.md) are separate checks. The latter compiles the same reviewed PHP 8.4 source within each userspace and exercises the non-root development checkout with an isolated PostgreSQL 17 service. The privileged systemd/VM rehearsal remains Debian 13-specific. Production targets still require qualified runtime providers and reviewed service/recovery adapters; no preflight adds a PPA or changes host configuration. See the [platform guide](../../documentation/development/SUPPORTED-PLATFORMS.md).

The [candidate preparation helper](recipes/candidate-install.sh) supports read-only `--plan` and `--check-runtime` modes. The former reports the reviewed development runtime profile; the latter checks actual PHP/extensions and the server version of the process `DATABASE_URL` without loading `.env` or running migrations. With no option, the helper prepares an existing trusted checkout as a non-root operator: Composer dependencies, planner, assets, local environment and schema migrations. It does not install operating-system packages or services. [Command details and limits](preflight/README.md)

The restricted wizard UI lives in [templates/setup](../templates/setup/README.md); business state and orchestration live in the [Setup module](../src/Module/Setup/README.md). Reviewed host operations use accepted installation/agent boundaries. Repeated execution must distinguish fresh installation, resume, repair and supported upgrade.

Service templates/profiles have a single source in [resources/services](../resources/services/README.md). Recipes select compatible resources and approved operations rather than maintaining another copy of service defaults. Machine settings, secrets and live /etc files stay outside versioned source.

- [Dependency and toolchain locations](../DEPENDENCIES.md)
- [Installation/recovery tests](../tests/Installer/README.md)
- [Delivery and release acceptance](../../documentation/development/DELIVERY-AND-RELEASES.md)
- [Core scope/status](../README.md)
