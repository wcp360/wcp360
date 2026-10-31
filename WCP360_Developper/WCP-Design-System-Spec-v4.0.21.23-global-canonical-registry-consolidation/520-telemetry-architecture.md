# 520 --- Telemetry Architecture

## Origine

**\[V3.4 --- P0\]** Ferme la principale lacune d'observabilité
identifiée.

## Modèle

`Metrics + Logs + Traces + Events + Health` avec timestamps, source,
resource/tenant scope, freshness et quality.

## Pipeline

Collectors/adapters → ingestion → normalized telemetry API →
retention/storage adapter → dashboards/alerts/export.

## Principe

Les dashboards WCP consomment le modèle de télémétrie WCP ; Grafana ou
autre UI externe n'est jamais requis.

## Contrat transversal

-   **WCP-ADMIN** porte l'administration système/infrastructure et les
    politiques globales.
-   **WCP-USER** ne voit que les capacités explicitement déléguées à son
    scope client/revendeur.
-   Les technologies concrètes sont des **adapters/capabilities** ; le
    cœur WCP dépend d'un contrat stable, pas d'un fournisseur unique.
-   Toute mutation sensible exige autorisation backend, audit, preflight
    lorsque pertinent et résultat confirmé.
-   Toute opération longue est un job observable, annulable/retryable
    lorsque techniquement sûr.
-   Secrets et données sensibles sont redacted dans logs, audit,
    notifications, traces et sorties IA.
