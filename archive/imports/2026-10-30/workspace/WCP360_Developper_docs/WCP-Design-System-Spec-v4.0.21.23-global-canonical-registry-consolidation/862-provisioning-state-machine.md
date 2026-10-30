# 862 --- Provisioning State Machine

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

`requested → validated → queued → provisioning → active` ou `failed`.
Suspension et termination sont des workflows séparés. Mapping external
ID ↔ WCP resource ID est unique et audité. Les retries respectent
l'idempotence ; reconciliation corrige les divergences sans duplication
de compte.

## V4.0.9 normative deepening

- `API-409-16-PROVISIONING-REQUEST-MODEL.md`
- `API-409-17-ACCOUNT-PROVISIONING-SAGA.md`
- `API-409-20-EXTERNAL-STATE-RECONCILIATION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
