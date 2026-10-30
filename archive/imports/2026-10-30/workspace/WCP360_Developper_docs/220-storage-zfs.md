# 220 --- ZFS Storage

## Statut et origine

\[SOURCE\] ZFS listed under Storage & Filesystems.

## Objectif

Gérer datasets/pools/snapshots si ZFS capability existe.

## Pages / sous-vues

Pools; datasets; snapshots; health.

## Ressources et données

pool/dataset, capacity, health, properties approved.

## Actions opérateur

Snapshot; approved dataset operations; scrub if supported.

## Monitoring, santé et logs

Capacity, errors, scrub status.

## Sécurité, permissions et audit

Storage admin only; destructive actions strongly guarded.

## Contrat backend attendu

ZFS adapter.

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

Module absent sans ZFS capability.
