# 100 --- WCP-ADMIN / System Overview

## Statut et origine

\[SOURCE\] Core System Manager : System Overview, OS & Kernel,
hostname/timezone, utilisateurs/groupes, processus, packages, cron,
services, storage, terminal, logs, reboot/shutdown.

## Objectif

Vue centrale de l'état de l'hôte et point d'entrée vers les modules
système.

## Pages / sous-vues

Overview; Identity; OS/Kernel; Resources; Services; Storage; Updates;
Events.

## Ressources et données

Host identity, OS/kernel, uptime, CPU, RAM, swap, disks, load, services
critiques, updates.

## Actions opérateur

Refresh; ouvrir module; maintenance; reboot/shutdown via confirmation
renforcée.

## Monitoring, santé et logs

CPU/RAM/load/disk/I/O/network, uptime, health services, événements
récents.

## Sécurité, permissions et audit

`system.read`; actions séparées pour maintenance/reboot/shutdown.
Step-up recommandé pour arrêt/reboot. Audit obligatoire.

## Contrat backend attendu

`GET system/overview`; capabilities; telemetry timestamps; mutations as
jobs.

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

Le dashboard n'invente aucun état Healthy lorsque la télémétrie manque.
