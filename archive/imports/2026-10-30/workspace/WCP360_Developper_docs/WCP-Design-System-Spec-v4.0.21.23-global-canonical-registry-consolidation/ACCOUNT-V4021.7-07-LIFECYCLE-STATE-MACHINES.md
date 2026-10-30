# V4.0.21.7 --- Account / Reseller / Plan Lifecycle State Machines

## Hosting account

`PROVISIONING → READY ↔ SUSPENDED → DELETING → DELETED`. `ERROR` is
recoverable through reconcile/repair. `NONCOMPLIANT` blocks creation
that worsens the violation but does not destroy existing resources.

## Reseller

`ACTIVE ↔ SUSPENDED → CLOSING → CLOSED`. Suspending a reseller does not
automatically delete descendants. The exact descendant access policy is
explicit and reversible.

## Plan

`DRAFT → ACTIVE → ARCHIVED`. Archived plans remain resolvable for
historical assignments until migration.

## Trial

`ACTIVE → EXPIRING → GRACE → SUSPENDED → PURGE_ELIGIBLE`. Conversion to
STANDARD runs impact/reconciliation before mode change.

## Delete principle

State transition precedes irreversible deletion. Destructive boundaries
are named and acceptance-tested.
