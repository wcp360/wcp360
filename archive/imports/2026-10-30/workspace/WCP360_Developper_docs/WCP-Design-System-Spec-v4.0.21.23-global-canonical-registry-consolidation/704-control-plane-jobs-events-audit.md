# 704 --- Jobs, Events & Audit Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## États Job

`queued`, `running`, `waiting`, `succeeded`, `failed`,
`cancel_requested`, `cancelled`, `timed_out`.

## Événements

Nommage passé : `node.enrolled`, `node.capabilities_changed`,
`job.started`, `job.succeeded`, `job.failed`, `service_account.created`,
`credential.rotated`.

## Audit

Toute mutation conserve actor, auth method, tenant/scope, target,
action, result, request/correlation IDs, timestamp et metadata redacted.

## Retry

Retry seulement si action déclarée idempotente/retry-safe par son Action
Registry. Jamais de retry aveugle d'une mutation root.
