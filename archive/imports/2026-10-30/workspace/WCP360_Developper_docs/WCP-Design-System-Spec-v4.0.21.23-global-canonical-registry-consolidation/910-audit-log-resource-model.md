# 910 --- Audit / Log Resource Model

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Ressources : `AuditEvent`, `SecurityEvent`, `LogSource`, `LogStreamRef`,
`RetentionPolicy`, `ExportDestination`, `LegalHold` si produit l'active.

Audit est append-oriented et distinct des logs techniques. Les logs
applicatifs ne remplacent jamais l'audit métier.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
