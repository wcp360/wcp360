# 54 --- WCP-ADMIN : Email & Disaster Recovery

## Objectif

Couvrir mail accounts/routing/auth, spam/greylisting, queue/delivery,
troubleshooting, webmail, lists, repair permissions, backup scheduler,
restore, incremental/differential, remote destinations, snapshots et
encryption.

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

Mail queue explorer, delivery diagnostics, backup policy builder,
destination health, restore preview, encryption/key status, RPO/RTO
labels.

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

## V4.0.6 normative deepening

- `MAIL-406-24-MAIL-BACKUP-RESTORE.md`
- `MAIL-406-25-MAIL-MIGRATION-IMPORT.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`
- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
