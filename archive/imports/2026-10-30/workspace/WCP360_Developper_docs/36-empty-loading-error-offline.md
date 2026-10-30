# 36 --- Empty, loading, error & offline

## Objectif

Documenter les états non idéaux comme des écrans de première classe.

## Règles obligatoires

-   Skeleton pour chargement structurel ; spinner pour action courte.
-   Empty state distingue "aucune donnée" et "aucun résultat de filtre".
-   Erreur indique cause utile, impact, retry et identifiant de
    corrélation si disponible.
-   Offline/degraded ne doit pas faire croire qu'une mutation a réussi.

## Composants / anatomie

Skeleton, EmptyState, ErrorState, OfflineBanner, PermissionDenied,
MaintenanceState.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
