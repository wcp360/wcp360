# Core verification locations

**Status: the development candidate includes executable PHPUnit unit, integration/functional and plugin-lifecycle suites, plus a separate Panther browser suite. Contract, Security and Installer remain organizational locations; security and installer checks also live in implemented suites and the disposable Linux/VM harnesses. A directory is not evidence of a passing test.**

| Test source | Purpose |
| :--- | :--- |
| [Unit](Unit/README.md) | Pure domain invariants, application use cases with explicit adapters, resource bounds and configuration rendering. |
| [Contract](Contract/README.md) | Serialization, compatibility, operation identifiers, permission namespaces, bounded IPC and approved API/DB adapter behavior. |
| [Integration](Integration/README.md) | Actual persistence, transport, agent and selected Linux-service integration on disposable supported environments. |
| [Security](Security/README.md) | Tenant/surface isolation, authorization/revocation, path confinement, broker boundaries and secret redaction. |
| [Lifecycle](Lifecycle/README.md) | Install, enable, crash, quarantine, disable, update and remove detached optional plugins while retaining Core operations. |
| [Installer](Installer/README.md) | Fresh install, rejected preconditions, interrupted/resumed setup, repair, qualified upgrades and recovery rehearsal. |
| [Browser](Browser/README.md) | Twig/UX workflows, native form behavior, keyboard focus, responsive layout and visible error/loading/empty states. |
| [Fixtures](Fixtures/README.md) | Shared deterministic settings, input/output examples, fake tenant/resource identities and expected rendered configuration. |

Run `composer check` from Core for Symfony-aware PHPStan, analyzer regressions, PHPUnit and framework lints. Run `composer test:browser` in the documented Chromium/WebDriver environment. See the [testing guide](../../documentation/development/TESTING.md) for commands, scopes, fixture safeguards and Linux/VM evidence. The deliberate-error PHPStan fixtures live in `tools/phpstan/fixtures`, outside application test discovery.

## Real-browser journeys

[AdministratorIdentityJourneyTest](Browser/AdministratorIdentityJourneyTest.php) exercises actual password login, MFA enrollment, one-time recovery-code display, identity reconfirmation, replay refusal, global own-session revocation and logout through Turbo. [HostingJourneyTest](Browser/HostingJourneyTest.php) covers USER login, the runtime selector, themes/navigation and idempotent plan submission. Both use synthetic fixtures; browser and database execution results must be recorded for their exact source. Browser execution requires the documented Chrome/WebDriver environment.

## Candidate identity and authorization regressions

| Executable source | Bounded verification scope |
| :--- | :--- |
| [CandidateMfaTest](Integration/CandidateMfaTest.php) | Encrypted factor storage, replay and attempt windows, single-use backup codes and factor revisions. |
| [MfaJourneyTest](Integration/MfaJourneyTest.php) | Pending administrator role denial, exact Origin/CSRF boundaries, session rotation, recovery/reset, unavailable-key refusal and administrator reconfirmation with password/MFA, generic failure feedback, replay and revoked-pending refusal. |
| [CandidateAuthorizationTest](Integration/CandidateAuthorizationTest.php) | Exact single-permission/current-USER checks, shared visibility/execution, cross-tenant refusal, idempotent audited blocks, membership regrant, queued-worker rechecks, bounded listing and audit-failure rollback. |
| [PermissionAdministrationTest](Integration/PermissionAdministrationTest.php) | Completed/fresh administrator MFA, Origin and target-bound CSRF for access changes; existing user-session/API denial; worker rejection after a block; unblocking without restoring deleted membership. |
| [CandidateAccessCommandTest](Integration/CandidateAccessCommandTest.php) | Read-only worker/setup CLI decisions, unknown-permission refusal, JSON output and exit status; HTTP runtime contexts cannot use the command. |
| [CandidateSessionsTest](Integration/CandidateSessionsTest.php) | Exact owner/context/cookie bindings, idle and absolute expiry, MFA strength upgrades, durable confirmation budgets, bounded inventory, monotonic concurrent timestamps and atomic audit rollback. |
| [SessionJourneyTest](Integration/SessionJourneyTest.php) | Own-account session inventory and single/all revocation; exact Origin, target-bound CSRF and current password; durable logout, legacy-cookie refusal, pending/fresh administrator MFA, mutation/MFA rechecks and kernel-restart persistence. |
| [Role policy](Unit/CandidateDatabasePolicyTest.php), [SCRAM vectors](Unit/PostgresScramTest.php), [maintenance CLI](Integration/CandidateDatabaseRolesCommandTest.php) and [PostgreSQL role creation](Integration/CandidateDatabaseRolesTest.php) | Fresh role creation, exact effective table/column grants, constant-zero locking, no existing-role adoption, audit rollback, conflict/refusal/redaction and real USER/ADMIN-MFA/worker journeys. |
| [Database URL selection](Unit/CandidateDatabaseUrlTest.php), [middleware](Unit/CandidateDatabaseMiddlewareTest.php) and [Symfony configuration](Integration/CandidateDatabaseConfigurationTest.php) | Context-only secret resolution, no fallback, invalid-profile/URL refusal, exception redaction and actual container wiring. |
| [PostgreSQL session grants](Integration/CandidatePostgresGrantsTest.php) | Real separate login roles, session lifecycle, opposite-surface SELECT/INSERT/UPDATE/DELETE refusal, worker denial and rejection of elevated, inherited, schema-creating, cross-granted or incomplete credentials. Runs only against the explicitly configured disposable PostgreSQL test database; skipped on local SQLite. |
| [RuntimeIdentity unit regressions](Unit/RuntimeIdentityTest.php) | Synthetic canonical identity matches; saved/filesystem UID/GID, group, capability and mapping refusals; malformed/duplicate/truncated/oversized kernel observations. |
| [RuntimeIdentity standalone/live tests](Integration/RuntimeIdentityTest.php) | Fixed-profile CLI with no app bootstrap, secret redaction, invalid arguments and real Linux calling-thread/NSS observations. Linux-only cases execute in CI; positive synthetic cases do not establish real deployment of the canonical accounts. |

Access checks depend on [Version20261101000200](../migrations/Version20261101000200.php); durable session admission depends on [Version20261101000300](../migrations/Version20261101000300.php). Existing cookies require a new login after the session migration. The new registries hold metadata and SHA-256 bindings, not the native Symfony session payload. These suites verify candidate surfaces; they do not qualify the 82 canonical permissions, general in-flight revocation fencing, production database grants or Unix identities. Full current-source CI for the session slice must be executed and recorded in [draft PR #116](https://github.com/yyogas/wcp360/pull/116). Earlier workflow totals remain evidence for their own source revisions.

Organize implemented test files by the same domain/service names as the source. Framework configuration, deterministic inputs and the accepted contract/requirement must accompany the implementation. Keep the shared synthetic fixture source in Fixtures instead of copying test data across suite directories.

Configuration-template checks identify the service, package/version, resource scope and expected rendering. Supported-target integration also executes actual service validation, failed-apply cases, concurrent-change/ownership checks and observed post-apply health on a disposable environment. A syntax check or source directory is not service acceptance.

Record the exact candidate SHA, environment, assertion and result. Browser tests and security/integration tests have different scopes; document missing execution instead of substituting a VM adapter or screenshot. Generated reports/caches belong outside source.

- [Source navigation](../NAVIGATION.md)
- [Service resource lifecycle](../resources/services/README.md)
- [Core acceptance journey](../../documentation/development/CORE-ENGINE.md)
