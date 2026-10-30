# V4.0.21.7 --- Exact Plan / Entitlement / Quota Operation Registry

## Plan / entitlement

`PLAN-LIST`, `PLAN-READ`, `PLAN-CREATE`, `PLAN-UPDATE`, `PLAN-CLONE`,
`PLAN-ARCHIVE`, `PLAN-IMPACT`, `PLAN-ASSIGN`,
`ENTITLEMENT-EFFECTIVE-READ`, `ENTITLEMENT-OVERRIDE-SET`,
`ENTITLEMENT-OVERRIDE-RESET`.

## Quota / isolation

`QUOTA-USAGE-READ`, `QUOTA-SET`, `QUOTA-RECONCILE`,
`ISOLATION-PROFILE-LIST`, `ISOLATION-PROFILE-CREATE`,
`ISOLATION-PROFILE-UPDATE`, `ISOLATION-PROFILE-ARCHIVE`,
`ISOLATION-PROFILE-ASSIGN`, `ISOLATION-EFFECTIVE-READ`,
`ISOLATION-CAPABILITY-DETECT`, `ISOLATION-RECONCILE`,
`ISOLATION-VERIFY`, `ISOLATION-EVENTS-READ`.

## Contract rule

Every item above is an individual Operation ID. `plan.*`, `quota.*` or
`isolation.*` may be UI grouping labels but never substitutes for an
exact authorization or acceptance row.
