# 210 --- Backup & Disaster Recovery Overview

## Statut et origine

\[SOURCE\] Scheduler/profiles, granular restore,
incremental/differential, remote storage, snapshots, DR, encryption.

## Objectif

Point d'entrée sauvegarde WCP-ADMIN.

## Pages / sous-vues

Policies; Jobs; Destinations; Restore; Snapshots; DR; Health.

## Ressources et données

policy, destination, last/next run, coverage, retention, encryption
state.

## Actions opérateur

Run backup; create policy; restore workflow; test destination.

## Monitoring, santé et logs

Success/failure, duration, size, age of last good backup.

## Sécurité, permissions et audit

Backup/restore permissions separate; secrets redacted; restore
high-risk.

## Contrat backend attendu

Backup adapter; jobs; immutable history.

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

Dashboard distingue dernière exécution et dernière sauvegarde valide.

## V4.0.7 normative deepening

- `SECOPS-407-12-BACKUP-RESOURCE-MODEL-DEEP.md`
- `SECOPS-407-19-DISASTER-RECOVERY.md`

Ces contrats approfondissent cette surface et conservent les exigences antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
