# 41 --- WCP-USER : Email

## Objectif

Couvrir Email Accounts, Forwarders, Routing, Autoresponders, Default
Address, Mailing Lists, Track Delivery, Global/Account Filters,
Deliverability, Importer, Spam, GPG, BoxTrapper, Calendars/Contacts et
Disk Usage.

## Règles obligatoires

-   UX simple et guidée par défaut ; Advanced explicite.
-   Toute ressource affiche quota/limite lorsqu'applicable.
-   Les suggestions IA ne masquent jamais la configuration réelle.
-   Les mutations importantes créent une tâche si elles ne sont pas
    instantanées.
-   Toutes les listes utilisent le Data Grid standard.

## Composants / anatomie

Pages liste + ressource mailbox ; wizard création ; builder de filtre ;
timeline Track Delivery ; health SPF/DKIM/DMARC ; quotas. Les
suggestions IA restent explicables et réversibles.

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

## V4.0.6 normative deepening

- `MAIL-406-01-MAIL-RESOURCE-MODEL-DEEP.md`
- `MAIL-406-27-MAIL-RBAC-IMPERSONATION-AUDIT.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
