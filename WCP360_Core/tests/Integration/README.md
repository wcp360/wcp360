# Integration and supported-target tests

**Status: executable PHPUnit functional/integration suites cover the candidate workbench, enrollment, application-server policy/UI and isolated-activation state transitions. SQLite supports portable checks; Linux CI also exercises real PostgreSQL. Broker peer credentials, physical services and reboot recovery are checked by separate disposable Linux/VM harnesses.**

Actual persistence, transport, agent and selected Linux-service integration on disposable supported environments.

Record the exact candidate, package versions and observed outcome. Cover failed validation/apply, concurrency and uncertain outcomes; a rendered file alone is not service acceptance.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
