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
