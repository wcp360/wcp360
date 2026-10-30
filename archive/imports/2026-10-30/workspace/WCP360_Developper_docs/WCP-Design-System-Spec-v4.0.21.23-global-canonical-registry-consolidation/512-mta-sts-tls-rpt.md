# 512 --- MTA-STS / TLS-RPT

## Fonctions

Policy MTA-STS, policy hosting/status, DNS records associés, TLS-RPT
destination, validation.

## Workflow

Preflight MX/TLS → generate policy → publish endpoint/DNS selon
ownership → validate observed state.

## Règle

Ne pas activer une politique `enforce` sans validation explicite de la
chaîne mail.

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
