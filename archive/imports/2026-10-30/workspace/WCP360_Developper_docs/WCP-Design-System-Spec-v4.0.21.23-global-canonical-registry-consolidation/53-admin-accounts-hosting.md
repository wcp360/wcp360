# 53 --- WCP-ADMIN : Accounts & Hosting

## Objectif

Couvrir clients, revendeurs, domaines, quotas/ressources,
templates/skeletons, shell access et migrations.

## Règles obligatoires

-   Densité élevée autorisée, jamais au détriment de la compréhension du
    risque.
-   Toute action root affiche cible et scope exacts.
-   Start/stop/restart, update, firewall, migration et destruction sont
    audités.
-   Prévisualiser diff/impact avant changements de configuration quand
    possible.
-   Les tâches longues passent par Task Center.

## Composants / anatomie

Account lifecycle, suspend/unsuspend, package assignment,
CPU/RAM/storage/bandwidth limits, impersonation banner, migration wizard
et validation post-migration.

## États à prévoir

healthy, degraded, critical, unknown, maintenance, disconnected, quorum
risk, pending, running, failed.

## Responsive

Desktop prioritaire ; mobile destiné surtout à supervision et actions
d'urgence sûres.

## RBAC et sécurité

Scopes serveur/cluster/projet. Step-up auth recommandé pour opérations
critiques. Impersonation et sudo-like elevation toujours visibles et
audités.

## Definition of Done locale

-   Tous les états sont maquettés et testables.
-   Light/Dark/System utilisent les mêmes tokens sémantiques.
-   Clavier, focus, lecteurs d'écran et zoom 200 % sont vérifiés.
-   Les permissions sont contrôlées côté backend et reflétées
    correctement dans l'UI.
-   Les actions mutantes produisent feedback, audit et gestion d'erreur.
-   Aucun écran ne dépend uniquement de la couleur pour communiquer un
    état.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-01-HOSTING-ACCOUNT-DEEP.md`
- `HOSTING-404-05-ACCOUNT-SUSPEND-TERMINATE-RECOVERY.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
