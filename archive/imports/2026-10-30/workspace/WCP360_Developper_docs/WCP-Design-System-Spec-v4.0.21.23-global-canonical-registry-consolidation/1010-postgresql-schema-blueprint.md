# 1010 --- PostgreSQL Schema Blueprint

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

## Schémas logiques

`control`, `identity`, `hosting`, `dns`, `mail`, `database_mgmt`,
`backup`, `security`, `apps`, `billing`, `observability`, `audit`.

## Tables fondatrices

Control : nodes, capabilities, jobs, job_attempts, outbox_events,
adapter_instances. Identity : identities, role_assignments, sessions,
service_accounts, credential_refs, mfa_enrollments. Hosting : accounts,
resellers, plans, entitlements, domains, websites. Audit : audit_events.

## Migrations

Une migration est immutable après release. Nommage
`YYYYMMDDHHMMSS_description`. Expand/contract pour changements
nécessitant compatibilité N/N-1. Aucun secret brut en seed.
