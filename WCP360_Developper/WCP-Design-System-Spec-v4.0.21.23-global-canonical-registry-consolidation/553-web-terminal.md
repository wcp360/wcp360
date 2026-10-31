# 553 --- Web Terminal

## Positionnement

Fonction à risque élevé, optionnelle.

## Scope

Terminal sous identité/scope utilisateur ou session admin explicitement
autorisée ; jamais shell root implicite depuis WCP-USER.

## Sécurité

MFA/step-up possible, timeout, session audit metadata, websocket/origin
controls, copy/paste policy éventuelle, aucune capture de secrets en
clair dans l'audit.

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
