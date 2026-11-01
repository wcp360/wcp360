# IdentityAccess

**Status: a bounded development authentication candidate is implemented. Complete identity/access management and production qualification remain unfinished.**

Local authentication, MFA, credentials, sessions, authorization, revocation and administrator recovery.

Start here when changing: **Identity, roles, session and recovery behavior.**

| Implemented candidate source | Responsibility |
| :--- | :--- |
| [Domain/Principal.php](Domain/Principal.php) | Candidate identity and separate administrator/user roles. |
| [Infrastructure/PrincipalProvider.php](Infrastructure/PrincipalProvider.php) | Context-bound identity lookup and session refresh; inactive accounts cannot retain access. |
| [Application/CandidateMfa.php](Application/CandidateMfa.php) | Encrypted candidate TOTP enrollment, replay prevention, durable attempt limits and single-use recovery codes. |
| [Infrastructure/AdminTwoFactorProvider.php](Infrastructure/AdminTwoFactorProvider.php) | Mandatory administrator second factor through SchebTwoFactorBundle and OTPHP. |
| [Infrastructure/MfaSessionGuard.php](Infrastructure/MfaSessionGuard.php) | Exact administrator Origin, pending-session expiry, factor revision checks, session rotation and fresh-MFA gates. |
| [UI/Http/MfaController.php](UI/Http/MfaController.php) | CSRF-protected enrollment, account-security page and password-confirmed factor reset. |
| [Infrastructure/MfaSchema.php](Infrastructure/MfaSchema.php) | Explicitly candidate-only encrypted factor and hashed recovery-code persistence. |
| [Shared security](../../Shared/Security/README.md) | Runtime separation, session boundaries and bounded session lifetimes. |
| [Security configuration](../../../config/packages/security.yaml) | Password hashing, CSRF-protected form login/logout and login throttling. |

The candidate checks active tenant membership for the single `candidate.hosting.plan` permission through [WorkbenchStore](../Operations/Infrastructure/WorkbenchStore.php). It does not yet provide a general permission/delegation engine. Account and tenant lifecycle, plans and quotas belong to [Tenancy](../Tenancy/README.md).

## Administrator MFA candidate

Administrator password authentication starts a five-minute pending session with administrator roles withheld. Every administrator login must complete the second factor; an account without a factor must first enroll one. User-context password login retains its existing flow. No trusted-device or remember-me bypass is enabled.

| Mechanism | Implemented candidate behavior |
| :--- | :--- |
| TOTP | Six digits, 30-second periods and a one-period clock window in either direction; a durable monotonically increasing counter rejects accepted-code replay. |
| Enrollment | An explicit CSRF-protected POST returns a new seed once; confirmation expires after five minutes. Active seeds are never redisplayed. |
| Attempt limits | Database-backed limits allow five failed checks and five enrollment starts per separate 15-minute window. |
| Seed protection | Sodium authenticated encryption with an independent 32-byte key, supplied as 64 hexadecimal characters in `WCP_MFA_KEY`; the key is not derived from `APP_SECRET`. |
| Backup codes | Ten random 128-bit codes are shown once after enrollment. Only actor-bound keyed hashes are stored; successful use atomically consumes a code and increments the factor revision. |
| Browser boundary | Mutating administrator requests require exact Origin; forms also require CSRF tokens. MFA completion rotates the session identifier. Seed/code responses use the shared no-store policy. |
| Current authority | Inactive accounts, changed factor revisions and unavailable or incorrect encryption keys invalidate administrator access. Recovery or reset invalidates other sessions' old MFA proofs. |
| Freshness | Unsafe authenticated administrator requests require MFA less than five minutes old. The current candidate asks the administrator to sign in again; an inline sensitive-action reauthentication flow remains unfinished. |
| Factor replacement | `/account/security` accepts a fresh-MFA, CSRF-protected POST with the current password. Reset deletes the factor and backup codes, revokes old proofs, ends the current session and requires enrollment at the next login. |

Backup codes replace the **second factor after password authentication**. They do not reset a forgotten password. Keep them outside the hosted server. If the encryption key is lost, restore the correct operator-managed key; no automatic administrative bypass or plaintext seed export is provided. Existing local `.env` files are preserved, so an operator must add `WCP_MFA_KEY` when upgrading an older checkout. A fresh [checkout installation](../../../installer/recipes/README.md) generates independent private application and MFA keys.

[Service regressions](../../../tests/Integration/CandidateMfaTest.php) cover factor storage, replay, attempt windows and recovery. [HTTP journeys](../../../tests/Integration/MfaJourneyTest.php) cover pending-role denial, Origin/CSRF checks, session rotation, recovery/reset and missing-key refusal. Executed outcomes must be tied to the exact source in [draft PR #116](https://github.com/yyogas/wcp360/pull/116); these sources alone are not a passing CI claim.

## Remaining production foundations

The MFA tables and flows are `wcp_candidate_*` development mechanisms, not an accepted canonical production schema. Complete password recovery, passkeys, inline sensitive-action reauthentication, service accounts, reseller delegation, general permission/session administration and production contract integration remain unfinished. Production Unix identities and PostgreSQL grants still require independent separation and qualification. **M1 is not complete.** Candidate code and passing tests do not establish production readiness.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Hosting resource policy stays with its owning domain; reuse the common authorization boundary.

- [Core roadmap and completion estimate](../../../ROADMAP.md)
- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
