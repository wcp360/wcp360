# 610 --- Audit → SIEM Export

## Objectif

Exporter les événements d'audit vers des systèmes externes.

## Contrat

Structured event schema, event ID, actor, scope, action, target, result,
timestamp, correlation/job IDs, redacted metadata.

## Destinations

Remote syslog, webhook/event collector et futurs SIEM adapters.

## Fiabilité

Buffer/retry, delivery health, backpressure policy, duplicate-safe event
IDs.

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
