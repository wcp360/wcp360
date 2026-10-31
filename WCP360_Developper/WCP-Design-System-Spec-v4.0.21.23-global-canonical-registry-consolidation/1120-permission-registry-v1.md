# 1120 --- Permission Registry V1

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

## Sprint 0

`identity.read`, `identity.manage`, `roles.read`, `roles.manage`,
`sessions.manage`, `mfa.manage`, `service_accounts.read`,
`service_accounts.manage`, `nodes.read`, `nodes.manage`,
`capabilities.read`, `jobs.read`, `jobs.cancel`, `audit.read`,
`audit.export`, `control_plane.maintenance`.

## Métadonnées obligatoires

Chaque entrée : `permission_id`, module, description, scope
(`global|tenant|resource`), surfaces (`admin|user|api`),
reseller_delegable, step_up, risk_class.

## Rôles bootstrap

`system_owner`: toutes permissions Sprint 0, non assignable par
reseller. `system_auditor`: lecture nodes/jobs/audit/identity sans
mutation. Aucun rôle WCP-USER n'est nécessaire au Sprint 0.

Les futurs rôles métier seront des données versionnées construites à
partir de permissions, pas des conditions codées en dur.
