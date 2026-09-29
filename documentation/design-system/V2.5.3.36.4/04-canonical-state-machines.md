# Canonical State Machines — Design Freeze

## resource_lifecycle

`PROVISIONING` → `VALIDATING` → `READY` → `DEGRADED` → `SUSPENDED` → `MAINTENANCE` → `DELETING` → `SOFT_DELETED` → `RESTORING` → `FAILED`

## job

`QUEUED` → `WAITING_DEPENDENCY` → `RUNNING` → `PAUSED` → `RETRYING` → `SUCCEEDED` → `FAILED` → `CANCELLED` → `UNKNOWN_OUTCOME`

## approval

`PENDING` → `APPROVED` → `REJECTED` → `EXPIRED` → `INVALIDATED` → `EXECUTION_RECHECK_FAILED` → `CONSUMED`

## credential_secret

`ACTIVE` → `EXPIRING` → `EXPIRED` → `REVOKED` → `SHOW_ONCE` → `NEVER_REVEAL`

## ha

`HEALTHY` → `DEGRADED` → `DRAINING` → `DRAINED` → `FAILOVER_READY` → `FAILOVER_BLOCKED` → `FAILOVER_RUNNING`

## notification

`NEW` → `READ` → `ACKNOWLEDGED` → `MUTED` → `GROUPED` → `DELIVERY_FAILED`

## certificate

`ISSUING` → `VALID` → `RENEWAL_DUE` → `RENEWING` → `DEPLOYMENT_FAILED` → `EXPIRED` → `REVOKED`

## State-machine invariants

- UI labels may be friendlier than backend enums, but mappings must be deterministic.
- `UNKNOWN_OUTCOME` is mandatory for timeout/disconnect cases where execution may have happened.
- approval and privilege states must be revalidated at execution time.
- destructive actions cannot silently jump directly from READY to deleted state without job/audit evidence.