# Setup

**Status: interactive candidate enrollment, restartable checkout preparation and a read-only native-installation handoff are implemented. Graphical first-run setup and production installation/upgrade orchestration remain future work.**

Initial setup state, administrator enrollment, restricted wizard, install/upgrade progress and recovery handoff.

Start here when changing: **Wizard steps, setup validation and interrupted-install recovery.**

| Source | Responsibility |
| :--- | :--- |
| [UI/Console/SetupCommand.php](UI/Console/SetupCommand.php) | Interactive candidate account enrollment using password hashing, transactional persistence and audit. |
| [Application/NativeInstallationPlan.php](Application/NativeInstallationPlan.php) | Evaluate bounded host observations and explicit requirements; describe blocked native-installation prerequisites without granting host effects. |
| [Infrastructure/CandidateInstaller.php](Infrastructure/CandidateInstaller.php) | Fixed non-root checkout preparation and explicit resume orchestration. |
| [Infrastructure/InstallationJournal.php](Infrastructure/InstallationJournal.php) | Private durable checkpoints and a lock spanning each mutating run. |
| [Infrastructure/InstallationInputs.php](Infrastructure/InstallationInputs.php) | Bind recovery state to the reviewed source, configuration and environment inputs. |

Prepare a trusted checkout through [installer/recipes/candidate-install.sh](../../../installer/recipes/candidate-install.sh). Use `--status` for read-only inspection and `--resume` for explicit continuation. Failed or interrupted database migrations require manual reconciliation or a verified backup; successful migrations are verified read-only on resume. The installer does not provision native packages or system services.

The standalone [native-plan preflight](../../../installer/preflight/README.md#read-only-native-installation-handoff) consumes live [LinuxHostInventory](../../Shared/Platform/LinuxHostInventory.php) observations without Composer, `.env`, database access or system commands. Requested TCP/UDP ports, available-memory thresholds and `/var` free-space thresholds are explicit diagnostic inputs, not accepted production defaults. `--tcp=none` and `--udp=none` explicitly request no ports; omission remains `not_checked`. Omitted requirements, unknown observations and existing managed paths require review. Every report retains `native_apply_allowed: false`; the development runtime profile supplies no production package-provider trust or privileged operation.

Enroll the initial candidate administrator with `WCP_CONTEXT=setup php bin/console wcp:candidate:enroll`, selecting the administrator account context in the interactive terminal. The graphical wizard is not enabled by this command. Candidate execution still requires its explicit development/test runtime guards.

Use the common [module layout](../README.md) for new Domain, Application, Infrastructure and UI code. Standalone CLI entry points live in installer; this module does not provide a generic privileged shell.

- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
