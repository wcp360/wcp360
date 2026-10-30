# 55 --- WCP-ADMIN : Monitoring, Packages & API

## Objectif

Couvrir process/service monitor, resource analytics, logs, alerts, task
queue, dashboard widgets, hosting packages, feature manager, locales,
installer, plugins, branding, API/tokens, webhooks, SDK, UI builder, CLI
et docs.

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

Monitoring dashboards, alert rules, package matrix, feature toggles,
token scopes, webhook delivery logs/retry, API audit and developer
surfaces.

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

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-24-OPERATIONS-DASHBOARDS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
