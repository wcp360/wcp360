# 01 --- Architecture de l'information

## Objectif

Définir l'organisation mentale du panneau, la séparation
WCP-USER/WCP-ADMIN, les niveaux de navigation et la relation entre
ressources.

## Règles obligatoires

-   Une fonction doit avoir un emplacement principal unique ; les
    raccourcis pointent vers cette source.
-   Maximum 2 niveaux visibles dans la sidebar ; les niveaux profonds
    passent par tabs, sous-navigation ou pages ressource.
-   URL stable pour chaque page et ressource importante.
-   Le contexte actif (organisation, serveur, compte, domaine) est
    visible avant toute action mutante.
-   WCP-USER privilégie les tâches d'hébergement ; WCP-ADMIN privilégie
    santé, sécurité, exploitation et orchestration.
-   Les fonctions futures Cluster/Storage/SDN/Compute sont réservées
    dans l'IA sans surcharger la phase 1.

## Composants / anatomie

AppShell, SidebarGroup, Breadcrumb, PageHeader, ContextSwitcher, Tabs,
ResourceHeader, QuickActions, RecentItems.

## Pages et cas couverts

Hiérarchie : Produit → domaine fonctionnel → liste → ressource →
sous-vue. Les pages transversales Task Center, Notifications, Search et
Audit restent accessibles globalement.

## États à prévoir

Contexte absent, contexte inaccessible, ressource supprimée, permission
refusée, fonctionnalité désactivée, maintenance.

## Responsive

Desktop : sidebar persistante. Tablette : sidebar compacte. Mobile :
drawer ; breadcrumb tronqué avec retour explicite.

## Accessibilité

Landmarks sémantiques, skip link, ordre de focus cohérent, titre de
document mis à jour à chaque route.

## RBAC et sécurité

La route n'accorde jamais l'accès. La navigation est filtrée par
capacité, mais l'API réévalue toute permission.

## Anti-patterns

Éviter les mega-menus sans hiérarchie, les mêmes fonctions dans
plusieurs sections, les changements de contexte silencieux et les pages
sans URL propre.

## Critères d'acceptation

Un utilisateur peut retrouver Domaine, SSL, Sauvegardes, Logs et Comptes
en ≤3 interactions depuis n'importe quelle page.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.
