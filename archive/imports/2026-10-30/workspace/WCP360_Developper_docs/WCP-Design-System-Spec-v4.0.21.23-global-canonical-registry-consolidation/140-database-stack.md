# 140 --- Database Stack Overview

## Statut et origine

\[SOURCE\] MariaDB/MySQL/PostgreSQL + users + replication.

## Objectif

Vue des moteurs DB et santé.

## Pages / sous-vues

Engines; databases; users; replication; performance; backups.

## Ressources et données

engine/version/state, database count, connections, storage.

## Actions opérateur

Open engine; service control; create resources by scope.

## Monitoring, santé et logs

Connections, queries/errors/replication lag if available.

## Sécurité, permissions et audit

DB admin permissions separated from user DB permissions.

## Contrat backend attendu

Adapters per engine.

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

Les moteurs absents ne sont pas présentés comme installés.
