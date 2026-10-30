# 28 --- Terminal, logs, code & configuration

## Objectif

Encadrer les interfaces techniques à haut risque.

## Règles obligatoires

-   Terminal réservé aux permissions explicites ; session clairement
    identifiée par serveur/utilisateur.
-   LogViewer supporte filtre, recherche, pause/follow, wrap,
    timestamps, severity et téléchargement.
-   ConfigEditor offre validation, diff avant sauvegarde et rollback si
    possible.
-   Les secrets détectés doivent pouvoir être masqués.
-   Toute commande privilégiée est auditée.

## Composants / anatomie

Terminal, LogViewer, CodeEditor, ConfigEditor, DiffViewer, JSONViewer,
YAMLViewer, CommandPreview.

## États à prévoir

connected, reconnecting, disconnected, read-only, permission denied,
validation error, unsaved changes.

## Anti-patterns

Pas de terminal root par défaut ; pas d'édition de config sans
validation/diff ; pas de logs secrets en clair.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
