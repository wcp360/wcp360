# Synthetic test fixtures

**Status: test source location reserved; no executable test or passing result is supplied by this README.**

Shared deterministic settings, input/output examples, fake tenant/resource identities and expected rendered configuration.

Keep each fixture in one canonical location. Use synthetic secret references, document provenance and select the engine/version explicitly; never include real logs, dumps, private keys or customer archives.

Organize implemented fixtures by their owning module/service and document how each consuming test obtains them. Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
