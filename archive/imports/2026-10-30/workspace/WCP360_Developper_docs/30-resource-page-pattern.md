# 30 --- Pattern page ressource

## Objectif

Unifier les pages Domaine, Compte, Serveur, Base, Certificat, Node, VM,
etc.

## Règles obligatoires

-   Header : breadcrumb, identité, statut, métadonnées clés, actions.
-   Tabs recommandés : Overview, Settings, Activity/Logs, Security selon
    ressource.
-   Danger Zone séparée en bas ou onglet dédié pour ressources
    complexes.
-   Les informations les plus critiques restent visibles sans changer
    d'onglet.

## Composants / anatomie

ResourceHeader, MetadataList, StatusBadge, TabNav, ActivityFeed,
DangerZone.

## Exemples WC

Domaine : Overview / DNS / SSL / Email / Logs. Serveur : Overview /
Services / Resources / Network / Logs / Security.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
