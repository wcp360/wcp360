# 260 --- Monitoring & Alerting

> **V3.5 — rôle du document : UX monitoring/alerting.** Le pipeline de télémétrie canonique est `520-telemetry-architecture.md`.


## Statut et origine

\[SOURCE\] Metrics dashboard, alerts/thresholds, logs, reports,
integrations.

## Objectif

Centraliser métriques, alertes et règles.

## Pages / sous-vues

Dashboards; Alerts; Rules; Silences if supported; Integrations; Reports.

## Ressources et données

metric/alert, severity, resource, state, freshness, rule.

## Actions opérateur

Acknowledge if model supports; edit rule; integration test.

## Monitoring, santé et logs

All telemetry with units/timestamps/source.

## Sécurité, permissions et audit

Alert configuration permission; audit changes.

## Contrat backend attendu

Observability API; streaming optional.

## États UI obligatoires

Loading, skeleton, empty, no-results, partial data, stale telemetry,
permission denied, service unavailable, maintenance, action pending,
action failed, success confirmé.

## Règles UX communes

-   Afficher serveur/nœud, scope et cible avant toute mutation.
-   Distinguer `Save`, `Apply`, `Reload` et `Restart`.
-   Une modification de configuration sensible présente un diff si
    possible.
-   Toute opération longue crée un job dans Task Center.
-   Ne jamais afficher un pourcentage de progression artificiel.
-   Les commandes/configurations proposées par l'UI restent validées et
    autorisées côté backend.
-   Les secrets sont redacted dans logs, audit et notifications.

## Critères d'acceptation

Unknown/stale data explicit.

## V4.0.10 normative deepening

- `OPS-4010-09-ALERT-RULE-MODEL.md`
- `OPS-4010-10-ALERT-DEDUP-SUPPRESSION.md`
- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-24-OPERATIONS-DASHBOARDS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
