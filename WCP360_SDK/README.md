# WCP360 SDK

**Status: public integration boundary reserved; no executable SDK/client or production manifest schema is supplied yet.**

The SDK gives detached plugins a supported interface without importing Core internals. Core, SDK and plugins have separate versions and compatibility matrices.

| Directory | Intended contents |
| :--- | :--- |
| [php](php/README.md) | Typed public client/value objects and error handling derived from accepted Core contracts. |
| [contracts](contracts/README.md) | Validated public schemas/compatibility metadata or generation inputs with exact canonical provenance. |
| [testing](testing/README.md) | Synthetic contract fixtures and test-host tooling with explicit limitations. |

A PHP application consumes a qualified SDK library through its own Composer graph; installing a plugin never changes the Core dependency lock. Package/runtime locks belong to their applications. PHP client code does not connect directly to Core tables, shared privileged sockets or arbitrary root execution.

The SDK must preserve typed identifier namespaces, resource/tenant/surface context, revocation, bounded requests, safe retries, UNKNOWN_OUTCOME/reconciliation, redaction and compatibility. It must not invent routes, endpoint schemas or permissions missing from owner-approved contracts.

Generated bindings require a pinned generator, source artifacts and reviewed regeneration. Historical mirror exports are not a substitute for current specifications.

- [Accepted specifications](../documentation/specifications/README.md)
- [Manifest and gate](../documentation/manifests/WCP360-BM-0001.md)
- [Development strategy](../documentation/development/README.md)
- [Plugin lifecycle](../documentation/development/PLUGIN-LIFECYCLE.md)
