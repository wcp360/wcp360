# 874 --- Monitoring / Telemetry API, RBAC & Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Permissions : `monitoring.read/manage`, `alerts.read/manage`,
`telemetry.read`, `notifications.manage`. Tests : stale signal, alert
fire/resolve, notification failure/retry, secret redaction,
cardinality/size bounds, retention, user scope, service degradation.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-24-OPERATIONS-DASHBOARDS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
