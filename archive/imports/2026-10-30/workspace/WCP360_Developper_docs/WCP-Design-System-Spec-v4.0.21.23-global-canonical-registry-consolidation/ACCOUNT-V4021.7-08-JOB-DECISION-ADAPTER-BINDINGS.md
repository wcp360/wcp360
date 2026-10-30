# V4.0.21.7 --- Account/Plan/Quota Execution Bindings

## Required jobs

-   `account.provision`
-   `account.suspend`
-   `account.resume`
-   `account.delete`
-   `account.reconcile`
-   `reseller.provision`
-   `reseller.ceiling.reconcile`
-   `plan.assignment.reconcile`
-   `quota.apply`
-   `quota.reconcile`
-   `isolation.apply`
-   `isolation.reconcile`

## Decisions

-   `DEC-ACCOUNT-PROVISION-001`
-   `DEC-ACCOUNT-SUSPEND-001`
-   `DEC-ACCOUNT-DELETE-001`
-   `DEC-PLAN-IMPACT-001`
-   `DEC-CEILING-IMPACT-001`
-   `DEC-QUOTA-ENFORCEMENT-001`
-   `DEC-ISOLATION-BACKEND-001`

## Adapter boundary

Account provisioning may use closed actions for Unix identity,
filesystem root, cgroup/systemd placement and quota backend. No generic
shell action is allowed.

A plan assignment invokes only adapters required by the computed diff.
Pure control-plane entitlement changes remain `NO_JOB` unless
reconciliation of system state is required.
