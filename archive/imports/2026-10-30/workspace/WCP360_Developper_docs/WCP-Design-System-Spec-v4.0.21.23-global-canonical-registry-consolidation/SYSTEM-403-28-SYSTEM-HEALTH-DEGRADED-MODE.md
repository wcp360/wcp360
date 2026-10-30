# V4.0.3 --- System Health, Degraded Mode & Dependency Failure

## Statut

**NORMATIVE --- V4.0.3 SYSTEM & OS DEEP SPECIFICATION --- NO CODE**

Cette spécification instancie les fondations V4.0.2 pour WCP-ADMIN. Elle
décrit le comportement attendu sans imposer de framework ni produire
d'artefact exécutable.

## Invariants communs

-   WCP-USER n'obtient jamais de primitives d'administration hôte.
-   Toute mutation système est autorisée, bornée, auditable et exécutée
    via une action privilégiée fermée.
-   Aucun champ UI/API ne devient une commande shell, un chemin absolu
    arbitraire, un nom d'unité arbitraire ou une liste d'arguments root.
-   Toute mutation suit **preflight → plan/diff → lock → apply → verify
    → reconcile/rollback → audit**.
-   Timeout ou perte de réponse = état ambigu jusqu'à vérification.
-   L'état affiché provient de l'observation effective et porte un
    timestamp/freshness.

## Health domains

OS, clock, storage, memory, package DB, service manager,
network/resolver, firewall, WCP daemons, control-plane DB et critical
hosting services.

## Aggregation

Le health global est dérivé mais conserve les causes. Un composant
`unknown` empêche un `healthy` absolu si ce composant est critique.

## Degraded mode

Lecture et diagnostics restent disponibles autant que possible. Les
mutations dépendantes d'un composant unhealthy sont bloquées ou queued
selon contrat.

## Staleness

Chaque observation porte collected_at/age. Une valeur ancienne devient
stale puis unknown selon TTL par métrique.

## Operator UX

Afficher cause, impact, evidence timestamp, remediation sûre et lien
vers Jobs/logs/audit ; pas seulement un badge rouge.

## V4.0.10 normative deepening

- `OPS-4010-02-HEALTH-STATE-MODEL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
