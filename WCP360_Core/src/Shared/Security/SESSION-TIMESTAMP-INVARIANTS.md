# Common session timestamp invariants

Date: 2026-11-04. Reference: PR #134 / D-TECH-RESOLUTION-0001.
Scope: the Core development candidate, not the frozen SOURCE/platform copy.

Every session kind requires `createdAt <= lastActivityAt <= serverNow` before
calculating expiration. `SessionLifetimePolicy::isExpired()` throws the existing
`ContractViolation` for invalid ordering, including activity in the future.
This closes a common-helper gap: browser admission and the durable candidate
registry already reject future activity, but another helper caller previously
could omit that check. No demonstrated browser-authentication bypass is claimed.

USER, ADMIN, PRIVILEGED_ADMIN and BREAK_GLASS use the same ordering guard.
Existing idle/absolute durations, equality-at-expiration behavior and break-glass
absence of idle timeout remain unchanged. This does not enable privileged or
break-glass sessions and does not change the optional-MFA enrollment policy.

`tests/Unit/SessionTimestampConsistencyTest.php` covers four kinds for future
activity, future creation, activity before creation and valid current activity,
plus all exact idle/absolute deadlines. The pre-change helper failed four future-
activity cases in a 32-check isolated PHP probe; the patched helper passed all 32.
That probe is not the full PHPUnit suite; exact-commit CI remains required.

The derivative is recorded in CANDIDATE-PROVENANCE.json. No API/IPC shape,
permission, database schema, migration, dependency or production mode changes.
