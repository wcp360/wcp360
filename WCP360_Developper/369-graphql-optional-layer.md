# 369 --- GraphQL Optional Layer

## Origine

**\[V3.2\]** GraphQL demandé comme possibilité API-first.

## Positionnement

GraphQL est **optionnel**. Il convient surtout aux lectures agrégées et
outils externes ayant besoin de sélectionner leurs champs. Les mutations
critiques restent reliées aux mêmes services de domaine, permissions,
audit et jobs que REST.

## Interdits

Pas de bypass RBAC/scope; pas d'accès direct aux modèles DB;
depth/complexity limits; introspection policy décidée par environnement.

## Cohérence

REST et GraphQL ne dupliquent jamais la logique de provisioning,
sécurité ou orchestration.

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
