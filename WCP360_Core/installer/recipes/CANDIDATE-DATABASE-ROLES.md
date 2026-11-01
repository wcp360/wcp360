# Candidate PostgreSQL role provisioning

**WCP360 Standalone** is the server-installed panel in the **WCP360** project. This maintenance tool creates a fresh, bounded development role set for the [isolated candidate profile](../../config/contexts/DATABASE-PROFILES.md). Native production provisioning remains unfinished.

## Prerequisites and fixed scope

- Use a disposable development PostgreSQL 17 cluster, candidate execution in `APP_ENV=dev/test`, and `WCP_CONTEXT=setup`.
- The connected database must be exactly `wcp360_workbench` or the explicitly disposable `wcp360_test` fixture database. These names are guards, not proof that an arbitrary host is disposable.
- The maintenance login needs role-creation authority and ownership of the database, public schema and all candidate tables, or development superuser authority. It is never a web/worker credential.
- Apply all candidate migrations, including [Version20261101000400](../../migrations/Version20261101000400.php). Required tables and the validated constant-zero lock constraints must exist.
- All three target role names must be absent. Existing roles are never adopted, changed, rotated, repaired or deleted. Unexpected public relations and reserved `wcp_*` schemas are refused.

| Database | USER role | ADMIN role | Worker role |
| :--- | :--- | :--- | :--- |
| `wcp360_workbench` | `wcp360_dev_user` | `wcp360_dev_admin` | `wcp360_dev_worker` |
| `wcp360_test` | `wcp360_test_user` | `wcp360_test_admin` | `wcp360_test_worker` |

No database, schema, table, operating-system account, service or package is created by this command.

## Inspect, then explicitly apply

From Core, on the disposable Linux candidate:

```bash
WCP_CONTEXT=setup php bin/console doctrine:migrations:migrate --no-interaction
WCP_CONTEXT=setup php bin/console wcp:candidate:database:roles
```

The second command is read-only and needs no new role passwords. It reports the fixed names and reviewed grant matrix; a plan does not reserve names or authorize later changes. Application rechecks prerequisites under a transaction-scoped advisory lock.

For creation, provide three independently generated, unique 64-character lowercase hexadecimal passwords through a **setup-only private environment/secret store**:

| Private variable | Used for |
| :--- | :--- |
| `WCP_CANDIDATE_DATABASE_USER_PASSWORD` | New USER database login |
| `WCP_CANDIDATE_DATABASE_ADMIN_PASSWORD` | New ADMIN database login |
| `WCP_CANDIDATE_DATABASE_WORKER_PASSWORD` | New worker database login |

Do not pass passwords in command arguments, commit them or place all three in an environment shared with web processes. Once that private environment is supplied, explicit creation is:

```bash
WCP_CONTEXT=setup WCP_ALLOW_CANDIDATE_DATABASE_ROLES=1 php bin/console wcp:candidate:database:roles --apply
```

The enable flag defaults to zero. USER, ADMIN and worker contexts cannot inspect or create roles, even with the flag set. The JSON result contains metadata and `planned`, `created`, `refused` or `uncertain` status, never passwords, verifiers or connection URLs.

After creation, distribute **only the corresponding private URL** to each runtime: `WCP_DATABASE_USER_URL`, `WCP_DATABASE_ADMIN_URL` or `WCP_DATABASE_WORKER_URL`, with `WCP_DATABASE_MODE=session-isolated` and the matching context. Remove setup-only password variables and maintenance credentials from those runtime environments. Secret distribution and launcher/Unix isolation are not performed by this command.

## Direct candidate grants

The [single grant policy](../../src/Module/Setup/Infrastructure/CandidateDatabasePolicy.php) owns this matrix. It covers the implemented candidate paths, not canonical production permissions. `S/I/U/D` mean SELECT/INSERT/UPDATE/DELETE; `L` means UPDATE on the constant-zero `lock_guard` column.

| Candidate table suffix | USER | ADMIN | Worker |
| :--- | :--- | :--- | :--- |
| `users` | S, L | S, L | S |
| `accounts` | — | — | — |
| `tenants` | S, L | S, L | S |
| `memberships` | S | S | S |
| `sites` | S, I, L | — | S, U |
| `jobs` | S, I | — | S, U |
| `audit` | I | I | I |
| `plugins` | S | S, I, U, D | — |
| `runtime_settings` | S | S, U | — |
| `activations` | S, I | — | S, U |
| `activation_grants` | S | — | S |
| `mfa` | — | S, I, U | — |
| `mfa_recovery` | — | S, I, D | — |
| `permission_denials` | S | S, I, D | S |
| `user_sessions` | S, I, U, D | — | — |
| `admin_sessions` | — | S, I, U, D | — |

PostgreSQL row-locking reads require UPDATE privilege on at least one column. The [new migration input](../../src/Module/Setup/Infrastructure/CandidateRowLockSchema.php) adds a non-null `SMALLINT lock_guard` fixed to zero by a validated CHECK constraint on users, tenants and sites. Granting UPDATE on this marker permits existing `FOR UPDATE` paths without permitting identity, password or active-state changes. See [PostgreSQL 17 privileges](https://www.postgresql.org/docs/17/ddl-priv.html).

Effective table and column privileges are checked across all 16 candidate tables before commit, including inherited PUBLIC grants. Unexpected rights abort the entire creation. No grant option, broad schema/default grant, ownership, role membership or privileged role attribute is assigned to application roles. Tenant/account authorization remains enforced by the application; these are per-runtime service logins, not one PostgreSQL login per human user.

## Password handling, atomicity and recovery

[The provider](../../src/Module/Setup/Infrastructure/CandidateDatabaseRoles.php) uses native PDO for sensitive DDL so application SQL profiling does not retain verifier statements. [SCRAM encoding](../../src/Module/Setup/Infrastructure/PostgresScram.php) uses native SHA-256 PBKDF2/HMAC, 4,096 iterations and a fresh 16-byte random salt; supported passwords are ASCII, avoiding a partial SASLprep implementation. SQL receives only a PostgreSQL SCRAM verifier. Verifiers remain sensitive and server-side SQL logging needs its own protection. The format follows [PostgreSQL 17](https://www.postgresql.org/docs/17/catalog-pg-authid.html) and [RFC 7677](https://www.rfc-editor.org/rfc/rfc7677); tests check the published authentication exchange and actual PostgreSQL logins.

One transaction creates NOLOGIN roles, applies explicit grants, validates effective rights, enables LOGIN and appends `candidate.database.roles.created` without credentials. Existing caller transactions are refused. A competing invocation fails without interfering with the lock holder. Pre-commit failure rolls back roles, grants and audit together; commit or rollback uncertainty invalidates the connection and returns `uncertain`.

There is no automatic retry, rotation, rollback of a committed creation or existing-role repair. After an uncertain result, inspect roles/grants and the audit record with the maintenance identity before taking further action. Keep the originally supplied secrets available privately; a repeated attempt refuses names that already exist.

PostgreSQL roles are cluster-wide. This tool grants rights only in the selected candidate database and does not alter other databases' PUBLIC privileges or host authentication rules. Production HBA/network/TLS policy, cross-database confinement, maintenance retirement, native identities/filesystem packaging, general credential lifecycle, canonical IAM/permission integration and live-connection drift fencing remain open. See [PostgreSQL role scope](https://www.postgresql.org/docs/17/sql-createrole.html) and [M1](../../ROADMAP.md#m1).

## Verification

[Policy tests](../../tests/Unit/CandidateDatabasePolicyTest.php), [SCRAM vectors](../../tests/Unit/PostgresScramTest.php), [CLI refusals](../../tests/Integration/CandidateDatabaseRolesCommandTest.php) and [actual PostgreSQL journeys](../../tests/Integration/CandidateDatabaseRolesTest.php) cover the implemented boundary. PostgreSQL cases run only against the explicitly configured disposable test database. They cover real logins, direct privilege refusals, constant-zero constraints, audit rollback, name conflicts, caller/concurrent transactions, PUBLIC-column pollution and USER/ADMIN-MFA/worker application use. Windows SQLite runs skip the PostgreSQL cases; exact-source Linux CI outcomes belong in [PR #116](https://github.com/yyogas/wcp360/pull/116).
