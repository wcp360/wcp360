# V4.0.21.16 --- Job / Decision / Privileged Action Core Registry

## Job requirements

origin operation, actor/initiator, tenant/resource scope, idempotency
key, resource lock, desired generation, retry/backoff, timeout,
cancellation class, progress, result, error and correlation IDs.

## Decisions

Every sensitive operation records reason-coded
ALLOW/DENY/STEP_UP/APPROVAL/ BLOCKED outcome. Denied operations never
create privileged actions.

## Core closed actions

`OS-USER-CREATE`, `OS-USER-LOCK`, `OS-USER-DELETE`,
`MANAGED-DIRECTORY-CREATE`, `MANAGED-DIRECTORY-VERIFY`, `CGROUP-APPLY`,
`FILESYSTEM-QUOTA-APPLY`, `ISOLATION-PROVIDER-APPLY`.

Typed DTO only. No shell string, arbitrary path, arbitrary service name
or arbitrary command argument vector from WCP-USER.
