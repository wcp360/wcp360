# 533 --- Rclone / Rsync Transfer Adapters

## Scope

Outils de transfert/synchronisation pouvant servir migration, copie ou
certains workflows backup selon policy.

## Règle

Un transfert Rclone/Rsync n'est pas automatiquement qualifié de backup :
la qualification dépend du manifest, de la rétention et de la
restaurabilité.

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
