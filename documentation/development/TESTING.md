# Testing and static analysis

The [engineering quality and security catalogue](QUALITY-SECURITY-STACK.md) covers PHP/Symfony, Go, Python, Shell, Linux hardening, HTTP/IPC/PostgreSQL, browsers and software delivery. The [security test matrix](SECURITY-TEST-MATRIX.md) defines required positive/negative cases and revision-bound evidence. Both distinguish existing controls from configured or conditional additions; they do not assert that every listed tool has run.

The executable candidate uses locked development dependencies: PHPUnit 12, Symfony Test Pack, PHPStan 2 at level 8 with the PHPStan Symfony extension, and Symfony Panther 2.4. Exact patch versions are recorded in WCP360_Core/composer.lock. No Symfony Flex plugin is needed to run this project's explicit configuration.

## Commands

From WCP360_Core, after composer install and building the Go planner:

~~~sh
composer analyse
composer test:analysis
composer test
composer check
composer test:browser
~~~

composer check prepares the Symfony analysis container, runs PHPStan and its rule regressions before PHPUnit, then runs Symfony container/YAML/Twig checks. Browser tests are separate because Chromium/ChromeDriver must be installed. Their absence is a failure of the browser job, not a silent passing skip.

| Layer | Scope | Evidence |
| :--- | :--- | :--- |
| Unit | Pure policies, runtime/session boundaries and protocol validation. | PHPUnit and Go tests. |
| Integration / functional | KernelTestCase and WebTestCase with real persistence, authentication, CSRF, tenant checks, durable audit and activation transitions. | PostgreSQL 17 in Linux CI; SQLite for portable local tests. |
| Static | All implemented Core src and migrations, analysis tooling, candidate SDK decoder and detached reference report. Level 8, PHP 8.4, Symfony container/Console rules, no baseline/ignore list. | composer analyse and composer test:analysis, before application execution tests in CI. |
| Browser | Login, global-default/site selector presentation, Stimulus duplicate-submit protection and Turbo navigation with compiled assets. | Panther and real Chromium/WebDriver in a non-root disposable test container. |
| Linux effects | Separate unprivileged agent and root broker create two PHP-FPM sites, with socket-peer checks, fixed operations and outcome inspection. | Isolated provisioning smoke test; no host mounts. |
| Runtime profiles | Real Nginx/PHP-FPM and immutable-image FrankenPHP Worker, including request cleanup and recycling. | Separate smoke probes. |

The standalone File Manager and proposed catalog packages are not claimed as integrated, level-8-checked Core modules. Each additional package needs its own dependency graph, analysis configuration and execution tests before integration. Planned features such as file-manager context menus and themes receive browser tests when implemented; placeholder assertions would not prove their behavior.

## Symfony-aware analysis

Use `composer analyse`, which first boots the actual Kernel in `test/user` debug mode and checks the generated container XML path. Symfony invalidates the container when tracked source/configuration resources change; generated XML stays in ignored `var/cache`. The extension and framework rules are explicitly included in `phpstan.neon`; no Composer extension-installer plugin is enabled.

The dedicated bootstrap in `WCP360_Core/tools/phpstan` supplies synthetic settings directly, without loading an operator's `.env`. It forces an in-memory SQLite database, a local origin and disabled provisioning/Worker-plan gates. The required `ext-pdo_sqlite` is declared in Composer development requirements, so a missing analysis driver is detected during dependency/platform checks. PHPStan inspects Console definitions through a dedicated application loader; it does not execute enrollment, job or activation commands. Static analysis covers the current service graph; runtime context and authorization behavior still require the existing functional tests.

`composer test:analysis` prepares the same container and analyzes explicit fixture paths separately from the real source scope. It requires these results:

| Fixture | Required result |
| :--- | :--- |
| Public router service | Accepted with its inferred RouterInterface type. |
| Missing service | Rejected by `symfonyContainer.serviceNotFound`. |
| Private application service | Rejected by `symfonyContainer.privateService`. |
| Defined Console option | Accepted. |
| Unknown Console option | Rejected by `symfonyConsole.optionNotFound`. |

The runner checks the exact diagnostic identifiers and rejects missing or additional errors. Deliberately invalid fixtures are never loaded by the hosting application and are excluded from normal analysis paths. The Linux PostgreSQL job runs both analysis commands before PHPUnit; static analysis itself never uses that PostgreSQL database. The workflow runs for relevant pull-request changes and pushes to main, avoiding duplicate push/pull-request executions on the candidate branch.

## Browser setup and data

The Linux browser image installs Chromium and its matching distribution ChromeDriver. It uses an application-specific SQLite fixture in Core var/panther.db and a localhost-only test origin. The fixture Kernel uses test; PANTHER_APP_ENV=dev starts the HTTP application with native session cookies, avoiding the mock-file storage used by KernelBrowser. Both processes share only the synthetic browser database. It exercises a real HTTP server and JavaScript; WebTestCase remains useful for faster request-level tests.

PANTHER_NO_SANDBOX is scoped to Chromium inside the disposable non-root CI container. It does not disable tenant isolation or AlmaLinux SELinux. Production hosting services never consume this browser setting.

The ordinary suite uses a dedicated wcp360_test database or SQLite. Fixture cleanup refuses any other PostgreSQL database. Console hidden-password interaction is skipped on Windows and exercised on Linux. Do not infer target-platform qualification from Windows/SQLite results.

## PestPHP

Pest is an optional alternative syntax over PHPUnit and is not installed in the default dependency graph. Review the selected Pest major's PHP/PHPUnit constraints before adopting it; current/latest versions cannot be assumed compatible with this lock. Keep existing PHPUnit assertions and the Symfony integration/browser suites working. No universal compatibility promise or automatic rewrite is made.

Static analysis is a type/correctness control, not a replacement for a security audit, adversarial testing or independent review. Run outcomes are bound to the exact commit and environment in [PR #116](https://github.com/yyogas/wcp360/pull/116).

References: [Symfony testing](https://symfony.com/doc/7.4/testing.html), [PHPStan levels](https://phpstan.org/user-guide/rule-levels), [Panther](https://symfony.com/doc/current/testing/end_to_end.html), [Pest installation](https://pestphp.com/docs/installation).

## Clean Debian VM and hosted application

The [disposable VM rehearsal](DEBIAN-VM.md) runs the shared provisioning and packaged-application checks against Debian 13 distribution PHP-FPM. Its explicit environment gates have PHP and Go regression coverage for stale boot IDs, wrong distributions, ordinary VM identifiers and writable/tenant-owned markers. This is additional to the container, PostgreSQL, Panther and FrankenPHP jobs; consult the exact workflow run for execution evidence.

## Supervised recovery

The [systemd recovery rehearsal](SYSTEMD-RECOVERY.md) adds strict Go journal/managed-file regressions and tests actual candidate service restart, owned PHP-FPM child failure, inconsistent-state refusal and full guest reboot. Application sessions, counters and Unix identities must survive without replaying physical provisioning. The workflow result applies only to its exact source head.

## Native Go fuzzing

From `WCP360_Core/agents/wcpd/candidate/provisioning`:

~~~sh
go test ./...
go vet ./...
go test -run='^$' -fuzz='^FuzzProvisionEnvelope$' -fuzztime=10s -parallel=2 .
go test -run='^$' -fuzz='^FuzzActivationJournal$' -fuzztime=10s -parallel=2 .
~~~

These targets exercise bounded untrusted-message and journal parsing, canonical round trips and immutable identity/state bindings. They call pure parsers/renderers only; they never launch a broker, create users, mutate services or access customer files. Seed corpora also run with ordinary `go test`. Linux candidate image builds configure ten seconds per target with two fuzz workers; exact execution results belong to that build's source SHA.

Use longer bounded campaigns in isolated test workers when qualifying a parser change. Retain any minimal failing corpus as a regression fixture after checking it for secrets. Go's fuzz cache is local build state; it is not a release artifact. Fuzzing does not replace the real peer-credential, UID/GID, filesystem, process-crash and VM reboot tests.

## Dependency update configuration

`.github/dependabot.yml` configures reviewed weekly updates for Core Composer dependencies, both candidate Go modules, GitHub Actions and candidate container definitions. It configures no automatic merge. Dependabot becomes active only when its configuration is available on the default branch and the repository service supports it; a file on the candidate branch is not evidence that update jobs ran.
