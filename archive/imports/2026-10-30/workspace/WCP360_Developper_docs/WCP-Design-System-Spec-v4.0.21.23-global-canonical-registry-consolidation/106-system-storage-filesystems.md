# 106 --- Storage & Filesystems

## Statut et origine

\[SOURCE\] Storage & Mount Manager.

## Objectif

Vue stockage système distincte du File Manager utilisateur.

## Pages / sous-vues

Devices; Filesystems; Mounts; Usage; SMART/health if backend supports
it.

## Ressources et données

device, filesystem, mount, capacity, used, reserved, inode usage,
health.

## Actions opérateur

Mount/unmount only under policy; inspect; navigate to advanced storage.

## Monitoring, santé et logs

Capacity, inode, I/O, latency if available.

## Sécurité, permissions et audit

`storage.read/control`; opérations destructives hors scope simple;
confirmation renforcée.

## Contrat backend attendu

Storage adapter; freshness; mount jobs; no raw device mutation without
dedicated workflow.

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

Capacité et consommation utilisent unités cohérentes et timestamp.

## V4.0.3 — Deep Specification normative

Ce document de surface doit être interprété avec les contrats approfondis suivants :

- `SYSTEM-403-11-FILESYSTEM-MOUNTS-INODES.md`
- `SYSTEM-403-12-DISK-LVM-RAID-SMART.md`
- `SYSTEM-403-25-SYSTEM-BACKUP-RECOVERY.md`

Les règles V4.0.3 prévalent lorsqu'une ancienne formulation est moins précise. Le statut de cette surface reste **à valider contre le READY Gate System/OS**.
