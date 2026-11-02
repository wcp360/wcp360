# Operator-assisted candidate account recovery

This command restores access to an active WCP360 development account when its password is forgotten. It belongs to **Setup maintenance**, independently of an authenticated browser session. It preserves the account's existing MFA mode.

The command is not an email reset flow, a production recovery contract or a way to disable MFA. Candidate execution still requires APP_ENV=dev/test and WCP_CANDIDATE=1.

## Inspect the exact account

From WCP360_Core, use the operator's existing private maintenance configuration and explicit USER or ADMIN surface:

```bash
WCP_CONTEXT=setup php bin/console wcp:candidate:account:recover alice --surface=user
```

The default operation is a read-only account plan. It returns the account ID, username, surface and whether application is enabled. It never returns a password, verifier, contact email or MFA material. Unknown, malformed and disabled accounts are refused.

For PostgreSQL, only version 17 and the disposable wcp360_workbench/wcp360_test databases are accepted. The actual login must be unchanged and must be the maintenance database/table owner or a development superuser; a USER, ADMIN or worker database login cannot become maintenance merely by setting WCP_CONTEXT=setup. Shared SQLite development uses the existing operator-controlled local database file.

In session-isolated mode, supply only the setup process's WCP_DATABASE_SETUP_URL through its private configuration. Never distribute that URL or the recovery enable flag to web, worker, hosted-application or extension processes. Back up the database and apply the current candidate migrations before maintenance.

## Explicitly apply a recovery

```bash
WCP_CONTEXT=setup WCP_ALLOW_CANDIDATE_ACCOUNT_RECOVERY=1 php bin/console wcp:candidate:account:recover alice --surface=user --apply
```

For an administrator, supply that account's username and --surface=admin. This selects an application account, not a Linux user.

Application requires the enable flag, --apply and interactive input. The command asks for confirmation, defaulting to **No**, then asks twice for the new password through hidden prompts. Visible-input fallback is disabled. Passwords are never accepted through an argument, option or environment variable. Passphrase spaces are preserved; the console record ending is removed. The shared policy requires at least 14 UTF-8 characters and at most 256 bytes, and rejects null/invalid UTF-8 bytes and reuse of the current password.

## State transition

| State | Behavior |
| :--- | :--- |
| Target identity | The planned account ID, username and surface are rechecked. A reused username cannot redirect the inspected target to another account. |
| Concurrent credentials | A conditional native-PDO write compares the verifier read before hashing. A concurrent credential change, deactivation or identity change is refused. |
| Sessions | Every registration for the selected account and surface is revoked. Other accounts retain their sessions. |
| MFA | Encrypted seed, factor revision and unused backup codes are preserved. An enabled factor is still required at the next login. |
| Account state | Recovery does not activate a disabled account or change username, contact email, tenancy or permissions. |
| Audit | Session revocations and candidate.account.password_recovered commit with the password. The recovery event has a null web actor and the affected account as resource; it does not impersonate the account or establish a canonical operator identity. |
| Failures | Audit/update failure rolls back the password and revocations. Caller-owned transactions are refused. PostgreSQL mutation locks/statements have bounded timeouts. |

The next login uses the new password and the existing MFA mode. A lost MFA encryption key still requires restoration of the operator-managed key; this command provides no factor-removal shortcut.

## Result and uncertainty

JSON uses schema wcp360.candidate.account-recovery/1. Status is planned, cancelled, recovered, refused or uncertain; production_qualified always remains false. Successful recovery includes the target identity and number of revoked registrations. Interactive prompts precede the final JSON line.

A cancelled confirmation has no effect. Driver/hasher causes are replaced with fixed refusal messages, keeping verifiers and private connection details out of console errors and Doctrine parameter profiling. Application never automatically retries.

If commit or rollback becomes uncertain, the connection is closed. Inspect the actual account state and the recovery audit with maintenance authority before another attempt. Do not infer success from a CLI failure or blindly repeat an uncertain transition.

For an owner-chosen replacement instead of direct operator entry, the [short-lived code flow](RECOVERY-CODES.md) supplies a separate shared-profile candidate. It preserves MFA and still requires an explicitly verified private operator handoff; email delivery is not implemented.

## Verification status and remaining work

[Service and CLI regressions](../../tests/Integration/RecoverAccountPasswordTest.php) define positive/refusal, MFA preservation, rollback, profiling, target reuse, concurrency, cancellation and hidden-input cases. [Real PostgreSQL role regressions](../../tests/Integration/CandidateDatabaseRolesTest.php) define refusal for runtime logins disguised as maintenance.

Exact-source verification is pending in the existing GitHub CI; test definitions and historical passing revisions are not current passing results.

Self-service email delivery, single-use recovery tokens, protected public request/confirmation endpoints, notification and complete production operator/identity integration remain future work. M1 remains open.

- [Recovery index](README.md)
- [Setup ownership](../../src/Module/Setup/README.md)
- [Identity and session boundary](../../src/Module/IdentityAccess/README.md)
