# Naming and account identifiers

Documentation target recorded on November 3, 2026. This guide applies the [owner decision](../governance/decisions/owner-decision-account-naming-2026-11-03.md); it does not modify the executable candidate, existing identities or frozen contracts.

## Purpose

Use English ASCII names without spaces or accents for new project-owned paths. Prefer lowercase kebab-case for freely named directories, scripts, configuration and documentation: `account-management/`, `check-account-name.sh`, `account-policy.yaml`, `naming-and-account-identifiers.md`. Underscores in otherwise freely named paths are reserved for an explicit prefix or version separator, for example `release_0.0.0.1.json`. Keep meaningful extensions, including compound extensions such as `.html.twig`; use the format required by the consumer rather than converting every configuration file to YAML.

Language and tool contracts take precedence over the general path convention. PHP PSR-4 namespace directories and class filenames retain matching case, for example `src/Module/IdentityAccess/Domain/CandidateUsername.php`. Preserve standard entrypoints and filenames such as `README.md`, `LICENSE`, `SECURITY.md`, `Dockerfile`, `Makefile`, `AGENTS.md`, `composer.json`, `composer.lock`, `go.mod`, `go.sum`, `.env`, `.gitignore`, `.htaccess`, extensionless `bin/console`, Go `_linux.go`/`_test.go` files, Stimulus `_controller.js` files and existing migration class names. Do not translate public identifiers, rename the existing `WCP360_Core` tree, change vendor/generated files or rewrite historical sources through this policy. A separately reviewed migration must cover references, case-only Git changes, autoloading, deployment paths and Linux case sensitivity before any existing path is renamed.

The general file convention does not replace language conventions for variables, classes, SQL identifiers, public API fields, external protocol names or version contracts. The existing four-segment project target `v0.0.0.1` remains separate from package formats and recorded compatibility boundaries.

## Data model

| Identity | Target and boundary |
| --- | --- |
| Opaque account ID | Stable authority reference; never inferred from a username, display name or email. |
| Panel login username | New human-account allocations target 6 through 32 ASCII characters, starting with `a-z`, followed only by `a-z`, `0-9` or `_`. |
| Display name and contact email | Separate data, not a canonical account key, role or implicit login alias. |
| Hosting UNIX `system_name` | Keep the frozen generated `wcp_<base32-id-fragment>` format, maximum 31 characters and immutability after allocation; do not substitute a submitted login. |
| Service and privileged identities | Explicitly managed service users, ADMIN/USER runtimes and UID 0 broker have their own accepted contracts. This policy does not rename OS `root`, `www-data`, `wcp360-user` or `wcp360-admin`. |
| Role, permission and tenant membership | Server-authorized relationships independent of the spelling or prefix of a name. |

The owner's panel username expression is `^[a-z][a-z0-9_]{5,31}$`. Require a full-string ASCII match without trimming, case conversion or multiline acceptance; some engines treat `$` as a position before a final newline. PHP integration must use equivalent absolute boundaries such as `\A[a-z][a-z0-9_]{5,31}\z`. This is a product policy, not a universal kernel or `/etc/passwd` limit. Linux user/group tooling, NSS providers and each downstream consumer require their own qualification.

Recommendations `usr_martin` and `adm_jdupont` meet the shape rule. `rev_alpha01` is only an example for a future separately admitted reseller scope; it does not restore reseller to Community. Prefixes are optional naming aids, not an authorization mechanism or an automatic collision solution. Operator-reserved prefixes must be enforced by the owning allocation policy, not trusted from a request.

Reject at least `root`, `admin`, `system`, `www-data`, `support` and `test` for new human login allocations, including names already rejected by length or syntax. In particular, `system` and `support` match the shape rule and still require the reserved-name refusal. Also reserve platform service identities and configured operator-reserved names in the applicable allocation namespace. Do not apply the rule by changing or deleting existing system users. Check login uniqueness within the authoritative authentication context and enforce it atomically in persistence; separate ADMIN and USER surfaces remain separate authorities. UNIX allocations additionally check the appropriate group/user namespace, internal allocation records and NSS, with conflict handling at the typed privileged effect. Regex validation alone proves no uniqueness.

## API and interface contracts

The current candidate is explicitly different: `CandidateUsername`, `UsernameSchema` and its migration allow 3 through 32 characters; `root` and `admin` are currently accepted. Existing setup/recovery examples describe that candidate. These facts must remain visible until a separately implemented, verified change supersedes them. Do not relabel the candidate as enforcing 6 characters or a reserved-name list.

Before implementing new enrollment rules, inventory every writer, reader and importer: interactive setup, future account creation endpoints, forms, jobs, schemas/migrations, constraints, fixtures, lookup, login, session refresh, recovery and restore. Use one owner-reviewed allocation rule across writers and equivalent persistence constraints; keep legacy lookup and recovery functional. Handle uniqueness races without creating a second account, tenant or partial audit. Keep existing safe login/recovery responses and attempt limits; do not expose a new anonymous account-enumeration endpoint.

Existing shorter or reserved logins are legacy identities, not grounds for an immediate lockout or silent rename. An explicit migration must preserve opaque account IDs, credentials, MFA, tenant ownership, audit and recovery. Restoration must detect collisions and preserve quarantine/tombstone rules; it must not reassign an old name or UID/GID to another owner. Revisit stale usernames and generated fallbacks only through that reviewed lifecycle. No automatic application-login to Linux-user mapping is introduced.

Acceptance examples for a future implementation:

| Input or scenario | Expected result |
| --- | --- |
| `abcdef`, `usr_martin`, `adm_jdupont`, one letter followed by 31 allowed characters | Shape accepted, then reserved-name, current authority and uniqueness checks. |
| Five or 33 characters; uppercase, accents, emoji, hyphen, dot, slash, leading digit/underscore | New allocation refused. |
| `system`, `support` | Refused even though the syntax and length are valid. |
| Leading/trailing whitespace, final newline, embedded NUL or newline | Refused without normalizing into a different accepted login. |
| Two concurrent creations in the same context | At most one allocation; the refusal leaves no partial account/tenant/audit effects. |
| Identical spelling on USER and ADMIN | Cannot select or confer authority on the other surface; apply the accepted context uniqueness contract. |
| Existing `root`, `admin` or a three-to-five-character login | Login/session refresh/recovery/restore remain supported until a reviewed migration; no name-based privilege. |
| Tenant attempts a reserved service UNIX name, NSS collision or tombstone reuse | Refused by allocation policy and rechecked at effect time. |

These are requirements to test on the implementing revision, not tests run by this documentation change.

## Roles and security

An `adm_` prefix never grants ADMIN, `usr_` never proves tenant ownership and `rev_` never grants reseller rights. Check the current principal, permission, tenant, ownership/delegation, state and required authentication strength on the server. Never concatenate a username into a shell command, absolute path, SQL identifier or web-server configuration; use accepted typed operations and opaque resource identities. Account renaming cannot transfer sessions or resource ownership.

Username restrictions are not password restrictions. The existing candidate `NewAccountPassword` requires at least 14 UTF-8 characters and at most 256 bytes, rejects invalid UTF-8/NUL and accepts passphrases without trimming or character-composition rules. The new naming instruction does not change that policy. Keep password hashing, hidden entry, non-disclosure, attempt limits, recovery controls and session revocation as defined by IdentityAccess; no password, token or verifier belongs in documentation examples or reports. Administrator MFA remains optional per administrator and required for an account once enabled.

## Dependencies

- [Core structure](../../WCP360_Core/STRUCTURE.md) and [IdentityAccess](../../WCP360_Core/src/Module/IdentityAccess/README.md).
- [Candidate username](../../WCP360_Core/src/Module/IdentityAccess/Domain/CandidateUsername.php), [username schema](../../WCP360_Core/src/Module/IdentityAccess/Infrastructure/UsernameSchema.php), [username migration](../../WCP360_Core/migrations/Version20261101000500.php) and [password policy](../../WCP360_Core/src/Module/IdentityAccess/Domain/NewAccountPassword.php).
- Frozen naming design: [implementation naming contract](../../WCP360_Developper/WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation/IMPL-4017-27-NAMING-IDENTIFIER-CONTRACT.md) and [allocation/normalization registry](../../WCP360_Developper/WCP-Design-System-Spec-v4.0.21.23-global-canonical-registry-consolidation/CONTRACT-V4021-09-NAMING-ALLOCATION-NORMALIZATION.md). Their identities and bytes remain unchanged.
- [PSR-4](https://www.php-fig.org/psr/psr-4/) for PHP path/class case; [Debian 13 adduser configuration](https://manpages.debian.org/trixie/adduser/adduser.conf.5.en.html) and [useradd](https://manpages.debian.org/trixie/passwd/useradd.8.en.html) for tool-specific account constraints. These do not establish a universal Linux-kernel username limit.
- [Security test matrix](SECURITY-TEST-MATRIX.md) and [production-readiness review](PRODUCTION-READINESS-REVIEW.md). Documentation checks do not qualify account provisioning, migration, isolation or production security.
