# Shared security primitives

**Status: bounded candidate runtime/session primitives are implemented. Complete production boundaries and qualification remain unfinished.**

Reserve shared typed context/secret-reference values, redaction plumbing and narrowly scoped security ports that multiple Core modules actually reuse. Use reviewed framework/native mechanisms when implemented; this directory is not a place for bespoke cryptography, global mutable permission state or a general privileged command runner.

| Ownership | Boundary |
| :--- | :--- |
| IdentityAccess | Authentication, credentials, token/session lifecycle, authorization and revocation policy |
| Owning resource module | Current resource/tenant/delegation checks and domain-specific validation |
| Files | Canonical confined file operations and resource-path authority |
| Platform and agents | Selected host/secret-store adapters and accepted bounded system operations |
| Observability | Audit/diagnostic retention and presentation, using the shared redaction mechanism |

Sharing a primitive does not create a second identity, permission, secret or resource inventory. Production secrets, private keys and service credentials stay outside repository source and test fixtures. A PHP path check cannot replace confinement and safe file handling at the actual operation boundary.

## Candidate browser-session boundary

[SessionBoundary](SessionBoundary.php) delegates durable admission and revocation policy to IdentityAccess [CandidateSessions](../../Module/IdentityAccess/Application/CandidateSessions.php). It checks registered cookies before the firewall, checks current authority before unsafe controller execution and MFA attempts, registers the final rotated identifier after primary login, upgrades the pending ADMIN registration after MFA and retires it before Symfony clears a POST logout. Missing or legacy registrations are never automatically adopted.

MFA is optional per administrator account. Password-only ADMIN sessions are admitted at strength 1 while MFA is off, with a recent password proof for mutations. Enabled-factor logins and voluntary enrollment withhold roles until real MFA upgrades the pending registration to strength 2. The guard reads current database factor state/revision; enabling/disabling a factor invalidates older authentication. Missing factor encryption configuration never silently bypasses enabled MFA.

[SessionLifetimePolicy](SessionLifetimePolicy.php) remains unchanged and supplies USER idle/absolute limits of 30 minutes/12 hours and ADMIN limits of 15 minutes/8 hours. Browser-session admission additionally refuses future last-activity timestamps (matching the DB registration's existing fail-closed clock check), rather than allowing a malformed heartbeat to extend the idle window. MFA rotation preserves the registration's original absolute deadline. The registry stores only a SHA-256 binding and server-generated selector alongside metadata; native Symfony session payloads remain in server-side files. It neither implements canonical production session storage nor establishes separate PostgreSQL grants or Unix identities.

Browser-admission time is supplied by the server-owned PSR clock, defaulting to `NativeClock`, consistently with the existing IdentityAccess clock abstraction. Request attributes cannot select or replace the clock. Login/MFA proof timestamps and lifetime limits are unchanged. [BrowserSessionLifetimeTest](../../../tests/Integration/BrowserSessionLifetimeTest.php) freezes only this admission clock after a real candidate login and keeps the database registration live, to distinguish the browser gate from registry expiry. Its cases cover both surfaces at one second before and exactly at idle/absolute deadlines, expired POST refusal and old-cookie replay rejection. No sleeps or database schema changes are required.

[RequestBoundaryLifetimeTest](../../../tests/Unit/RequestBoundaryLifetimeTest.php) separately exercises reused nonce/reference issuers, live-request cross-contamination refusal and collection of completed cyclic Request objects while the issuers stay alive. These are source-level regression definitions, not a claim of successful execution or a substitute for the dedicated real persistent-worker isolation probe. Exact-source CI and owner acceptance must be recorded separately in Core M1 issue #130 and PR #127.

The owning module documents [session inventory, password-confirmed revocation, migration and limitations](../../Module/IdentityAccess/README.md#candidate-session-inventory-and-revocation). Existing checkouts require the new migration and a new login. Revocation gates subsequent admitted requests and the implemented mutation/MFA boundaries; general in-flight effect fencing remains unfinished.

## Runtime identity observation

The standalone [runtime identity inspector](../../../installer/preflight/README.md#runtime-identity-inspection) compares live Linux credentials with the accepted identity model. It is not called implicitly by [RuntimeContext](RuntimeContext.php): declaring a development context still does not establish an effective Unix identity. Profile matches do not authorize effects or qualify service, filesystem, PostgreSQL or minimal-root separation. Complete production runtime admission and identity/grant isolation remain M1 work.

- [Shared foundations](../README.md)
- [Identity and access owner](../../Module/IdentityAccess/README.md)
- [Accepted security specifications](../../../../documentation/specifications/security/)
- [Security test source](../../../tests/Security/README.md)
- [Capability ownership](../../../CAPABILITY-MAP.md)
