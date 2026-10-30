# 870 --- Monitoring / Telemetry Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Ressources : `MetricSeriesRef`, `LogStreamRef`, `TraceRef`,
`HealthCheck`, `AlertRule`, `AlertInstance`, `NotificationRoute`,
`SloDefinition`, `IncidentRef`.

Telemetry data et configuration WCP sont séparées : PostgreSQL conserve
metadata/configuration, pas nécessairement les séries brutes.

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-24-OPERATIONS-DASHBOARDS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
