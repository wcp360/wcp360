# 363 --- File Manager UX, Code Editor & Preview

## Origine

**\[V3.2\]** Complète `350-file-manager-complete.md`.

## Code Editor

Éditeur de type IDE web : coloration syntaxique, numéros de ligne,
recherche/remplacement, indentation, raccourcis, détection encodage,
dirty state, conflit de version et sauvegarde atomique lorsque le
backend le permet. Monaco peut être un choix d'implémentation, pas une
obligation de Design System.

## Preview

Images compatibles, texte, JSON/Markdown en lecture, métadonnées. Les
formats actifs/dangereux ne sont jamais exécutés dans le contexte
privilégié du panel.

## Archives

Créer/extract `.zip`, `.tar`, `.tar.gz` selon backend; protection path
traversal/Zip Slip, quotas, nombre maximal d'entrées et taille
décompressée.

## Permissions

Mode symbolique/numérique avec explication; owner/group uniquement si
capability. Prévisualiser l'impact récursif.

## Upload

Drag/drop, file picker, progression réelle, reprise/chunks uniquement si
backend le supporte, collision policy explicite.

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
