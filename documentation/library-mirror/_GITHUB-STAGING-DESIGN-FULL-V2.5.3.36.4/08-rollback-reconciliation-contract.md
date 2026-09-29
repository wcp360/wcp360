# Rollback & Reconciliation Contract

## Categories

### Revision rollback
Examples:
- Nginx config;
- firewall policy;
- PHP pool config.

Approach:
- preserve known-good previous revision;
- atomically restore;
- validate;
- reload;
- verify.

### Recovery, not rollback
Examples:
- database deletion;
- filesystem data loss;
- certificate revocation.

Approach:
- restore/recreate from backup or source-of-truth;
- do not label as instant rollback.

### Forward-fix
Examples:
- package update without reliable downgrade;
- schema migration that is not reversible.

The UI must say rollback is unavailable.

## Reconciliation

When actual state differs from desired state:
- detect drift;
- record evidence;
- classify harmless/unsafe;
- allow reconcile if safe;
- preserve manual change evidence.

## Unknown outcome reconciliation

For `UNKNOWN_OUTCOME`:
1. inspect actual target state;
2. compare against desired operation;
3. determine completed/not-completed/partial;
4. finalize original job;
5. only then permit retry or compensating action.