# 232 --- Placement, Migration & Rolling Updates

## Statut et origine

\[SOURCE\] Placement & Scheduling, Live Migration, Rolling Updates.

## Objectif

Orchestrer déplacement et maintenance sans bloquer l'UI.

## Pages / sous-vues

Placement policies; migrations; rolling operations.

## Ressources et données

workload, source/target, constraints, state.

## Actions opérateur

Plan/migrate; rolling update; cancel if supported.

## Monitoring, santé et logs

Progress from orchestrator, failures.

## Sécurité, permissions et audit

Permissions + preflight + audit.

## Contrat backend attendu

All operations jobs; no fake progress.

## États UI obligatoires

Loading, skeleton, empty, no-results, partial data, stale telemetry,
permission denied, service unavailable, maintenance, action pending,
action failed, success confirmé.

## Règles UX communes

-   Afficher serveur/nœud, scope et cible avant toute mutation.
-   Distinguer `Save`, `Apply`, `Reload` et `Restart`.
-   Une modification de configuration sensible présente un diff si
    possible.
-   Toute opération longue crée un job dans Task Center.
-   Ne jamais afficher un pourcentage de progression artificiel.
-   Les commandes/configurations proposées par l'UI restent validées et
    autorisées côté backend.
-   Les secrets sont redacted dans logs, audit et notifications.

## Critères d'acceptation

Migration affiche préconditions et résultat final vérifié.

## V4.0.11 normative deepening

- `CLUSTER-4011-06-PLACEMENT-SCHEDULER.md`
- `CLUSTER-4011-07-CAPACITY-RESERVATION.md`
- `CLUSTER-4011-24-ROLLING-UPGRADES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.12 normative deepening

- `LIFE-4012-10-DATABASE-SCHEMA-MIGRATIONS.md`
- `LIFE-4012-11-DATA-BACKFILL-MIGRATION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.

## V4.0.14 normative deepening

- `BOOT-4014-27-TRANSFER-CENTER.md`
- `BOOT-4014-28-MIGRATION-COMPATIBILITY.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
