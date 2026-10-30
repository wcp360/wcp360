# 377 --- Global Search & Quick Launcher

## Origine

**\[V3.2\]** Formalise la Command Palette déjà présente dans
`26-search-command-palette.md`.

## Raccourci

`Cmd + K` macOS / `Ctrl + K` autres plateformes, avec alternative
accessible sans clavier.

## Recherche

Domaines, comptes, bases, boîtes mail, applications, serveurs/nœuds
selon scope, pages/settings, commandes autorisées.

## Sections

Recent; Resources; Navigation; Actions. Résultats tenant-aware et
permission-aware.

## Actions rapides

Créer domaine/boîte/DB, ouvrir logs, lancer scan ou deployment
uniquement si permission; actions destructives ne s'exécutent jamais
immédiatement depuis la palette.

## Performance

Debounce/cache/index backend selon architecture; résultats progressifs;
aucun leak cross-tenant.

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
