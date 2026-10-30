# 368 --- API-First / REST Coverage Contract

## Origine

**\[V3.2\]** 100 % des actions métier de l'interface doivent être
exécutables par API.

## Décision de base

**REST est le contrat obligatoire du cœur WCP.** GraphQL peut être
ajouté comme façade/query layer ultérieure, mais ne doit pas créer une
seconde logique métier.

## Règle de parité

Tout bouton/formulaire WCP déclenche une opération de domaine exposée
par API ou un endpoint de job. Aucun comportement critique n'existe
uniquement dans le frontend.

## Contrat

Versioning, pagination, filtering, idempotency pour mutations
appropriées, capabilities, correlation IDs, typed errors, async jobs,
ETags/versioning lorsque nécessaire.

## Documentation

OpenAPI générée/maintenue avec exemples, scopes et erreurs. SDKs peuvent
être générés à partir du contrat.

## Tests

La matrice de couverture associe chaque action UI à son endpoint/API
operation.

## Contrat WCP

-   **WCP-ADMIN** configure moteurs, catalogues, politiques, capacités
    et limites.
-   **WCP-USER** expose uniquement les fonctions attribuées au
    client/revendeur.
-   Toute mutation sensible est vérifiée côté backend, auditée et
    corrélée à un job/événement.
-   Les fonctionnalités sont pilotées par `capabilities`; aucune
    intégration tierce n'est supposée installée.
-   États obligatoires : loading, empty, denied, unavailable, partial,
    stale, pending, failed, success confirmé.
