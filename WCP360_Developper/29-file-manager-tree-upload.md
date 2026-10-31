# 29 --- File Manager, tree & upload

## Objectif

Définir un explorateur web sûr et performant.

## Règles obligatoires

-   Tree + liste détaillée ; breadcrumb de chemin.
-   Drag/drop, upload multipart, progression, reprise si supportée.
-   Actions : new, rename, copy, move, permissions, archive, extract,
    edit, download, delete.
-   Afficher ownership/permissions quand autorisé.
-   Corbeille optionnelle ou confirmation renforcée selon backend.

## Composants / anatomie

FileTree, FileTable, UploadQueue, FilePreview, PermissionEditor,
PathBreadcrumb.

## États à prévoir

uploading, conflict, quota exceeded, forbidden, locked, partial failure.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
