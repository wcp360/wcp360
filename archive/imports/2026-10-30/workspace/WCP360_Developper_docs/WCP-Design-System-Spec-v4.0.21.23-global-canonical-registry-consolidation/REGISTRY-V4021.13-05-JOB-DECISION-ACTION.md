# V4.0.21.13 --- Job / Decision / Privileged Action Registry

## Job

Type stable, originating operation, resource lock, retry policy,
timeout, cancellation semantics, progress model, result/error model,
idempotency key.

## Decision

Policy result durable pour les opérations à risque :
allow/deny/requires-step-up/ requires-approval/blocked-by-state avec
reason codes.

## Privileged Action

Closed action name, typed input DTO, validation, allowed caller job(s),
filesystem/service boundaries, timeout, rollback/compensation and audit.

## Interdit

`RUN-SHELL`, `EXEC-RAW`, `WRITE-ARBITRARY-PATH`,
`SERVICE-ARBITRARY-COMMAND` comme escape hatch.
