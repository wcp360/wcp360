# Browser and accessibility tests

**Status: test source location reserved; no executable test or passing result is supplied by this README.**

Twig/UX workflows, native form behavior, keyboard focus, responsive layout and visible error/loading/empty states.

Run against isolated synthetic accounts and explicit ADMIN/USER origins. Mark unavailable browser execution as unverified; DOM adapters and screenshots do not prove server authorization.

Organize implemented files by their owning module or service and use shared inputs from [Fixtures](../Fixtures/README.md). Test framework/bootstrap, target setup and exact assertions must accompany the reviewed implementation. Generated reports, caches and captured runtime state stay outside versioned fixtures.

- [Test-suite map](../README.md)
- [Core navigation](../../NAVIGATION.md)
- [Core acceptance](../../../documentation/development/CORE-ENGINE.md)
