# 65 --- Observabilité et modèle de santé

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
