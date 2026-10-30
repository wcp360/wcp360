# 871 --- Telemetry Ingestion Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Sources node/services/adapters → normalized telemetry pipeline.
OpenTelemetry/Prometheus/log backends restent adapters selon
architecture existante. Chaque observation possède
timestamp/source/freshness. Donnée absente/stale = unknown, jamais
healthy par défaut. Cardinality, payload size et retention quotas sont
bornés.
