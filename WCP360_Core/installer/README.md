# Installer source and orchestration

**Status: read-only preflight and a restartable non-root candidate checkout installer are implemented. No qualified production host installer is provided.** Existing versioned shell scaffolds keep their guarded non-operational behavior.

| Source area | Responsibility |
| :--- | :--- |
| [preflight](preflight/README.md) | Read-only discovery and validation before selecting or applying an installation plan. |
| [recipes](recipes/README.md) | Reviewed orchestration for an approved installation plan and selected compatible components. |
| [upgrades](upgrades/README.md) | Version-specific compatibility checks and orchestration for upgrades from explicitly supported installed versions. |
| [recovery](recovery/README.md) | State inspection, bounded resume/repair and recovery procedures for incomplete or failed installation/upgrade steps. |
| [scaffolds](scaffolds/README.md) | Preserve the earlier unqualified guarded shell templates; they are not released installers. |

The eventual autonomous CLI/bootstrap entry point must work before Symfony is installed, verify qualified artifact identity and select an accepted installation plan. Its source can live here while being packaged as an independently runnable installation artifact. Do not add a fake executable or dependency lock just to fill the layout.

The declared targets are Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS, identified through the shared `config/platforms/targets.json` catalogue. Read-only OS detection, PHP 8.4 Core prerequisites, native-package probes and the [source-runtime Core matrix](../../documentation/development/CORE-RUNTIME-MATRIX.md) are separate checks. The latter compiles the same reviewed PHP 8.4 source within each userspace and exercises the non-root development checkout with an isolated PostgreSQL 17 service. The privileged systemd/VM rehearsal remains Debian 13-specific. Production targets still require qualified runtime providers and reviewed service/recovery adapters; no preflight adds a PPA or changes host configuration. See the [platform guide](../../documentation/development/SUPPORTED-PLATFORMS.md).

The [candidate installer](recipes/candidate-install.sh) prepares an existing complete, trusted checkout as a non-root operator. It creates a private `.env` only when missing, installs locked dependencies, builds the planner, prepares assets and runs candidate schema migrations. Required runtimes and tools must already exist. It does not install operating-system packages or services.

| Invocation from `WCP360_Core/` | Behavior |
| :--- | :--- |
| `installer/recipes/candidate-install.sh --plan` | Read-only development runtime plan. |
| `installer/recipes/candidate-install.sh --check-runtime` | Read-only PHP/extensions and actual PostgreSQL server check using process `DATABASE_URL`; does not load `.env`. |
| `installer/recipes/candidate-install.sh --status` | Read-only inspection of an existing journal; requires no Composer dependencies and creates no state or lock. |
| `installer/recipes/candidate-install.sh` | Start preparation of a fresh checkout. Existing journal state requires explicit resume. |
| `installer/recipes/candidate-install.sh --resume` | Revalidate the recorded source, configuration and environment binding before resuming permitted steps. |

Progress lives in private `var/installer/candidate-install.json`. A lock covers the whole mutating run, and durable checkpoints distinguish completion, failure and interruption. Preparation steps can be replayed after successful admission; migration uncertainty requires manual reconciliation or a verified backup, never automatic retry. Successful migrations are checked read-only during resume. There is no reset flag. See the [recipe](recipes/README.md), [recovery procedure](recovery/README.md) and [preflight limits](preflight/README.md).

Interactive account enrollment remains `WCP_CONTEXT=setup php bin/console wcp:candidate:enroll`. The graphical first-run wizard remains future work: its reserved views live in [templates/setup](../templates/setup/README.md), and business orchestration belongs to the [Setup module](../src/Module/Setup/README.md). Reviewed host operations must use accepted installation/agent boundaries. Checkout resume is not a production repair or upgrade mechanism.

Service templates/profiles have a single source in [resources/services](../resources/services/README.md). Recipes select compatible resources and approved operations rather than maintaining another copy of service defaults. Machine settings, secrets and live /etc files stay outside versioned source.

- [Dependency and toolchain locations](../DEPENDENCIES.md)
- [Installation/recovery tests](../tests/Installer/README.md)
- [Delivery and release acceptance](../../documentation/development/DELIVERY-AND-RELEASES.md)
- [Core scope/status](../README.md)
