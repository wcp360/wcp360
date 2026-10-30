# V4.0.21.23 --- Global Job / RPC / Adapter Registry

Every system-backed mutation MUST resolve this chain:

Operation ID → Policy Decision → Job Type → Lock Set → Retry Class →
Timeout Class → Cancellation Class → Privileged RPC/Closed Action →
Provider Adapter → Verification Evidence → Compensation/Recovery →
Audit.

## Job classes

-   J0 synchronous control-plane only;
-   J1 short durable mutation;
-   J2 long durable operation;
-   J3 destructive/high-risk durable operation;
-   J4 recovery/update orchestration.

## Invariant

Process exit is never sufficient evidence of success. The Job records
action receipt plus observed verification. Duplicate delivery uses
idempotency and reconciliation rather than duplicating OS/system
effects.
