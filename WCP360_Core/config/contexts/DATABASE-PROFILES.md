# Candidate database profiles

**WCP360 Standalone** is the server-installed panel in the **WCP360** project. These profiles belong to its Core engine. They are development candidates, not production database provisioning or full isolation qualification.

## Select the access profile

| `WCP_DATABASE_MODE` | Behavior |
| :--- | :--- |
| `shared` (default) | Existing development behavior: resolve `DATABASE_URL`, including the local SQLite project-directory placeholder. No session-grant isolation is asserted. |
| `session-isolated` | Select only the current context's private PostgreSQL URL. Missing, empty or unsupported input fails; no shared or other-context fallback exists. |

| `WCP_CONTEXT` | Only credential read in isolated mode | Session privilege requirement |
| :--- | :--- | :--- |
| `user` | `WCP_DATABASE_USER_URL` | CRUD on `public.wcp_candidate_user_sessions`; no table/column privilege on the ADMIN registry. |
| `admin` | `WCP_DATABASE_ADMIN_URL` | CRUD on `public.wcp_candidate_admin_sessions`; no table/column privilege on the USER registry. |
| `worker` | `WCP_DATABASE_WORKER_URL` | No table/column privilege on either session registry. |
| `setup` | `WCP_DATABASE_SETUP_URL` | Maintenance connection; intentionally exempt from application-role inspection so migrations can create the schema. Never supply it to web/worker processes. |

Select the mode and context in the corresponding private process configuration:

```dotenv
WCP_DATABASE_MODE=session-isolated
WCP_CONTEXT=user
```

Supply that process's `WCP_DATABASE_USER_URL` through its private environment/secret store. Do not put all four credentials in one shared environment or source file. The selector reads only one URL; it cannot remove secrets already exposed to the process by its launcher.

Accepted URLs use `postgresql://` or `postgres://`, with explicit host, database, login and nonempty percent-encoded password. Supported query keys are `serverVersion` (17.x), `charset=utf8` and `sslmode` (`disable`, `require`, `verify-ca`, `verify-full`). Duplicate/unknown keys, control characters, fragments and malformed percent escapes fail. TLS `disable` is available for disposable local tests; remote deployment needs a separately qualified transport/trust configuration. Isolated URLs are not interpolated as container parameters.

The profile selector is [wired into Doctrine](../packages/doctrine.yaml), and its [middleware](../../src/Shared/Security/CandidateDatabaseMiddleware.php) is automatically registered by DoctrineBundle. Merely setting a mode does not create roles or grants. Apply candidate migrations through the maintenance context before attempting isolated application connections.

## Connection admission

On every newly established isolated USER, ADMIN or worker connection, the [grant gate](../../src/Shared/Security/CandidateSessionGrants.php):

1. Fixes the search path to `pg_catalog, public, pg_temp`, then observes the connected PostgreSQL identity and version.
2. Requires PostgreSQL 17, unchanged login/effective identity and an ordinary login without superuser, database/role creation, replication or row-security bypass.
3. Rejects any role membership, including non-inherited memberships that could permit `SET ROLE`. This is a conservative candidate restriction, not the final production role model.
4. Rejects database/public-schema ownership or public-schema creation and requires both physical candidate session tables.
5. Requires all four own-table CRUD privileges for USER/ADMIN. Opposite-table access includes column grants and extra table rights such as TRUNCATE, REFERENCES, TRIGGER and PostgreSQL 17 MAINTAIN; workers must have neither registry.

PostgreSQL privilege inquiry functions include effective table/column grants. Multiple listed privilege names mean *any* matching privilege; own-table CRUD therefore checks each privilege separately. See the [PostgreSQL 17 access-privilege documentation](https://www.postgresql.org/docs/17/functions-info.html) and [role membership catalog](https://www.postgresql.org/docs/17/catalog-pg-auth-members.html).

The explicit last `pg_temp` entry prevents implicit temporary-schema precedence from masking public identity/session tables. See [PostgreSQL 17 search-path semantics](https://www.postgresql.org/docs/17/runtime-config-client.html#GUC-SEARCH-PATH). Tests create real temporary impostor tables and verify public identity lookup plus opposite-session CRUD refusal.

Rejection uses a fixed error and omits the driver exception chain, preventing the gate from forwarding private connection details. Do not enable public debug output or log environment/connection parameter dumps.

The gate runs before DBAL hands the new driver connection to application code. It grants no privileges, mutates no role and automatically performs no migration. The setup exception is lifecycle authority, not an alternative web profile.

## Verification and limits

[URL tests](../../tests/Unit/CandidateDatabaseUrlTest.php), [middleware tests](../../tests/Unit/CandidateDatabaseMiddlewareTest.php) and [Symfony wiring tests](../../tests/Integration/CandidateDatabaseConfigurationTest.php) cover selection, refusal and redaction. [Real PostgreSQL tests](../../tests/Integration/CandidatePostgresGrantsTest.php) create random, temporary login roles only in an explicitly configured disposable `wcp360_test` database. They exercise real session creation/read/revocation/deletion and SQLSTATE 42501 for opposite-surface CRUD, then remove fixture grants/roles. SQLite runs skip these PostgreSQL tests; exact-source Linux CI must execute them before claiming that evidence.

This slice checks the **candidate session-table boundary**. It does not qualify grants for all other business tables, security-definer routines, views or alternate data paths. Privilege changes are checked on the next connection, not continuously fenced on an existing one. Role/grant provisioning, secret distribution, Unix/filesystem isolation, service packaging, TLS trust, maintenance revocation and canonical `wcp_iam` integration remain open.

The shared profile remains deliberately non-isolated development access. Neither profile enables production boot. **M1 remains open**; see the [Core roadmap](../../ROADMAP.md#m1).
