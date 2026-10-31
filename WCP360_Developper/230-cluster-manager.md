# 230 --- Cluster Manager

## Statut et origine

\[SOURCE\] Cluster Manager, Node Manager, heartbeat/fencing, placement,
live migration, rolling updates.

## Objectif

Vue et orchestration cluster.

## Pages / sous-vues

Overview; Nodes; Health; Placement; HA; Operations; Events.

## Ressources et données

cluster, nodes, quorum/health if backend model provides, workloads,
versions.

## Actions opérateur

Add/remove node via workflow; maintenance; rolling operation; open
migrations.

## Monitoring, santé et logs

Node health, capacity, heartbeat status.

## Sécurité, permissions et audit

Cluster admin; destructive/quorum-impact actions guarded.

## Contrat backend attendu

Cluster orchestrator API; jobs and preflight.

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

Le cluster n'est jamais résumé Healthy si un signal critique est
Unknown.
