# 51 --- WCP-ADMIN : Core System & Security

## Objectif

Couvrir System Settings, Scheduler, Console, Updates, Tuning, License,
Extensions, Support, SSH/Keys, Firewall, WAF, brute force, 2FA/external
auth, password policies, intrusion alerts et Security Advisor.

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

Éditeurs système avec diff, maintenance window, rollback si supporté ;
firewall rule builder ; clés SSH ; politiques auth ; findings de
sécurité avec preuve, sévérité et remediation.

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
