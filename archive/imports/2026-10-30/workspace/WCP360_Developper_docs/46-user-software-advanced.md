# 46 --- WCP-USER : Software & Advanced

## Objectif

Couvrir libs/runtime, Site Software, Optimize Website, MultiPHP, INI
Editor, Terminal, Cron, Track DNS, Indexes, Error Pages, Apache Handlers
et MIME Types.

## Règles obligatoires

-   UX simple et guidée par défaut ; Advanced explicite.
-   Toute ressource affiche quota/limite lorsqu'applicable.
-   Les suggestions IA ne masquent jamais la configuration réelle.
-   Les mutations importantes créent une tâche si elles ne sont pas
    instantanées.
-   Toutes les listes utilisent le Data Grid standard.

## Composants / anatomie

Sélection PHP par domaine, INI avec presets + advanced, cron wizard +
expression, terminal permissionné, diagnostics DNS, édition erreurs.
Changements runtime montrent compatibilité/risque.

## États à prévoir

loading, empty, quota warning, quota exceeded, provisioning, suspended,
failed, permission denied.

## Responsive

Prioriser consultation et actions urgentes sur mobile ; éditeurs
complexes peuvent proposer une vue simplifiée.

## RBAC et sécurité

Client limité à son scope ; revendeur peut gérer les comptes autorisés.
Les secrets ne traversent jamais les scopes.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
