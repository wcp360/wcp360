# Plugin and Core continuity tests

**Status: PluginLifecycleTest exercises the detached reference-report candidate and Core continuity through the implemented lifecycle transitions. The full plugin catalogue and production lifecycle remain unqualified; additional acceptance scenarios below must accompany each implemented capability.**

Install, enable, crash, quarantine, disable, update and remove detached optional plugins while retaining Core operations.

Prove the Core works with zero optional plugins. Exercise draining, credential/contribution revocation, active-engine constraints and explicitly scoped data retention.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
