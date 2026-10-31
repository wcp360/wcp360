# Shared security primitives

**Status: shared source location reserved; this directory does not implement or certify a security boundary.**

Reserve shared typed context/secret-reference values, redaction plumbing and narrowly scoped security ports that multiple Core modules actually reuse. Use reviewed framework/native mechanisms when implemented; this directory is not a place for bespoke cryptography, global mutable permission state or a general privileged command runner.

| Ownership | Boundary |
| :--- | :--- |
| IdentityAccess | Authentication, credentials, token/session lifecycle, authorization and revocation policy |
| Owning resource module | Current resource/tenant/delegation checks and domain-specific validation |
| Files | Canonical confined file operations and resource-path authority |
| Platform and agents | Selected host/secret-store adapters and accepted bounded system operations |
| Observability | Audit/diagnostic retention and presentation, using the shared redaction mechanism |

Sharing a primitive does not create a second identity, permission, secret or resource inventory. Production secrets, private keys and service credentials stay outside repository source and test fixtures. A PHP path check cannot replace confinement and safe file handling at the actual operation boundary.

- [Shared foundations](../README.md)
- [Identity and access owner](../../Module/IdentityAccess/README.md)
- [Accepted security specifications](../../../../documentation/specifications/security/)
- [Security test source](../../../tests/Security/README.md)
- [Capability ownership](../../../CAPABILITY-MAP.md)
