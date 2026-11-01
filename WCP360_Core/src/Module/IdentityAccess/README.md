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
| [Infrastructure/AdminTwoFactorProvider.php](Infrastructure/AdminTwoFactorProvider.php) | Account-enabled MFA or explicitly requested enrollment through SchebTwoFactorBundle and OTPHP. |
| [Infrastructure/MfaSessionGuard.php](Infrastructure/MfaSessionGuard.php) | Exact administrator Origin, pending-session expiry, factor revision checks and recent password/MFA gates according to the account mode. |
| [Infrastructure/CurrentPasswordConfirmation.php](Infrastructure/CurrentPasswordConfirmation.php) | Shared Origin/CSRF/current-password verification and durable five-attempt budget for MFA settings and session revocation. |
| [UI/Http/MfaController.php](UI/Http/MfaController.php) | CSRF-protected enrollment, account-security page and password-confirmed factor reset. |
| [UI/Http/ReauthenticationController.php](UI/Http/ReauthenticationController.php) | Administrator confirmation form using the existing password and MFA authenticators; no proof renewal on GET. |
| [Infrastructure/MfaSchema.php](Infrastructure/MfaSchema.php) | Explicitly candidate-only encrypted factor and hashed recovery-code persistence. |
| [Domain/NewAccountPassword.php](Domain/NewAccountPassword.php) | Shared enrollment/change policy: at least 14 UTF-8 characters, at most 256 bytes; passphrases accepted without trimming or composition rules. |
| [Application/ChangeOwnPassword.php](Application/ChangeOwnPassword.php) | Own-account credential replacement, current-state checks, context session revocation and audit in one transaction; shared database candidate only. |
| [UI/Http/PasswordController.php](UI/Http/PasswordController.php) | Username-account password form with exact Origin, CSRF and the shared current-password confirmation budget. |
| [Infrastructure/PasswordChangeErrors.php](Infrastructure/PasswordChangeErrors.php) | Redacted password-route exception responses with preserved refusal statuses and headers, including before-controller errors. |
| [Application/DisableAdministratorMfa.php](Application/DisableAdministratorMfa.php) | Atomic factor/backup-code removal and revocation of all ADMIN sessions for the account. |
| [Application/CandidateSessions.php](Application/CandidateSessions.php) | Current-session admission, bounded own-account inventory, durable revocation, MFA strength upgrade and password-confirmation attempt limits. |
| [Infrastructure/SessionSchema.php](Infrastructure/SessionSchema.php) | Separate candidate USER/ADMIN registries containing session metadata and SHA-256 bindings. |
| [UI/Http/SessionController.php](UI/Http/SessionController.php) | Own-account listing and password-confirmed, CSRF-protected single/all-session revocation in the current browser context. |
| [Shared security](../../Shared/Security/README.md) | Runtime separation, session boundaries and bounded session lifetimes. |
| [Security configuration](../../../config/packages/security.yaml) | Password hashing, CSRF-protected form login/logout and login throttling. |

The candidate checks current tenant authority for the single `candidate.hosting.plan` permission through `CandidateAuthorization`; [WorkbenchStore](../Operations/Infrastructure/WorkbenchStore.php) delegates its existing authorization and visibility facade to that service. It does not yet provide a general permission/delegation engine. Account and tenant lifecycle, plans and quotas belong to [Tenancy](../Tenancy/README.md).

## Username authentication

The USER surface is the **hosting workspace**. Both USER and ADMIN sign in with a canonical username: 3–32 lowercase ASCII letters, digits or underscores, beginning with a letter. The contact email remains a separate field and is never a login alias. Username lookup is parameterized and bound to the configured runtime context; a USER username cannot select the ADMIN identity. The database enforces format and uniqueness within each context. Existing login throttling, password hashing, CSRF, Origin checks and optional account MFA continue to apply.

The interactive `wcp:candidate:enroll` command asks for username, contact email, account context and a hidden password. Enrollment and its audit remain transactional; a duplicate username creates no account or tenant.

Before serving an updated checkout, apply [Version20261101000500](../../../migrations/Version20261101000500.php) with the existing setup/migration procedure and maintenance credentials. Back up the database first. The migration preserves account IDs, contact emails, password hashes, tenant relationships, stored MFA and session-registry data. A unique valid email local part becomes the username (for example, `alice@example.test` becomes `alice`). Invalid or colliding local parts receive a stable `u_` plus 30 hexadecimal characters; the setup operator must retrieve the assigned username from the account inventory. This is an application login identifier; it does not create or rename Linux users. All previous email-based browser sessions must sign in again. Renaming or reusing a username cannot transfer an existing session to another account ID.

The migration is transactional and cannot be automatically reversed to email login. Its SQL planning is read-only, including Doctrine dry runs. PostgreSQL uses NOT NULL, a format constraint and a unique index; SQLite development fixtures use equivalent format triggers and the unique index without rebuilding referenced tables or disabling foreign keys. Production identity integration remains unfinished.

## Own-account password change

In the shared-database development candidate, USER and ADMIN expose `GET /account/password` and `POST /account/password`. The navigation and administrator account-security page link to the form. GET changes no credentials and shows empty password fields; HTTP and Turbo snapshot caching remain disabled. Only the fully authenticated current account is a target: the form accepts no actor, tenant, role or account identifier. Pending MFA cannot access this page.

The POST requires exact Origin, intent-bound CSRF and the current password, using `CurrentPasswordConfirmation` and the existing durable five-confirmation/15-minute session budget shared with MFA settings and session revocation. New-password confirmation must match. Enrollment and password changes use `NewAccountPassword`: at least 14 UTF-8 characters, at most 256 bytes, no null or invalid UTF-8 bytes, no trimming or composition requirement. Password reuse is refused after successful current-password confirmation. Secrets are never repopulated on validation errors. The scoped `PasswordChangeErrors` listener also redacts exceptions raised before the controller (including Origin and stale-proof refusals), preserving their HTTP status/headers while keeping debug request/trace data out of the credential-bearing response.

`ChangeOwnPassword` hashes with the configured Symfony hasher before acquiring the account lock. A conditional native-PDO update checks the current account ID, username, context, active state and previously confirmed verifier, so a concurrent credential change cannot be overwritten using stale proof. Native PDO keeps old/new credential verifiers out of Doctrine parameter profiling. Within the same transaction the service rechecks the bound, unrevoked session; ADMIN also needs a recent matching factor-revision proof and the current required authentication strength. It revokes every session of that account in its context and appends `candidate.account.password_changed`. Audit failure rolls back both the verifier and all revocations. Other accounts and the other surface are unchanged. Success invalidates the current browser session and redirects to login; the old password no longer works. Existing MFA seeds, revision and backup codes are preserved, so enabled MFA remains required.

**Isolated candidate database mode does not offer password changes yet.** Its existing USER/ADMIN grants explicitly exclude credential writes. The page explains unavailability, POST fails closed with 503 and no confirmation budget is consumed. No grant is broadened, no privileged database fallback is introduced and no schema/dependency migration is required. A dedicated credential-write boundary and its acceptance are remaining M1 work. This authenticated change is not forgotten-password recovery, an administrative reset, password history or canonical production integration.

[Service regressions](../../../tests/Integration/ChangeOwnPasswordTest.php) cover rollback, stale credentials, session ownership, factor preservation and profiling. [HTTP journeys](../../../tests/Integration/PasswordJourneyTest.php) cover login replacement, old-browser rejection, request validation, CSRF/Origin, shared rate limits, optional/enabled MFA, recent proof and isolated-mode refusal. Exact-source execution belongs to the current draft PR.

## Candidate authorization and access blocks

The shared decision allows `candidate.hosting.plan` only when the actor is an active USER account, holds the exact permission membership for the requested tenant and has no matching explicit denial. Unknown permissions, malformed identifiers, inactive users and mismatched tenants fail closed. Administrator context does not supply tenant authority. Visibility and execution use the same database rule; queued hosting plans recheck current authority when processed.

`CandidateAuthorization` exposes `allowsTenantPermission`, `requireTenantPermission`, `requireAdmin` and `tenantsForActor`. The existing `WorkbenchStore` constructor and methods remain available to their callers. These services evaluate candidate database authority; browser MFA/session checks remain enforced at the HTTP boundary rather than being inferred from an actor UUID.

| Surface | Implemented candidate behavior |
| :--- | :--- |
| Administrator listing | `GET /workbench/access`, after completed administrator authentication (including MFA when enabled). Lists 100 membership rows per page with stable ordering; page values are bounded to 1–10,000. |
| Access block | `POST /workbench/access/{actor}/{tenant}/denial` accepts `denied=1` or `denied=0` and an actor/tenant-bound CSRF token. Mutations also require exact Origin and an administrator authentication proof less than five minutes old: password when MFA is off, completed MFA when enabled. |
| Write authority | Every change rechecks the current active administrator in the database and validates the USER target and tenant inside a transaction. Repeating the same state creates no duplicate transition. |
| Audit | Actual block/restoration transitions append `candidate.access.denied` or `candidate.access.restored`, recording the administrator, tenant and target user in the same transaction. A failed audit rolls back the access change. |
| Persistence | Blocks belong to the user/tenant pair and permission, independently of a membership row. Deleting and later regranting membership preserves the block. Removing a block never creates membership or activates an account. |
| Worker/setup diagnosis | `wcp:candidate:access:check actor tenant [--permission=candidate.hosting.plan]` reads current authority without changing data. Other runtime contexts are refused. |

The CLI emits JSON with schema `wcp360.candidate.access-check/1`, `allowed` and `production: false`. Exit status is `0` for allowed, `1` for denied and `2` for a forbidden runtime context. A successful candidate decision does not authorize a production operation or invoke a system action.

Before using this slice on an older checkout, apply [Version20261101000200](../../../migrations/Version20261101000200.php) through the existing candidate migration procedure. It creates `wcp_candidate_permission_denials`; the new authority checks depend on that table. Existing installation journals and migration uncertainty must still follow the [recovery rules](../../../installer/recovery/README.md). Do not remove a block table or alter accepted migration history to bypass a denial.

[Service tests](../../../tests/Integration/CandidateAuthorizationTest.php), [HTTP journeys](../../../tests/Integration/PermissionAdministrationTest.php) and [CLI tests](../../../tests/Integration/CandidateAccessCommandTest.php) define positive/refusal coverage. Full current-source execution is tracked in [draft PR #116](https://github.com/yyogas/wcp360/pull/116); test source alone is not passing evidence.

This candidate does not implement or grant any of the 82 canonical permissions. Positive overrides, roles/delegation, service accounts, general policy composition and production Unix/PostgreSQL separation remain unfinished. [AUDIT-WCP-0118](../../../../documentation/governance/audits/AUDIT-WCP-0100/CLOSURE-MATRIX.md#audit-wcp-0118--permission-priority-vs-security-intersection) remains open and authoritative for the unresolved canonical composition; these candidate blocks do not settle that audit or amend accepted contracts.

## Administrator MFA candidate

MFA is **optional per administrator account and off by default**. An account without an active factor signs in with its password and receives normal administrator access at authentication strength 1. Account security offers voluntary setup with current-password confirmation; enrollment uses the existing Symfony/Scheb pipeline and withholds roles until the selected activation is confirmed. Once enabled, each login requires the second factor and completes at strength 2. Confirmed disablement removes the factor/codes and ends the session; the next password login is sufficient. USER login retains its existing flow.

This follows the current product decision and supersedes earlier Core notes requiring MFA for all administrators. The external canonical security baseline still specifies mandatory ADMIN MFA; reconciling that requirement with this product decision remains explicit M1 integration work. Core-only implementation does not claim production contract acceptance.

| Mechanism | Implemented candidate behavior |
| :--- | :--- |
| TOTP | Six digits, 30-second periods and a one-period clock window in either direction; a durable monotonically increasing counter rejects accepted-code replay. |
| Enrollment | `GET /account/security/mfa/enable` presents an opt-in form without changing state. `POST /mfa/enroll` requires current password, exact Origin, CSRF and the shared confirmation budget before returning a seed once. Confirmation expires after five minutes; incomplete/cancelled enrollment never activates MFA. Active seeds are never redisplayed. |
| Attempt limits | Database-backed limits allow five failed checks and five enrollment starts per separate 15-minute window. |
| Seed protection | Sodium authenticated encryption with an independent 32-byte key, supplied as 64 hexadecimal characters in `WCP_MFA_KEY`; the key is not derived from `APP_SECRET`. |
| Backup codes | Ten random 128-bit codes are shown once after enrollment. Only actor-bound keyed hashes are stored; successful use atomically consumes a code and increments the factor revision. |
| Browser boundary | Mutating administrator requests require exact Origin; forms also require CSRF tokens. MFA completion rotates the session identifier. Seed/code responses use the shared no-store policy. |
| Current authority | Inactive accounts and changed factor revisions invalidate prior authentication. Unavailable/incorrect encryption keys block accounts with MFA enabled and new activation; they do not block password-only accounts. Recovery or disablement invalidates other sessions' prior factor proofs; enabling MFA also invalidates older password-only sessions. |
| Freshness | Unsafe administrator requests require authentication less than five minutes old: password proof when MFA is off, completed MFA proof when it is enabled. `/account/reauthenticate` repeats the normal login accordingly; GET and failed credentials never renew either proof. |
| Disablement | Account security offers `POST /mfa/reset` with fresh MFA, current password, exact Origin, CSRF and the shared attempt budget. It atomically removes the factor/codes and revokes all current-context ADMIN registrations through `DisableAdministratorMfa`, then ends the browser session. Subsequent password-only login is allowed; setup can be chosen again later. |

Backup codes replace the **second factor after password authentication**. They do not reset a forgotten password. Keep them outside the hosted server. If the encryption key is lost, restore the correct operator-managed key; no automatic administrative bypass or plaintext seed export is provided. Existing local `.env` files are preserved, so an operator must add `WCP_MFA_KEY` when upgrading an older checkout. A fresh [checkout installation](../../../installer/recipes/README.md) generates independent private application and MFA keys.

[Service regressions](../../../tests/Integration/CandidateMfaTest.php) cover factor storage, replay, attempt windows and recovery. [HTTP journeys](../../../tests/Integration/MfaJourneyTest.php) cover pending-role denial, Origin/CSRF checks, session rotation, recovery/reset and missing-key refusal. Executed outcomes must be tied to the exact source in [draft PR #116](https://github.com/yyogas/wcp360/pull/116); these sources alone are not a passing CI claim.

## Administrator identity confirmation

`GET /account/reauthenticate` is available to a fully authenticated administrator on ADMIN, with or without optional MFA. Account security and the administrator navigation link to the same form. It displays the current identity and submits to the existing CSRF-protected, rate-limited `/login` authenticator; there is no second password verifier or custom authentication bypass. Rejected credentials return a generic accessible error without changing the old proof or registration.

A successful password check retires the previous registration. With MFA off, the new strength-1 session receives a fresh password proof and returns to the dashboard. With MFA enabled, it remains pending until a new TOTP or unused backup code passes before sensitive actions become available. MFA completion rotates the identifier and upgrades that pending registration while preserving its original deadline. Opening the form, a failed password, a replayed code or a revoked pending registration cannot refresh the proof. All administrator POSTs still require exact Origin. The browser uses Turbo submissions with the existing `no-referrer` policy; it never accepts a missing or `null` Origin. Enrollment and the one-time backup-code response render inside the shared authentication frame, while full login, reconfirmation and logout target the whole page. The JavaScript bundle is required for these browser forms. Shared private pages disable Turbo snapshot caching as well as HTTP caching, including the enrollment seed and one-time backup-code views.

The flow returns to the dashboard. The administrator must retry the original action explicitly; no submitted action or external return URL is saved or replayed. This is a full candidate sign-in replacement, not an extension of the old session. Forgotten-password recovery and action-bound production confirmation remain open. [MFA HTTP regressions](../../../tests/Integration/MfaJourneyTest.php) and the [isolated PostgreSQL role journey](../../../tests/Integration/CandidateDatabaseRolesTest.php) define its positive and refusal coverage.

## Candidate session inventory and revocation

`GET /account/sessions` lists only the authenticated account's sessions in its current USER or ADMIN context. The shared navigation exposes this page in both contexts; password-only administrators can use it when MFA is off. Pending MFA login/enrollment cannot access it. The listing has a stable order, 100 rows per page and a page range of 1–10,000. It shows creation, last activity and maximum expiry times, current-session status and active/pending/expired/revoked states. It collects no IP address or user-agent inventory.

| Mechanism | Implemented candidate behavior |
| :--- | :--- |
| Registry | `wcp_candidate_user_sessions` and `wcp_candidate_admin_sessions` hold server-generated UUID selectors, owner/context, authentication strength, timestamps, revocation state and password-confirmation counters. A selector alone never authenticates a browser. |
| Browser binding | Only a domain-separated SHA-256 binding of the actor, context and actual Symfony session identifier is persisted. Raw session identifiers are excluded from database rows, audit records and rendered views. Symfony's native server-side session files still hold the session payload; this registry is not a database-backed session handler. |
| Primary authentication | A current active account must match the browser context. The final rotated session identifier is registered after Symfony's login strategy. USER and password-only ADMIN sessions are active at strength 1. Accounts with enabled MFA, or explicit enrollment in progress, remain pending at strength 1 until the factor upgrades the row to strength 2. |
| MFA completion | A compare-and-swap upgrade binds the final newly rotated identifier and changes the same pending ADMIN row to strength 2. Its original creation time and absolute deadline remain unchanged; a revoked or expired pending row cannot be restored by MFA. |
| Lifetime | The existing shared policy remains authoritative: USER idle/absolute limits are 30 minutes/12 hours; ADMIN limits are 15 minutes/8 hours. Activity updates never extend absolute expiry or move timestamps backwards. |
| Admission | The registry is checked before the firewall, before unsafe controller execution and again before an MFA attempt. Legacy, missing, revoked, expired or incorrectly bound registrations fail closed. Concurrent heartbeats preserve monotonic timestamps without invalidating another valid request solely because it touched the row. |
| Single revocation | `POST /account/sessions/{id}/revoke` targets only a session owned by the current account in the current context. Exact Origin, a selector-bound CSRF token and the current password are mandatory. Administrators also need a password proof less than five minutes old, or a recent MFA proof when MFA is enabled. |
| Global revocation | `POST /account/sessions/revoke-all` applies only to the current account and context, including its current session. It requires the same Origin/password checks and its own CSRF intent. It does not revoke another account or the other browser context. |
| Password-confirmation limit | After Origin and CSRF validation, a durable budget permits five total confirmation attempts per session in 15 minutes, including successful attempts. Consumption commits before password hashing; the sixth attempt returns 429 without revocation. MFA activation, disablement and session revocation share this budget through `CurrentPasswordConfirmation`; changing forms does not reset it. A new primary login receives a new session budget; MFA completion retains that pending session's counters. |
| Logout and audit | POST logout retires the bound registration before Symfony clears the browser session. Opening, rotating, revoking and confirmation-attempt events use the UUID selector, and mutations commit atomically with their audit records. |

Revoking the current session or all sessions invalidates the local session and redirects to `/login` with status 303. Revoking another owned session redirects to the inventory with status 303. Revocation is enforced on subsequent admitted requests and rechecked at the implemented mutation/MFA boundaries; it does not stop an already dispatched system operation or provide general fencing for in-flight work or other channels.

Before using this slice on an older checkout, apply [Version20261101000300](../../../migrations/Version20261101000300.php) using the existing candidate migration procedure. **Existing users must sign in again after migration:** old cookies are not silently adopted into the new registries. Respect the [installer recovery rules](../../../installer/recovery/README.md) if migration execution is uncertain; dropping a registry would discard durable revocation state.

[Service regressions](../../../tests/Integration/CandidateSessionsTest.php) cover binding/owner separation, exact lifetime limits, MFA rotation, attempt budgets, concurrent timestamp updates, bounded inventory and audit rollback. [HTTP journeys](../../../tests/Integration/SessionJourneyTest.php) cover own-account listing/revocation, browser checks, logout, legacy-cookie refusal, pending/fresh-MFA boundaries and revocation across kernel restarts. Exact-source execution remains tracked in [draft PR #116](https://github.com/yyogas/wcp360/pull/116); test definitions do not establish a current passing result.

## Remaining production foundations

The MFA, access-block and session tables and flows are `wcp_candidate_*` development mechanisms, not an accepted canonical production schema or the canonical `wcp_iam` storage/grant model. Complete password recovery, passkeys, action-bound production reauthentication, service accounts, reseller delegation, general permission composition, production session administration and contract integration remain unfinished. Production Unix identities and PostgreSQL grants still require independent separation and qualification. **M1 is not complete.** Candidate code and passing tests do not establish production readiness.

Use the common [module layout](../README.md) for Domain, Application, Infrastructure and UI code. Hosting resource policy stays with its owning domain; reuse the common authorization boundary.

- [Core roadmap and completion estimate](../../../ROADMAP.md)
- [Core navigation index](../../../NAVIGATION.md)
- [System-service resources](../../../resources/services/README.md)
- [Core implementation and acceptance](../../../../documentation/development/CORE-ENGINE.md)
