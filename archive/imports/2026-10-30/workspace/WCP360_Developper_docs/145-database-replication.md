# 145 --- Database Replication

## Statut et origine

\[SOURCE\] Replication.

## Objectif

Observer/configurer réplication selon moteur et capacités.

## Pages / sous-vues

Topology; replicas; status; lag; jobs.

## Ressources et données

primary/replica refs, state, lag, last error.

## Actions opérateur

Setup/failover only if separately supported; otherwise read-only
diagnostics.

## Monitoring, santé et logs

Lag, errors, link health.

## Sécurité, permissions et audit

High-risk actions require dedicated capability/approval.

## Contrat backend attendu

Engine-specific replication adapter.

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

L'UI distingue clairement monitoring et orchestration.
