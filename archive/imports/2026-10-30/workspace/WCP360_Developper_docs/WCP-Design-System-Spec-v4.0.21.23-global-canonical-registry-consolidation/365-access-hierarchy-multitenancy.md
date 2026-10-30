# 365 --- Root → Reseller → User Multi-tenancy

## Origine

**\[V3.2\]** Hiérarchie explicite demandée pour un niveau WHM/Plesk.

## Hiérarchie

`WCP-ADMIN Root/Admin → WCP-USER Reseller → WCP-USER User/Client`.

## Principe

Le rôle ne suffit pas : toute autorisation combine rôle + scope +
ownership + capability. Un revendeur n'accède jamais aux ressources d'un
autre revendeur.

## Scopes

Infrastructure/server; reseller; account; subscription/hosting;
domain/resource. Le Context Switcher affiche le scope courant et
invalide les sélections incompatibles.

## Délégation

WCP-ADMIN définit ce qu'un revendeur peut créer/déléguer. Le revendeur
ne peut jamais déléguer une capacité qu'il ne possède pas.

## Isolation

Toutes les requêtes backend sont tenant-aware; l'UI n'est jamais la
frontière d'isolation.

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
