# Core verification locations

**Status: the development candidate includes executable PHPUnit unit, integration/functional and plugin-lifecycle suites, plus a separate Panther browser suite. Contract, Security and Installer remain organizational locations; security and installer checks also live in implemented suites and the disposable Linux/VM harnesses. A directory is not evidence of a passing test.**

| Test source | Purpose |
| :--- | :--- |
| [Unit](Unit/README.md) | Pure domain invariants, application use cases with explicit adapters, resource bounds and configuration rendering. |
| [Contract](Contract/README.md) | Serialization, compatibility, operation identifiers, permission namespaces, bounded IPC and approved API/DB adapter behavior. |
| [Integration](Integration/README.md) | Actual persistence, transport, agent and selected Linux-service integration on disposable supported environments. |
| [Security](Security/README.md) | Tenant/surface isolation, authorization/revocation, path confinement, broker boundaries and secret redaction. |
| [Lifecycle](Lifecycle/README.md) | Install, enable, crash, quarantine, disable, update and remove detached optional plugins while retaining Core operations. |
| [Installer](Installer/README.md) | Fresh install, rejected preconditions, interrupted/resumed setup, repair, qualified upgrades and recovery rehearsal. |
| [Browser](Browser/README.md) | Twig/UX workflows, native form behavior, keyboard focus, responsive layout and visible error/loading/empty states. |
| [Fixtures](Fixtures/README.md) | Shared deterministic settings, input/output examples, fake tenant/resource identities and expected rendered configuration. |

Run `composer check` from Core for Symfony-aware PHPStan, analyzer regressions, PHPUnit and framework lints. Run `composer test:browser` in the documented Chromium/WebDriver environment. See the [testing guide](../../documentation/development/TESTING.md) for commands, scopes, fixture safeguards and Linux/VM evidence. The deliberate-error PHPStan fixtures live in `tools/phpstan/fixtures`, outside application test discovery.

Organize implemented test files by the same domain/service names as the source. Framework configuration, deterministic inputs and the accepted contract/requirement must accompany the implementation. Keep the shared synthetic fixture source in Fixtures instead of copying test data across suite directories.

Configuration-template checks identify the service, package/version, resource scope and expected rendering. Supported-target integration also executes actual service validation, failed-apply cases, concurrent-change/ownership checks and observed post-apply health on a disposable environment. A syntax check or source directory is not service acceptance.

Record the exact candidate SHA, environment, assertion and result. Browser tests and security/integration tests have different scopes; document missing execution instead of substituting a VM adapter or screenshot. Generated reports/caches belong outside source.

- [Source navigation](../NAVIGATION.md)
- [Service resource lifecycle](../resources/services/README.md)
- [Core acceptance journey](../../documentation/development/CORE-ENGINE.md)
