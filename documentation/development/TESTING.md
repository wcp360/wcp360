# Testing and static analysis

The executable candidate uses locked development dependencies: PHPUnit 12, Symfony Test Pack, PHPStan 2 at level 8 and Symfony Panther 2.4. Exact patch versions are recorded in WCP360_Core/composer.lock. No Symfony Flex plugin is needed to run this project's explicit configuration.

## Commands

From WCP360_Core, after composer install and building the Go planner:

~~~sh
composer analyse
composer test
composer check
composer test:browser
~~~

composer check runs PHPStan before PHPUnit, then Symfony container/YAML/Twig checks. Browser tests are separate because Chromium/ChromeDriver must be installed. Their absence is a failure of the browser job, not a silent passing skip.

| Layer | Scope | Evidence |
| :--- | :--- | :--- |
| Unit | Pure policies, runtime/session boundaries and protocol validation. | PHPUnit and Go tests. |
| Integration / functional | KernelTestCase and WebTestCase with real persistence, authentication, CSRF, tenant checks, durable audit and activation transitions. | PostgreSQL 17 in Linux CI; SQLite for portable local tests. |
| Static | All implemented Core src and migrations, candidate SDK decoder and detached reference report. Level 8, PHP 8.4, no baseline/ignore list. | composer analyse, before application execution tests in CI. |
| Browser | Login, global-default/site selector presentation, Stimulus duplicate-submit protection and Turbo navigation with compiled assets. | Panther and real Chromium/WebDriver in a non-root disposable test container. |
| Linux effects | Separate unprivileged agent and root broker create two PHP-FPM sites, with socket-peer checks, fixed operations and outcome inspection. | Isolated provisioning smoke test; no host mounts. |
| Runtime profiles | Real Nginx/PHP-FPM and immutable-image FrankenPHP Worker, including request cleanup and recycling. | Separate smoke probes. |

The standalone File Manager and proposed catalog packages are not claimed as integrated, level-8-checked Core modules. Each additional package needs its own dependency graph, analysis configuration and execution tests before integration. Planned features such as file-manager context menus and themes receive browser tests when implemented; placeholder assertions would not prove their behavior.

## Browser setup and data

The Linux browser image installs Chromium and its matching distribution ChromeDriver. It uses an application-specific SQLite fixture in Core var/panther.db and a localhost-only test origin. The fixture Kernel uses test; PANTHER_APP_ENV=dev starts the HTTP application with native session cookies, avoiding the mock-file storage used by KernelBrowser. Both processes share only the synthetic browser database. It exercises a real HTTP server and JavaScript; WebTestCase remains useful for faster request-level tests.

PANTHER_NO_SANDBOX is scoped to Chromium inside the disposable non-root CI container. It does not disable tenant isolation or AlmaLinux SELinux. Production hosting services never consume this browser setting.

The ordinary suite uses a dedicated wcp360_test database or SQLite. Fixture cleanup refuses any other PostgreSQL database. Console hidden-password interaction is skipped on Windows and exercised on Linux. Do not infer target-platform qualification from Windows/SQLite results.

## PestPHP

Pest is an optional alternative syntax over PHPUnit and is not installed in the default dependency graph. Review the selected Pest major's PHP/PHPUnit constraints before adopting it; current/latest versions cannot be assumed compatible with this lock. Keep existing PHPUnit assertions and the Symfony integration/browser suites working. No universal compatibility promise or automatic rewrite is made.

Static analysis is a type/correctness control, not a replacement for a security audit, adversarial testing or independent review. Run outcomes are bound to the exact commit and environment in [PR #116](https://github.com/yyogas/wcp360/pull/116).

References: [Symfony testing](https://symfony.com/doc/7.4/testing.html), [PHPStan levels](https://phpstan.org/user-guide/rule-levels), [Panther](https://symfony.com/doc/current/testing/end_to_end.html), [Pest installation](https://pestphp.com/docs/installation).
