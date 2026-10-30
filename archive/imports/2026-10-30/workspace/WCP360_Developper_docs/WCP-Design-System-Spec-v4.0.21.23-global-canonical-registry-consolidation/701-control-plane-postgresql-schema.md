# 701 --- Control Plane PostgreSQL Schema

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Tables minimales V1

`nodes`, `node_capabilities`, `jobs`, `job_attempts`, `domain_events`,
`outbox_events`, `audit_events`, `service_accounts`,
`service_account_credentials`, `adapter_instances`, `schema_migrations`.

## Contraintes

UUID/ULID interne possible derrière les IDs publics ; FK explicites ;
timestamps UTC ; indexes sur état/cible/created_at ; uniqueness sur
idempotency key dans son scope.

## Jobs

`jobs`: action, actor, target_type/id, state, idempotency_key,
correlation_id, requested_at, started_at, finished_at, result/error
references.

## Outbox

L'événement métier et l'entrée outbox sont écrits dans la même
transaction que la mutation métier. Le dispatcher marque
livraison/tentatives sans réécrire le fait métier.

## Secrets

Aucun secret brut dans ces tables : uniquement `CredentialRef`/metadata
conformément au SecretStore.
