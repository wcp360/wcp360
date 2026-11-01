# Core runtime matrix on Debian and Ubuntu

This development verification runs the same locked Symfony Core with PHP 8.4 and a real PostgreSQL 17 service on five target userspaces: Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS. It exercises application behavior beyond the separate [native package probes](SUPPORTED-PLATFORMS.md#what-each-verification-layer-establishes). Configuring these jobs is not evidence that they have passed; inspect the exact source SHA and job results in [PR #116](https://github.com/yyogas/wcp360/pull/116).

## Shared runtime inputs

| Source | Responsibility |
| :--- | :--- |
| [targets.json](../../WCP360_Core/config/platforms/targets.json) | Exact target IDs, releases, native PHP families, architecture and base images. |
| [runtime-providers.json](../../WCP360_Core/config/platforms/runtime-providers.json) | Reviewed PHP 8.4 source version, download URL, SHA-256 and installation prefix; PostgreSQL 17 verification service profile. |
| [RuntimePlan](../../WCP360_Core/src/Shared/Platform/RuntimePlan.php) | Validate those inputs and describe the development runtime without changing the host. |
| [RuntimeReadiness](../../WCP360_Core/src/Shared/Platform/RuntimeReadiness.php) | Inspect the executing PHP version/extensions and connect read-only to the explicitly supplied database. |
| [Platform Core image](../../WCP360_Build/environments/platform-core/README.md) | Compile PHP inside the selected target userspace and assemble the locked development checkout. |
| [Verification harness](../../WCP360_Build/verification/candidate-platform-core.sh) | Run the installer preparation, checks and synthetic application journey without root. |

The source manifest owns the PHP patch/hash pin. Each image downloads that reviewed upstream archive over HTTPS, checks its SHA-256 before extraction, and builds CLI/FPM against the target's native libraries. It does not reuse a Debian PHP binary on Ubuntu, replace the system PHP family or add a third-party package repository. The image retains build dependencies for development verification; it is not a hardened release image. This procedure verifies the tracked archive hash, not a detached PHP release signature or byte-for-byte reproducibility.

The isolated PostgreSQL service is the same major version for all five application images. This verifies Core against PostgreSQL 17; it does not verify a native PostgreSQL 17 installation, package provider or systemd unit on every target. The workflow resolves base/service images to their registry digests so the executed image identities can be recorded with the source revision.

## Read-only inspection

From the repository root on a supported Linux target:

```sh
bash WCP360_Core/installer/recipes/candidate-install.sh --plan
bash WCP360_Core/installer/recipes/candidate-install.sh --check-runtime
```

The standalone equivalents are:

```sh
php WCP360_Core/installer/preflight/runtime-plan.php
php WCP360_Core/installer/preflight/runtime-readiness.php
```

The plan command identifies the actual OS and describes the source-build profile, fixed CLI/FPM paths and isolated PostgreSQL profile. It requires PHP 8.1 or newer to inspect the configuration. Its output leaves `runtime_validation` as `not_checked`, sets `host_changes` to `false` and never represents the described services as installed. It does not establish actual architecture readiness; the existing `candidate.php --target --dependencies` check covers the Core prerequisites and architecture separately.

The readiness command checks the PHP process that executes it. PHP 8.4 and the required extensions must be present. Set `DATABASE_URL` in the process environment through the local secret mechanism before running it: the command deliberately does not load `.env` or the PHPUnit database override. Do not place real credentials in copied command examples or retained logs.

Readiness opens a bounded, non-persistent, read-only PostgreSQL connection and executes `SHOW server_version_num`. The observed server must be PostgreSQL 17. A URL's `serverVersion=17` hint cannot make another server pass. The JSON report contains individual Boolean checks, the observed major version and `ready`; it does not expose the connection string, credentials or database error text. A missing/unsupported URL, failed connection, wrong server major or missing PHP requirement gives a failing result. `qualified_release` remains `false`, including when all runtime checks pass.

Neither inspection command installs packages, modifies repository trust, creates accounts, runs migrations or starts a service. They accept no simulated OS override.

## Executed development journey

The [candidate workflow](../../.github/workflows/core-executable-candidate.yml) supplies the disposable network, synthetic database credentials and target identity. The application image defaults to UID/GID `10001:10001`. Its trusted checkout is writable by that development account so the real [candidate installation helper](../../WCP360_Core/installer/recipes/candidate-install.sh) can prepare dependencies, assets and the planner without root; installed toolchains remain root-owned.

| Stage | Assertions |
| :--- | :--- |
| Runtime inspection | Actual target identity; reviewed PHP 8.4 build; required extensions; real PostgreSQL 17 readiness. |
| Checkout preparation | Normal non-root installer path, generated local environment, locked Composer installation, Go planner build, frontend assets and migrations. This prepares a checkout, not an operating system. |
| Application checks | Dependency/platform checks, PHPStan and framework-aware analysis regressions, PHPUnit and Symfony configuration/template checks against the dedicated test database. |
| Enrollment | Interactive setup command creates separate synthetic user/admin identities and tenant membership, using password hashing. Fixture state excludes plaintext credentials. |
| Real HTTP | Non-root Nginx and PHP-FPM serve the Core; login and dashboard use their actual forms and session cookies. A forged CSRF submission is refused; replaying a valid idempotency key preserves the same job. |
| Job processing | The real CLI worker consumes the accepted plan job; the job succeeds and the site reaches `PLAN_READY`. Rendering a plan does not activate a hosted workload. |
| Optional plugin | The reference package installs, enables, reports only its authorized tenant summary, disables and uninstalls; Core remains usable without it. |
| Database recovery | PostgreSQL 17 tools dump the synthetic workbench, restore into a separate empty database and compare stable logical snapshots. |

The application journey uses `wcp360_workbench`; the ordinary suite uses the separate `wcp360_test` database. The recovery comparison uses `wcp360_restore_test`. These fixed synthetic databases prevent the harness from being repurposed as a general data-reset tool. Test snapshots contain stable identifiers/counts/state, without passwords, password hashes or connection secrets. A matching snapshot proves that selected candidate application data survived logical restore; it does not prove restoration of hosted files, service configurations, encryption keys, external systems or an entire server.

Never give these images production data, host-service mounts or the Docker socket. The harness starts only its own disposable application processes. It does not launch the privileged broker or change host identities and services.

## Qualification boundaries

| Verification | What it establishes | Separate work |
| :--- | :--- | :--- |
| Five-target native package matrix | Target recognition and real hosted Nginx/FPM behavior using each distribution's own PHP stream. Expected refusal of a non-8.4 Core runtime is a negative assertion. | Executing Symfony on PHP 8.4. |
| Five-target source-runtime Core matrix | The selected Core journey and logical database recovery using native target libraries, a reviewed PHP 8.4 source build and isolated PostgreSQL 17. | Production runtime providers, native host installation and target service lifecycle. |
| Debian 13 privileged VM rehearsal | Explicitly admitted agent/broker effects, systemd supervision, recovery and guest reboot for that VM profile. | Equivalent reviewed adapters and VM evidence on the other four targets. |
| Production release acceptance | Qualified artifacts, complete functional scope, install/upgrade/recovery and required security/review evidence. | No production release is qualified by these development jobs alone. |

The Debian 13 privileged admission guards remain unchanged. The separate FrankenPHP Worker image/probe also keeps its own scope; running Core through PHP-FPM in these five userspaces does not qualify a native FrankenPHP Worker deployment on each OS.

Record the commit, profile, image digests, observed runtime versions, exact job outcomes and recovery comparison for every executed matrix. Pending, skipped or failed jobs remain visible as such. For the wider acceptance criteria, use [supported platforms](SUPPORTED-PLATFORMS.md), [testing](TESTING.md), [Debian VM](DEBIAN-VM.md), [service recovery](SYSTEMD-RECOVERY.md) and [delivery/release acceptance](DELIVERY-AND-RELEASES.md).
