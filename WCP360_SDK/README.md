# WCP360 SDK

**Status: the Core integration boundary and production extension manifest remain reserved/unqualified. Separate Registry client, offline artifact verifier and local pairing client candidates (0.1.0) are available for isolated evaluation.**

The SDK gives detached plugins a supported interface without importing Core internals. Core, SDK and plugins have separate versions and compatibility matrices.

The [Registry candidate client](php/registry-client/README.md) consumes public editorial metadata from the Grav vendor website. Its pinned contract is explicitly a candidate, independent of the accepted Core API/IPC/permission specifications. It does not integrate with Core startup, install packages, authorize downloads, validate licenses or mutate a client machine. Local provider/consumer and HTTP tests are not Core production qualification.

The [offline artifact staging verifier](php/artifact-verifier/README.md) checks DSSE/Ed25519 signatures, locally trusted publisher scopes, identity, expiration, ZIP hashes and bounded archive content. Its protected build-side signer stays outside the website. The vendor Registry uses a pinned verifier copy for private admission, without public downloads, extraction or installation. This is not a production extension manifest, TUF update implementation or Core integration.

| Directory | Intended contents |
| :--- | :--- |
| [php](php/README.md) | Core client boundary reserved; separate Registry client and offline artifact verifier candidates available. |
| [contracts](contracts/README.md) | Validated public schemas/compatibility metadata or generation inputs with exact canonical provenance. |
| [testing](testing/README.md) | Synthetic contract fixtures and test-host tooling with explicit limitations. |

A PHP application consumes a qualified SDK library through its own Composer graph; installing a plugin never changes the Core dependency lock. Package/runtime locks belong to their applications. PHP client code does not connect directly to Core tables, shared privileged sockets or arbitrary root execution.

The SDK must preserve typed identifier namespaces, resource/tenant/surface context, revocation, bounded requests, safe retries, UNKNOWN_OUTCOME/reconciliation, redaction and compatibility. It must not invent routes, endpoint schemas or permissions missing from owner-approved contracts.

The [pairing client candidate](php/pairing-client/README.md) and standalone CLI consume an account-created one-time code against Verify License in direct loopback development. They prove key possession, preserve a pending request for exact recovery and check revocation. This does not integrate with Core startup, grant licenses or qualify an Internet protocol. Server HTTPS, key rotation / recovery, Linux deployment and independent review remain open.

Generated bindings require a pinned generator, source artifacts and reviewed regeneration. Historical mirror exports are not a substitute for current specifications.

- [Specification entry points](../documentation/contracts/README.md)
- [Manifest and gate](../documentation/manifests/WCP360-BM-0001.md)
- [Development strategy](../documentation/development/README.md)
- [Plugin lifecycle](../documentation/development/PLUGIN-LIFECYCLE.md)
