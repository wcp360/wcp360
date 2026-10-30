# V4.0.21.13 --- Surface / Audit / Error Cross Registry

## API

Route/version/method/request DTO/response DTO/idempotency/error mapping.

## CLI

Command/subcommand/options/output/exit codes/dry-run. La CLI appelle le
même Operation contract.

## UI

Action visible/enabled/disabled reason/confirmation/step-up/job
progress.

## Audit

Actor, initiator, impersonation chain, operation, resource, decision,
job, result, reason code, correlation ID. Secrets redacted.

## Errors

Codes stables :
authn/authz/scope/entitlement/quota/capability/lifecycle/
validation/conflict/backend/timeout/rollback/internal. Pas de dépendance
UI à un texte libre backend.
