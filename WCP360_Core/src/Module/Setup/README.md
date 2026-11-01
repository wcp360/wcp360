# Setup

**Status: interactive candidate enrollment, restartable checkout preparation and a read-only native-installation handoff are implemented. Graphical first-run setup and production installation/upgrade orchestration remain future work.**

Initial setup state, administrator enrollment, restricted wizard, install/upgrade progress and recovery handoff.

Start here when changing: **Wizard steps, setup validation and interrupted-install recovery.**

| Source | Responsibility |
| :--- | :--- |
| [UI/Console/SetupCommand.php](UI/Console/SetupCommand.php) | Interactive candidate account enrollment using password hashing, transactional persistence and audit. |
| [Application/NativeInstallationPlan.php](Application/NativeInstallationPlan.php) | Evaluate bounded host observations and explicit requirements; describe blocked native-installation prerequisites without granting host effects. |
| [Application/CandidateArtifactVerifier.php](Application/CandidateArtifactVerifier.php) | Verify a candidate manifest's detached signature, expected identity and local payload size/digest against an independently pinned operator key. |
| [Infrastructure/ArtifactInput.php](Infrastructure/ArtifactInput.php) | Bound local-file reads and refuse unsafe file types, links or staging ownership/permissions. |
| [Infrastructure/CandidateInstaller.php](Infrastructure/CandidateInstaller.php) | Fixed non-root checkout preparation and explicit resume orchestration. |
| [Infrastructure/InstallationJournal.php](Infrastructure/InstallationJournal.php) | Private durable checkpoints and a lock spanning each mutating run. |
| [Infrastructure/CandidateDatabaseRoles.php](Infrastructure/CandidateDatabaseRoles.php) | Transactional creation of fresh candidate PostgreSQL roles, or an explicit fixed password-operation upgrade on verified existing roles; no generic repair or automatic retry. |
| [Infrastructure/CandidatePasswordRoutinePolicy.php](Infrastructure/CandidatePasswordRoutinePolicy.php) | Validate frozen password operations, owner/settings and ACLs before fresh role creation or an explicit existing-role capability upgrade. |
| [Infrastructure/CandidateDatabasePolicy.php](Infrastructure/CandidateDatabasePolicy.php) | One grant matrix for the implemented USER, ADMIN and worker candidate paths. |
| [Infrastructure/CandidateRowLockSchema.php](Infrastructure/CandidateRowLockSchema.php) | New migration input for constant-zero row-lock markers; no UPDATE grant on identity columns. |
| [UI/Console/DatabaseRolesCommand.php](UI/Console/DatabaseRolesCommand.php) | Setup-only read-only plan or explicitly enabled apply, with private secret inputs and redacted JSON. |
| [Infrastructure/InstallationInputs.php](Infrastructure/InstallationInputs.php) | Bind recovery state to the reviewed source, configuration and environment inputs. |

Prepare a trusted checkout through [installer/recipes/candidate-install.sh](../../../installer/recipes/candidate-install.sh). Use `--status` for read-only inspection and `--resume` for explicit continuation. Failed or interrupted database migrations require manual reconciliation or a verified backup; successful migrations are verified read-only on resume. The installer does not provision native packages or system services.

The standalone [native-plan preflight](../../../installer/preflight/README.md#read-only-native-installation-handoff) consumes live [LinuxHostInventory](../../Shared/Platform/LinuxHostInventory.php) observations without Composer, `.env`, database access or system commands. Requested TCP/UDP ports, available-memory thresholds and `/var` free-space thresholds are explicit diagnostic inputs, not accepted production defaults. `--tcp=none` and `--udp=none` explicitly request no ports; omission remains `not_checked`. Omitted requirements, unknown observations and existing managed paths require review. Every report retains `native_apply_allowed: false`; the development runtime profile supplies no production package-provider trust or privileged operation.

The [offline candidate artifact command](../../../installer/preflight/README.md#offline-candidate-artifact-verification) also runs before Composer with PHP 8.1 and sodium. It authenticates exact bounded manifest bytes with an ordinary Ed25519 detached signature, checks the expected component/version/target, and compares the observed local payload length and SHA-256. Input files must satisfy the protected staging/file guards. The independently pinned public key is an explicit operator input; no official publisher identity or private key is bundled. There is no download, extraction, subprocess, installation or journal mutation. Verification success means `candidate_verified: true` for observed bytes while `native_apply_allowed` and `qualified_release` remain false. Accepted release trust/revocation/rollback policy, immutable staging reuse and native-provider/effect qualification remain open; the native plan's artifact-trust blocker is unchanged.

The [candidate database role recipe](../../../installer/recipes/CANDIDATE-DATABASE-ROLES.md) operates only on the reviewed disposable development databases and requires migrations first. It is separate from the default checkout installer and never runs automatically. Its `--password-changes` mode inspects or explicitly enables only the new fixed password operations on an existing verified candidate role set, preserving role IDs/passwords and table grants. Apply [Version20261101000600](../../../migrations/Version20261101000600.php) first. Native PostgreSQL service/HBA setup, secret distribution and production identity qualification remain open.

The enrollment terminal asks for a **username**, a separate **contact email**, the USER/ADMIN context and a hidden password. Usernames contain 3–32 lowercase ASCII letters, digits or underscores and begin with a letter. Login accepts the username only. Existing checkouts must apply the [username migration](../../../migrations/Version20261101000500.php) before using the updated Core; see [the migration and session rules](../IdentityAccess/README.md#username-authentication).

Enroll the initial candidate administrator with `WCP_CONTEXT=setup php bin/console wcp:candidate:enroll`, selecting the administrator account context in the interactive terminal. The graphical wizard is not enabled by this command. Candidate execution still requires its explicit development/test runtime guards.

Use the common [module layout](../README.md) for new Domain, Application, Infrastructure and UI code. Standalone CLI entry points live in installer; this module does not provide a generic privileged shell.

- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
