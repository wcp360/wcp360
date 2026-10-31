# Accepted-contract tests

**Status: test source location reserved; no executable test or passing result is supplied by this README.**

Serialization, compatibility, operation identifiers, permission namespaces, bounded IPC and approved API/DB adapter behavior.

Derive fixtures and assertions from accepted owner contracts. A directory must not introduce a new production schema, endpoint, capability or root command.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
