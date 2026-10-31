# 40 --- WCP-USER : Shell & Dashboard

## Objectif

Dashboard client centré sur domaines, stockage, bande passante, email,
SSL, sauvegardes, alertes et raccourcis. Widgets personnalisables mais
avec socle obligatoire : santé hébergement, quotas, alertes critiques.

## Règles obligatoires

-   UX simple et guidée par défaut ; Advanced explicite.
-   Toute ressource affiche quota/limite lorsqu'applicable.
-   Les suggestions IA ne masquent jamais la configuration réelle.
-   Les mutations importantes créent une tâche si elles ne sont pas
    instantanées.
-   Toutes les listes utilisent le Data Grid standard.

## Composants / anatomie

Widgets : ResourceUsage, DomainsHealth, SSLExpirations, BackupStatus,
MailQuota, RecentActivity, QuickActions. Sidebar suit les 10 groupes du
document source.

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
