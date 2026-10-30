# 525 --- Grafana Integration

## Positionnement

Grafana est une visualisation externe optionnelle, jamais le moteur
obligatoire des dashboards WCP.

## Fonctions

Link/SSO éventuel si une intégration sûre existe, datasource/tenant
mapping selon architecture, dashboard references.

## Règle

Aucune fonctionnalité WCP critique ne dépend de Grafana.

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
