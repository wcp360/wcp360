# 560 --- MFA Recovery & Break-glass

## Fonctions

Recovery codes, lost-device workflow, factor reset, admin-assisted
recovery, break-glass policy, optional requirement for multiple enrolled
methods.

## Sécurité

Recovery codes hashed/stored safely, one-time use, regeneration
invalidates prior set ; reset MFA est fortement audité et peut exiger
step-up/dual control selon policy.

## UX

L'utilisateur voit clairement facteurs actifs, moyens de récupération et
risque avant suppression du dernier facteur.

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
