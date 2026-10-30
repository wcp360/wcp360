# 1140 --- IPC V1 Wire Contract

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

## Transport

Unix domain socket `/run/wcp/privd.sock` (nom final configurable dans
`/etc/wcp`). Ownership root + groupe dédié au seul `wcpd`; mode minimal.
PHP users ne sont pas membres du groupe.

## Request

`protocol_version`, `request_id`, `correlation_id`, `action_id`,
`actor_context`, `resource_context`, `params`.

## Response

`protocol_version`, `request_id`, `status`, `result`, `error_code`,
`evidence`.

## Bornes

Message size max configurable avec valeur conservatrice ; deadline
obligatoire ; une requête = une action ; unknown field handling
explicite ; protocol version unsupported = refus.

## Authentification locale

Vérifier peer credentials du socket et identité attendue de `wcpd`. Ne
pas considérer la possession du path socket comme authentification
suffisante.
