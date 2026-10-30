# 221 --- Ceph / Distributed Storage

## Statut et origine

\[SOURCE\] Ceph/GlusterFS/NFS/S3 and distributed storage.

## Objectif

Vue stockage distribué sans réduire les backends à un modèle faux.

## Pages / sous-vues

Clusters/backends; health; pools/volumes according to adapter;
replication.

## Ressources et données

backend-specific resources + normalized health/capacity.

## Actions opérateur

Backend-specific safe operations only.

## Monitoring, santé et logs

Health, capacity, replication/recovery state.

## Sécurité, permissions et audit

Storage admin; high-risk operations.

## Contrat backend attendu

Adapter-specific schemas with normalized summary.

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

Les détails Ceph ne sont pas affichés pour Gluster/NFS/S3.
