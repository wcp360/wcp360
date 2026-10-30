# 26 --- Recherche universelle & Command Palette

## Objectif

Permettre de retrouver rapidement ressources, pages, réglages et
commandes autorisées.

## Règles obligatoires

-   Raccourci Ctrl/Cmd+K.
-   Résultats groupés : pages, domaines, comptes, serveurs, actions,
    docs.
-   Respect strict des permissions et du contexte.
-   Actions dangereuses ouvrent leur workflow normal ; aucune exécution
    directe depuis la recherche.
-   Historique local des recherches récentes sans exposer de secrets.

## Composants / anatomie

GlobalSearch, CommandPalette, SearchResultGroup, RecentSearches.

## États à prévoir

idle, typing, loading, no results, partial results, offline, permission
filtered.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
