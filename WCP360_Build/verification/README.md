# Qualification evidence

Reserve checks for package identity, dependency/secret decisions, Core acceptance, plugin lifecycle, fresh installation, upgrade, interrupted jobs and recovery. Link exact source/merge SHA, target, results and retained evidence. Automated results do not become independent review.

See the [build status](../README.md) and [release acceptance guide](../../documentation/development/DELIVERY-AND-RELEASES.md).

Use the [engineering quality and security catalogue](../../documentation/development/QUALITY-SECURITY-STACK.md) to select language, platform and delivery controls, and the [security test matrix](../../documentation/development/SECURITY-TEST-MATRIX.md) to record the behavior and evidence required. A configured tool, a skipped check or an unexecuted test is not a passing result.

## Platform verification entry points

| Entry point | Scope |
| :--- | :--- |
| [candidate-platform-smoke.sh](candidate-platform-smoke.sh) | Five-target native-package probes: actual OS identity, Core prerequisite acceptance/refusal and real hosted Nginx/FPM HTTP/UID/file boundaries. |
| [candidate-platform-core.sh](candidate-platform-core.sh) | Non-root five-target source-runtime Core journey with the same reviewed PHP 8.4 source and real PostgreSQL 17: checkout preparation, application checks, enrollment, HTTP/job and reference-plugin lifecycle. |
| [candidate-platform-core-fixture.php](candidate-platform-core-fixture.php) | Guarded synthetic enrollment/application state and stable logical snapshots used by the Core journey and separate PostgreSQL restore comparison. |
| [Candidate workflow](../../.github/workflows/core-executable-candidate.yml) | Matrix inputs, disposable service/network setup, image identities, PostgreSQL 17 dump/restore/snapshot comparison and cleanup, alongside the separate candidate/browser/VM checks. |

The [Core runtime matrix guide](../../documentation/development/CORE-RUNTIME-MATRIX.md) defines commands, data separation and evidence boundaries. These checks do not qualify native production PHP/PostgreSQL providers, install host services or extend privileged VM admission beyond the existing Debian 13 profile. The [platform guide](../../documentation/development/SUPPORTED-PLATFORMS.md) distinguishes each verification layer.
