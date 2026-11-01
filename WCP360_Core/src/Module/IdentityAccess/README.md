# IdentityAccess

**Status: a bounded development authentication candidate is implemented. Complete identity/access management and production qualification remain unfinished.**

Local authentication, MFA, credentials, sessions, authorization, revocation and administrator recovery.

Start here when changing: **Identity, roles, session and recovery behavior.**

| Implemented candidate source | Responsibility |
| :--- | :--- |
| [Domain/Principal.php](Domain/Principal.php) | Candidate identity and separate administrator/user roles. |
| [Infrastructure/PrincipalProvider.php](Infrastructure/PrincipalProvider.php) | Context-bound identity lookup and session refresh; inactive accounts cannot retain access. |
| [Application/CandidateAuthorization.php](Application/CandidateAuthorization.php) | One current-database rule for candidate tenant permission checks and visibility; current active administrator checks. |
| [Application/CandidatePermissionAdministration.php](Application/CandidatePermissionAdministration.php) | Bounded membership listing and transactional, audited administrator access blocks; no grant API. |
| [Infrastructure/PermissionDenialSchema.php](Infrastructure/PermissionDenialSchema.php) | Frozen candidate-only denial schema used by its dedicated migration. |
| [UI/Http/PermissionController.php](UI/Http/PermissionController.php) | Administrator membership list and actor/tenant-bound CSRF-protected block changes. |
| [UI/Console/CheckAccessCommand.php](UI/Console/CheckAccessCommand.php) | Read-only worker/setup CLI decision for the single candidate permission. |
| [Application/CandidateMfa.php](Application/CandidateMfa.php) | Encrypted candidate TOTP enrollment, replay prevention, durable attempt limits and single-use recovery codes. |
| [Infrastructure/AdminTwoFactorProvider.php](Infrastructure/AdminTwoFactorProvider.php) | Mandatory administrator second factor through SchebTwoFactorBundle and OTPHP. |
| [Infrastructure/MfaSessionGuard.php](Infrastructure/MfaSessionGuard.php) | Exact administrator Origin, pending-session expiry, factor revision checks, session rotation and fresh-MFA gates. |
| [UI/Http/MfaController.php](UI/Http/MfaController.php) | CSRF-protected enrollment, account-security page and password-confirmed factor reset. |
| [Infrastructure/MfaSchema.php](Infrastructure/MfaSchema.php) | Explicitly candidate-only encrypted factor and hashed recovery-code persistence. |
| [Shared security](../../Shared/Security/README.md) | Runtime separation, session boundaries and bounded session lifetimes. |
| [Security configuration](../../../config/packages/security.yaml) | Password hashing, CSRF-protected form login/logout and login throttling. |

The candidate checks current tenant authority for the single `candidate.hosting.plan` permission through `CandidateAuthorization`; [WorkbenchStore](../Operations/Infrastructure/WorkbenchStore.php) delegates its existing authorization and visibility facade to that service. It does not yet provide a general permission/delegation engine. Account and tenant lifecycle, plans and quotas belong to [Tenancy](../Tenancy/README.md).

## Candidate authorization and access blocks

The shared decision allows `candidate.hosting.plan` only when the actor is an active USER account, holds the exact permission membership for the requested tenant and has no matching explicit denial. Unknown permissions, malformed identifiers, inactive users and mismatched tenants fail closed. Administrator context does not supply tenant authority. Visibility and execution use the same database rule; queued hosting plans recheck current authority when processed.

`CandidateAuthorization` exposes `allowsTenantPermission`, `requireTenantPermission`, `requireAdmin` and `tenantsForActor`. The existing `WorkbenchStore` constructor and methods remain available to their callers. These services evaluate candidate database authority; browser MFA/session checks remain enforced at the HTTP boundary rather than being inferred from an actor UUID.

| Surface | Implemented candidate behavior |
| :--- | :--- |
| Administrator listing | `GET /workbench/access`, after completed administrator MFA. Lists 100 membership rows per page with stable ordering; page values are bounded to 1–10,000. |
| Access block | `POST /workbench/access/{actor}/{tenant}/denial` accepts `denied=1` or `denied=0` and an actor/tenant-bound CSRF token. Mutations also require exact Origin and an administrator MFA proof less than five minutes old. |
| Write authority | Every change rechecks the current active administrator in the database and validates the USER target and tenant inside a transaction. Repeating the same state creates no duplicate transition. |
| Audit | Actual block/restoration transitions append `candidate.access.denied` or `candidate.access.restored`, recording the administrator, tenant and target user in the same transaction. A failed audit rolls back the access change. |
| Persistence | Blocks belong to the user/tenant pair and permission, independently of a membership row. Deleting and later regranting membership preserves the block. Removing a block never creates membership or activates an account. |
| Worker/setup diagnosis | `wcp:candidate:access:check actor tenant [--permission=candidate.hosting.plan]` reads current authority without changing data. Other runtime contexts are refused. |

The CLI emits JSON with schema `wcp360.candidate.access-check/1`, `allowed` and `production: false`. Exit status is `0` for allowed, `1` for denied and `2` for a forbidden runtime context. A successful candidate decision does not authorize a production operation or invoke a system action.

Before using this slice on an older checkout, apply [Version20261101000200](../../../migrations/Version20261101000200.php) through the existing candidate migration procedure. It creates `wcp_candidate_permission_denials`; the new authority checks depend on that table. Existing installation journals and migration uncertainty must still follow the [recovery rules](../../../installer/recovery/README.md). Do not remove a block table or alter accepted migration history to bypass a denial.

[Service tests](../../../tests/Integration/CandidateAuthorizationTest.php), [HTTP journeys](../../../tests/Integration/PermissionAdministrationTest.php) and [CLI tests](../../../tests/Integration/CandidateAccessCommandTest.php) define positive/refusal coverage. Full current-source execution is tracked in [draft PR #116](https://github.com/yyogas/wcp360/pull/116); test source alone is not passing evidence.

This candidate does not implement or grant any of the 82 canonical permissions. Positive overrides, roles/delegation, service accounts, general policy composition and production Unix/PostgreSQL separation remain unfinished. [AUDIT-WCP-0118](../../../../documentation/governance/audits/AUDIT-WCP-0100/CLOSURE-MATRIX.md#audit-wcp-0118--permission-priority-vs-security-intersection) remains open and authoritative for the unresolved canonical composition; these candidate blocks do not settle that audit or amend accepted contracts.

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

The MFA and access-block tables and flows are `wcp_candidate_*` development mechanisms, not an accepted canonical production schema. Complete password recovery, passkeys, inline sensitive-action reauthentication, service accounts, reseller delegation, general permission/session administration and production contract integration remain unfinished. Production Unix identities and PostgreSQL grants still require independent separation and qualification. **M1 is not complete.** Candidate code and passing tests do not establish production readiness.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Hosting resource policy stays with its owning domain; reuse the common authorization boundary.

- [Core roadmap and completion estimate](../../../ROADMAP.md)
- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
