# WCP360 — State Registry

## Consolidated status

The state model is materially stable. The historical `BLOCKED` job-state disagreement is considered resolved in the current source registry.

`UNKNOWN_OUTCOME` remains a required first-class state and implies reconciliation before unsafe retry.

## Preserved source registry

# WCP360 Canonical State Registry
## Authority
**CANONICAL SOURCE — supersedes earlier isolated state lists.**

## Job states

`QUEUED`
→ `WAITING_DEPENDENCY`
→ `BLOCKED`
→ `RUNNING`
→ `RETRYING`
→ `SUCCEEDED`

Alternative/terminal states:
- `FAILED`
- `CANCEL_REQUESTED`
- `CANCELLED`
- `UNKNOWN_OUTCOME`

Optional transient state:
- `PAUSED`

### Semantics
- `WAITING_DEPENDENCY`: prerequisite job/resource condition not yet complete.
- `BLOCKED`: execution cannot proceed until an operator/policy/state conflict is resolved.
- `UNKNOWN_OUTCOME`: dispatch occurred but completion cannot be proven; reconciliation required before retry.
- `CANCEL_REQUESTED`: cooperative cancellation requested but not yet confirmed.
- `CANCELLED`: job definitively stopped without completing target mutation.

## Resource lifecycle
- `PROVISIONING`
- `VALIDATING`
- `READY`
- `DEGRADED`
- `SUSPENDED`
- `READ_ONLY`
- `MAINTENANCE`
- `DELETING`
- `SOFT_DELETED`
- `RESTORING`
- `RECOVERED`
- `FAILED`

## Approval
- `PENDING`
- `APPROVED`
- `REJECTED`
- `EXPIRED`
- `INVALIDATED`
- `EXECUTION_RECHECK_FAILED`
- `CONSUMED`

## Secret / credential
- `ACTIVE`
- `EXPIRING`
- `EXPIRED`
- `REVOKED`
- `SHOW_ONCE`
- `NEVER_REVEAL`

## HA
- `HEALTHY`
- `DEGRADED`
- `DRAINING`
- `DRAINED`
- `FAILOVER_READY`
- `FAILOVER_BLOCKED`
- `FAILOVER_RUNNING`

## Notification
- `NEW`
- `READ`
- `ACKNOWLEDGED`
- `MUTED`
- `GROUPED`
- `DELIVERY_FAILED`

## Certificate
- `ISSUING`
- `VALID`
- `RENEWAL_DUE`
- `RENEWING`
- `DEPLOYMENT_FAILED`
- `EXPIRED`
- `REVOKED`

## Rule
UI labels may differ, but implementation enums must map deterministically to these canonical states.
