# 65 --- Observabilité et modèle de santé

> **V3.5 — rôle du document : modèle de santé.** L’architecture de télémétrie canonique est `520-telemetry-architecture.md`.


## Objectif V2

Normaliser la provenance et la fraîcheur des métriques.

## Chaque métrique

Nom, valeur, unité, timestamp, période, source, fraîcheur, état de
collecte.

## Health

La santé agrégée documente ses règles. `Healthy` n'est pas déduit de
l'absence d'alertes si la télémétrie est absente.

## Freshness

Afficher `Last updated`. Si les données dépassent le seuil de fraîcheur,
passer à `stale/unknown`.

## Correlation

Alertes, logs, métriques et incidents peuvent se référencer
mutuellement, sans fusionner leurs modèles de données.

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-03-METRICS-MODEL.md`
- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-02-HEALTH-STATE-MODEL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
