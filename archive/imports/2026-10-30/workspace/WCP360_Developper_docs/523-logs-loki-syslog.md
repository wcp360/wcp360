# 523 --- Logs / Loki / Remote Syslog

## Objectif

Pipeline de logs indépendant du backend.

## Adapters

Local journal/files, Loki, remote syslog et futurs collectors.

## Fonctions

Sources, parsing metadata, retention pointer, search capability, export
health.

## Règle

Les logs applicatifs bruts restent soumis à redaction et tenant
isolation.

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
