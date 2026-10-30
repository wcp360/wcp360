# 43 --- WCP-USER : Files & Backups

## Objectif

Couvrir File Manager, Images, Directory Privacy, Disk Usage, Web Disk,
Backup Wizard, Git et File Restoration.

## Règles obligatoires

-   UX simple et guidée par défaut ; Advanced explicite.
-   Toute ressource affiche quota/limite lorsqu'applicable.
-   Les suggestions IA ne masquent jamais la configuration réelle.
-   Les mutations importantes créent une tâche si elles ne sont pas
    instantanées.
-   Toutes les listes utilisent le Data Grid standard.

## Composants / anatomie

Explorateur, upload queue, permissions, éditeur, visualisation stockage,
wizard sauvegarde/restauration, timeline versions. Restaurer affiche
destination, écrasements et point de retour si disponible.

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

## V4.0.7 normative deepening

- `SECOPS-407-01-FILE-ROOT-PATH-RESOLUTION.md`
- `SECOPS-407-12-BACKUP-RESOURCE-MODEL-DEEP.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
