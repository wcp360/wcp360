# Domain and unit tests

**Status: test source location reserved; no executable test or passing result is supplied by this README.**

Pure domain invariants, application use cases with explicit adapters, resource bounds and configuration rendering.

Use deterministic clocks/identifiers and bounded synthetic inputs. Keep the test near its owning domain; do not start real services or rely on network access.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
