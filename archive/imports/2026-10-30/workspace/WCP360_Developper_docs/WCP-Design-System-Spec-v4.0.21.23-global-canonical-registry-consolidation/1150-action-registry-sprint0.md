# 1150 --- Action Registry Sprint 0

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

## Actions initiales non destructives

`system.identity.read` --- retourne identité/version OS normalisée.
`system.service.status` --- accepte uniquement `service_id` issu du
ServiceRegistry fermé. `system.time.read` --- diagnostic temps/clock
metadata. `filesystem.path.probe` --- uniquement roots internes
prédéfinies, sans path arbitraire externe. `control.health.probe` ---
vérifications locales allowlistées.

## ServiceRegistry Sprint 0

Uniquement services WCP eux-mêmes (`wcpd`, `wcp-privd`) et
éventuellement PostgreSQL si ownership opérateur explicitement décidé.
Aucun nom d'unité libre.

## Chaque action

Schema, permission/internal caller rule, timeout, idempotency, lock
class, executor, verify, redaction, tests négatifs.
