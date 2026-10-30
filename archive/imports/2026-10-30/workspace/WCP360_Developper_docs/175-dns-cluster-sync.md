# 175 --- DNS Cluster & Synchronization

## Statut et origine

\[SOURCE\] DNS cluster & sync.

## Objectif

Observer réplication et résoudre désynchronisations.

## Pages / sous-vues

Nodes; zones sync; conflicts; queue.

## Ressources et données

node, zone version/serial, sync state, error.

## Actions opérateur

Resync; reconcile through explicit workflow.

## Monitoring, santé et logs

Lag/errors.

## Sécurité, permissions et audit

Cluster permissions; audit.

## Contrat backend attendu

Sync jobs with per-zone results.

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

Une resynchronisation massive devient un job traçable.
