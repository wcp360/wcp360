# Domain and unit tests

**Status: executable PHPUnit tests cover runtime contexts, principal/session policy, disposable-execution guards and packaged bootstrap behavior. Results depend on the exact revision and environment; this README does not certify a production release.**

Pure domain invariants, application use cases with explicit adapters, resource bounds and configuration rendering.

Use deterministic clocks/identifiers and bounded synthetic inputs. Keep the test near its owning domain; do not start real services or rely on network access.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
