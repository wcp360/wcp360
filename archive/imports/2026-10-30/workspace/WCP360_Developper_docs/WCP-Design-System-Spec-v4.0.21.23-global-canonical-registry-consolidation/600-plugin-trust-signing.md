# 600 --- Plugin Trust, Signing & Permissions

## Objectif

Compléter Plugin Manager/SDK par une chaîne de confiance.

## Manifeste

Publisher, plugin ID/version, WCP compatibility, requested
permissions/capabilities, backend/frontend components, migrations,
network needs.

## Trust

Signature/package integrity, publisher identity policy, install review,
permission diff on update, revocation/disable mechanism.

## Rollback

Update transactionnelle lorsque possible ; rollback package/schema
uniquement si le plugin déclare une stratégie sûre.

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
