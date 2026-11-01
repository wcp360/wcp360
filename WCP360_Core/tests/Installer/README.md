# Installation and upgrade tests

**Status: candidate checkout journal and orchestration have executable regression tests. Production native installation and upgrade acceptance remain unqualified.**

| Executable source | Responsibility |
| :--- | :--- |
| [InstallationJournalTest](../Unit/InstallationJournalTest.php) | Durable state, locking, interrupted steps and refusal of invalid recovery state. |
| [InstallationInputsTest](../Unit/InstallationInputsTest.php) | Source/configuration/environment binding and rejection of changed inputs. |
| [CandidateInstallerTest](../Integration/CandidateInstallerTest.php) | Standalone status, literal command arguments, output redaction and real migration-metadata verification. |

These tests belong to the normal Core PHPUnit suite: run `composer test` or the complete `composer check` from `WCP360_Core/`. A documented test location is not itself a passing result; record the actual command and environment when reporting verification.

Cover fresh preparation, read-only status before dependencies exist, concurrent invocation, changed inputs, failed/interrupted preparation and safe replay. An uncertain migration must block automatic resume; a successful migration checkpoint must be verified without repeating the migration command. Preserve existing preflight behavior and account-enrollment compatibility.

Use disposable machines or images with explicit ownership for real installer execution. The five-target source-runtime matrix exercises candidate checkout preparation on Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS. Native packages, privileged service changes, upgrade paths and backup recovery require their own acceptance evidence; this journal does not broaden privileged target admission.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
