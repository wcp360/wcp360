# V4.0.21.16 --- Core Errors / Audit / Acceptance

## Stable error classes

`AUTHENTICATION_REQUIRED`, `MFA_REQUIRED`, `PERMISSION_DENIED`,
`SCOPE_DENIED`, `ENTITLEMENT_DENIED`, `QUOTA_EXCEEDED`,
`CAPABILITY_UNAVAILABLE`, `LIFECYCLE_BLOCKED`, `CONFLICT`,
`STALE_VERSION`, `SECURITY_FLOOR_DENIED`, `BACKEND_FAILURE`,
`RECONCILIATION_REQUIRED`.

## P0 acceptance

Cross-tenant IDs denied before mutation; reseller cannot exceed ceiling;
plan cannot lower security floor; DEMO creates no system Job;
impersonation preserves initiator; suspended account blocks governed
mutations; account downgrade preserves data and becomes NONCOMPLIANT;
duplicate idempotency key does not duplicate OS user/root; failed
privileged action reconciles observed state; secrets never appear in
audit payload.
