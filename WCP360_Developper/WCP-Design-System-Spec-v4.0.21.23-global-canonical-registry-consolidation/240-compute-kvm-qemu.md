# 240 --- KVM / QEMU Compute

## Statut et origine

\[SOURCE\] KVM/QEMU, VM lifecycle, snapshots, templates, live migration,
GPU/SR-IOV.

## Objectif

Administration VM lorsque Compute capability existe.

## Pages / sous-vues

VMs; Templates; Snapshots; Devices; Migration.

## Ressources et données

VM state, vCPU, RAM, disks, networks, host, capabilities.

## Actions opérateur

Create/start/stop/reboot/migrate/snapshot; force actions separated.

## Monitoring, santé et logs

CPU/RAM/I/O if available.

## Sécurité, permissions et audit

Compute permissions; console access audited; destructive actions
guarded.

## Contrat backend attendu

Compute adapter.

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

Stop gracieux et force-off sont deux actions différentes.
