# 700 --- Control Plane Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Ressources cœur

`Node`, `Capability`, `Job`, `JobAttempt`, `DomainEvent`, `AuditEvent`,
`ServiceAccount`, `CredentialRef`, `SystemService`, `AdapterInstance`.

## Identifiants

IDs opaques et stables : `nod_`, `cap_`, `job_`, `evt_`, `aud_`, `svc_`,
`sad_`. Les noms Linux, hostnames et paths ne sont jamais des clés
métier primaires.

## État commun

`id`, `tenant_id` nullable pour ressources globales, `node_id`,
`desired_state`, `observed_state`, `health`, `generation`, `created_at`,
`updated_at`.

## Concurrence

Toute mutation d'une ressource versionnée compare `generation`/ETag. Un
conflit retourne une erreur de concurrence plutôt qu'un écrasement
silencieux.
