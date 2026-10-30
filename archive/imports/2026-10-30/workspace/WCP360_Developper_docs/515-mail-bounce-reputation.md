# 515 --- Bounce / Reputation Diagnostics

## Fonctions

Classification des bounces/rejects à partir des logs/DSN disponibles,
trends, affected domain/account/IP, reputation observations.

## Règle

Une blocklist ou réputation externe est une observation tierce
horodatée, jamais une vérité absolue.

## Actions

Investigate, throttle according to policy, open account/security
findings.

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
