# Security boundary tests

**Status: test source location reserved; no executable test or passing result is supplied by this README.**

Tenant/surface isolation, authorization/revocation, path confinement, broker boundaries and secret redaction.

Use synthetic identities and disposable resources. Include denied operations and preserve separate ADMIN/USER/worker contexts; never use production credentials or customer data.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
