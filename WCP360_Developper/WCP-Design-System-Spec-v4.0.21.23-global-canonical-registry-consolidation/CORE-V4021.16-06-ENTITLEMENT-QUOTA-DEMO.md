# V4.0.21.16 --- Entitlement / Quota / DEMO Registry

## Precedence

Platform security floor \> reseller ceiling \> plan entitlement/quota \>
account override allowed by policy \> actor permission/scope.

## Operations

`ENTITLEMENT-SET-UPDATE`, `QUOTA-POLICY-UPDATE`, `ACCOUNT-OVERRIDE-SET`,
`ACCOUNT-COMPLIANCE-RECALCULATE`.

All are scoped and audited; recalculation may use a Job when
system-backed resource reconciliation is required.

## DEMO

`demo_mode` is policy context. Per operation/module result:
`VISIBLE_READONLY`, `SIMULATED`, `HIDDEN`, `DENIED`.

SIMULATED returns explicitly synthetic result and can never enqueue a
privileged Job or allocate system resources.
