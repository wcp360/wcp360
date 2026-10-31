# Core verification locations

**Status: test layout documented; no executable Core test suite is created by these directories.** Add suites/fixtures when actual implementation and accepted assertions exist.

| Intended test location | Purpose |
| :--- | :--- |
| tests/Unit/<Domain>/ | Pure domain rules, use cases, configuration rendering and bounded validation |
| tests/Contract/ | Approved API/DB/permission/IPC compatibility and adapter serialization/failure semantics |
| tests/Integration/<Domain>/ | Persistence, approved service adapters, durable operations and observed system results |
| tests/Security/ | Tenant/surface negatives, path confinement, privilege boundaries and secret redaction |
| tests/Lifecycle/ | Plugin install/enable/crash/disable/update/remove and Core continuity |
| tests/Installer/ | Fresh installation, invalid preconditions, interruption/resume, upgrade and recovery |
| tests/Browser/ | Twig/UX workflows, responsive layout, keyboard/focus and accessibility |
| tests/Fixtures/ | One source of synthetic configuration inputs/expected output; no real credentials or customer content |

Configuration-template tests should identify the owning service, package/version, resource scope and expected rendered result. Target integration checks also execute actual service validation, failed-apply cases, concurrency/ownership checks and observed post-apply health on a disposable supported environment. A syntax check or README is not service acceptance.

Keep the file path and domain naming parallel to the actual source. Record candidate SHA, environment, assertion and result. These reserved locations do not claim executed tests, production safety or independent review.

- [Source navigation](../NAVIGATION.md)
- [Service resources and lifecycle](../resources/services/README.md)
- [Core acceptance journey](../../documentation/development/CORE-ENGINE.md)
