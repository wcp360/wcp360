# 1132 --- HTTP Request Lifecycle

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

Ordre middleware WCP-ADMIN/API : trusted proxy normalization → request
ID → security headers → rate limit coarse → session/token auth → CSRF si
session mutation → authorization context → handler → response
normalization → audit hooks.

`correlation_id` est accepté uniquement selon règles définies ; sinon
généré. Les valeurs externes sont validées/bornées avant propagation
vers logs/jobs/IPC.

## V4.0.12 normative deepening

- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
