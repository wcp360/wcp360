# Installation and upgrade tests

**Status: test source location reserved; no executable test or passing result is supplied by this README.**

Fresh install, rejected preconditions, interrupted/resumed setup, repair, qualified upgrades and recovery rehearsal.

Use disposable machines or images with explicit ownership. Separate schema migrations from host/package changes and verify interrupted steps do not duplicate destructive effects.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
