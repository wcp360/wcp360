# 1110 --- PostgreSQL Initial DDL Specification

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

## Extensions et principes

Utiliser types PostgreSQL natifs ; timestamps `timestamptz`; IDs
internes UUID/ULID selon implémentation, IDs publics opaques séparés.
Aucune FK cross-tenant sans scope vérifiable.

## Schémas Sprint 0

`control`, `identity`, `audit`.

## `control.nodes`

`id`, `public_id`, `hostname`, `status`, `health`, `generation`,
`last_seen_at`, `created_at`, `updated_at`. Unique : `public_id`. Index
: status, health, last_seen_at.

## `control.node_capabilities`

`id`, `node_id`, `capability`, `version`, `state`, `metadata_json`,
`observed_at`. Unique `(node_id, capability)`.

## `control.jobs`

`id`, `public_id`, `tenant_id nullable`, `actor_ref`, `action_id`,
`target_type`, `target_id`, `state`, `idempotency_key nullable`,
`correlation_id`, `input_json`, `result_json nullable`,
`error_code nullable`, `requested_at`, `started_at`, `finished_at`,
`timeout_at`, `attempt_count`. Unique idempotency dans le scope défini
par service. Index state/requested_at, correlation_id, target.

## `control.job_attempts`

`id`, `job_id`, `attempt_no`, `worker_ref`, `state`, `started_at`,
`finished_at`, `error_code`, `evidence_json`. Unique
`(job_id, attempt_no)`.

## `control.outbox_events`

`id`, `event_id`, `event_type`, `schema_version`, `aggregate_type`,
`aggregate_id`, `tenant_id nullable`, `correlation_id`,
`causation_id nullable`, `payload_json`, `created_at`,
`published_at nullable`, `attempt_count`, `next_attempt_at nullable`.
Unique `event_id`.

## Identity

`identity.identities`, `identity.password_credentials`,
`identity.sessions`, `identity.mfa_enrollments`, `identity.roles`,
`identity.permissions`, `identity.role_permissions`,
`identity.role_assignments`, `identity.service_accounts`,
`identity.service_account_credentials`.

Les mots de passe sont stockés uniquement sous forme de hash moderne
avec paramètres ; recovery codes hashés ; token/session secrets non
stockés en clair.

## Audit

`audit.audit_events`: `id`, `public_id`, `occurred_at`, `actor_type`,
`actor_id`, `effective_actor_id nullable`, `tenant_id nullable`,
`action`, `target_type`, `target_id`, `result`, `request_id`,
`correlation_id`, `source_ip nullable`, `metadata_json`. Append-oriented
; index temps, actor, target, correlation.

## Migration initiale

Une migration bootstrap crée les schémas/tables/contraintes ; les seeds
ne contiennent que Permission Registry et rôles système nécessaires,
jamais credentials.
