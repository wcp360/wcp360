# V4.0.21.23 --- Global Retry / Recovery Registry

## Retry classes

`NO_RETRY`, `SAFE_RETRY`, `RETRY_WITH_BACKOFF`,
`RECONCILE_BEFORE_RETRY`, `MANUAL_RECOVERY`.

## Recovery classes

`NONE`, `COMPENSATE`, `ROLLBACK_LAST_KNOWN_GOOD`, `RESTORE_CHECKPOINT`,
`RECONCILIATION_REQUIRED`, `RECOVERY_REQUIRED`.

Every durable Job must assign both classes. Retry applies only to errors
proven transient/idempotent. Destructive steps never become retryable
merely because the transport failed.

After reboot/power loss WCP compares desired state, action receipts and
observed state before deciding resume/retry/compensation/manual
recovery.
