# 33 --- Task & Job Center

## Objectif

Donner une visibilité globale sur toutes les opérations asynchrones.

## Règles obligatoires

-   Chaque tâche a ID, type, cible, initiateur, début, durée, statut et
    logs.
-   Statuts : queued, starting, running, waiting, completed, warning,
    failed, cancelled.
-   Une navigation de page ne doit pas interrompre une tâche serveur.
-   Retry uniquement si idempotence/stratégie backend le permet.
-   Tâches critiques accessibles depuis notification et ressource.

## Composants / anatomie

TaskIndicator, TaskDrawer, TaskList, TaskDetail, Progress, TaskLog.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
