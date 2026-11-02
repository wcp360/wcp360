# Candidate recovery codes

An operator can issue a private, short-lived code so an account owner chooses a new password in WCP360. This complements [direct operator recovery](ACCOUNT-PASSWORD.md); the operator does not enter the owner's replacement password.

This is an experimental shared-database candidate. Email request/delivery and notifications are not implemented. Session-isolated PostgreSQL redemption stays unavailable without a separately accepted credential-write operation.

## Configure independent surface keys

| Configuration | Purpose |
| :--- | :--- |
| WCP_ALLOW_CANDIDATE_RECOVERY_CODES=1 | Explicitly enable issuance/redemption in the relevant candidate processes; default zero. |
| WCP_RECOVERY_USER_KEY | Independent random 32-byte key, encoded as 64 hexadecimal characters, for USER codes. |
| WCP_RECOVERY_ADMIN_KEY | A different independent random 32-byte hexadecimal key for ADMIN codes. |

Generate keys privately with a cryptographic generator, for example openssl rand -hex 32. Never copy a test/example key. The USER process receives only its own key; ADMIN receives only its own. Setup needs the key for the explicitly requested target surface. Workers, hosted applications and extensions receive neither.

The context-aware reader reads only a permitted requested surface. There is no fallback to APP_SECRET, WCP_MFA_KEY or the opposite key. A launcher must still avoid exposing unwanted environment variables; the reader cannot remove already exposed secrets. Existing .env files are not rewritten automatically.

## Issue, then hand off privately

Inspect an active account using the existing setup-only maintenance connection:

```bash
WCP_CONTEXT=setup php bin/console wcp:candidate:account:recovery-code alice --surface=user
```

The default operation returns metadata only. Explicit generation requires an interactive terminal, the code enable flag, the appropriate private key and confirmation defaulting to No:

```bash
WCP_CONTEXT=setup WCP_ALLOW_CANDIDATE_RECOVERY_CODES=1 php bin/console wcp:candidate:account:recovery-code alice --surface=user --issue
```

The command displays the secret code once after committing its issuance audit. Deliver it only after verifying the recipient through an established private channel. Do not record the terminal, redirect output to shared logs or send the code to public chat/issues. The command sends no message and generates no absolute URL.

The owner opens /reset-password on the matching USER/ADMIN origin and pastes the code. Codes are submitted only in POST bodies, never in URLs. The form asks for the replacement password twice, leaves all sensitive fields empty after errors, and reuses the existing WCP360 authentication layout. A successful reset redirects to normal login and grants no authenticated session. Enabled MFA is still required there; existing factor secrets and backup codes are preserved.

## Code lifecycle and atomicity

- A candidate code carries its surface, account UUID, issue time and a fresh cryptographic 256-bit nonce. HMAC-SHA-256 authenticates these fields together with current username, contact email, password verifier and MFA revision, using the corresponding independent surface key.
- The code is usable for 900 seconds; future timestamps, malformed values, wrong surfaces and changed state are rejected. Neither raw code nor an additional code verifier is persisted.
- Redemption validates the current account snapshot and computes the configured replacement hash before locking. A conditional credential update checks identity, contact address, active state and expected verifier. The factor revision and expiry are checked again under the account lock.
- Replacement, all selected-account/context session revocations and mandatory audit share one transaction. Audit failure rolls everything back and leaves the unconsumed code usable until expiry.
- The changed verifier invalidates all codes issued against the old password state, including concurrently submitted codes. A successful reset therefore consumes the capability; multiple outstanding codes share the same credential-state epoch.
- Issuance audit records candidate.account.recovery_code_issued; completion records candidate.account.password_reset. Both use a null web actor and target account resource, without code, verifier or password. These events do not establish a canonical operator identity.
- Native PDO keeps verifier-bearing statements out of Doctrine parameter profiling. Credential parameters and exception handling prevent the development renderer from echoing submitted secrets. Browser requests require exact Origin and CSRF.

Five validated-form attempts per client/surface are allowed per 15-minute cache window. Client keys are HMAC-derived; raw client addresses/codes are not limiter keys. This uses the existing candidate cache and is not a distributed anti-abuse or per-account production admission claim. Invalid code attempts do not lock or alter the account.

## Revocation, restore and uncertainty

Codes have no independent inventory or per-code revocation record. Before expiry, revoke the account's outstanding codes by changing its credentials or rotating its surface recovery key. Issuing another code does not itself revoke earlier codes.

Restoring a database snapshot can restore an old verifier and revive a still-unexpired code. **Rotate the affected recovery key after database restore.** Rotating one surface key invalidates that surface's outstanding codes. Process restart/configuration rollout and live revocation fencing remain operator/integration responsibilities.

Caller-owned transactions are refused. PostgreSQL mutation lock/statement timeouts are bounded. An uncertain commit/rollback closes the connection and requires maintenance inspection rather than automatic retry. Inactive accounts cannot redeem codes or be reactivated through this flow.

## Verification and remaining work

[Code semantics](../../tests/Unit/PasswordRecoveryCodeTest.php), [key separation](../../tests/Unit/RuntimeRecoveryKeysTest.php), [service/CLI cases](../../tests/Integration/PasswordRecoveryCodeTest.php), [HTTP journeys](../../tests/Integration/PasswordRecoveryJourneyTest.php) and [real database-role refusals](../../tests/Integration/CandidateDatabaseRolesTest.php) are prepared for GitHub CI. Definitions are not current passing results; local tests are not run for this increment.

Protected public request handling, delivery adapters, reset notifications, recovery inventory, production operator/identity contracts, isolated redemption and all-target qualification remain unfinished. M1 stays open.

Design references: [OWASP password recovery guidance](https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html) and [Django's state-bound token approach](https://github.com/django/django/blob/main/django/contrib/auth/tokens.py). This candidate does not claim conformance or inherit their qualification.

- [Recovery index](README.md)
- [Setup](../../src/Module/Setup/README.md)
- [IdentityAccess](../../src/Module/IdentityAccess/README.md)
