# 513 --- BIMI

## Positionnement

Fonction optionnelle de délivrabilité/branding.

## Scope

Record BIMI, asset reference/validation basique et certificats associés
uniquement si l'organisation dispose des prérequis. WCP ne garantit
jamais l'affichage du logo par les fournisseurs mail.

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
