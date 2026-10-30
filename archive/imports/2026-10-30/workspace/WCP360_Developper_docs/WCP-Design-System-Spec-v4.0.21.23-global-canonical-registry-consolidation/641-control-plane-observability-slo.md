# 641 --- Control Plane Observability & SLO

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Signaux

API availability/latency/errors, auth failures, job queue age/depth,
worker failures, DB/state health, agent heartbeat freshness, event
delivery, secret-store availability.

## Séparation

La santé du control plane est distincte de la santé des serveurs
hébergés.

## À décider

SLO/SLA chiffrés, retention et alert routing. Aucun seuil arbitraire
n'est inventé dans cette passe.

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-03-METRICS-MODEL.md`
- `OPS-4010-06-LOGGING-CANONICAL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
