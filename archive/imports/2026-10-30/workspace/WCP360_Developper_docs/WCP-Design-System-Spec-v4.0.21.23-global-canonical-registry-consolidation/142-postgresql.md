# 142 --- PostgreSQL Manager

## Statut et origine

\[SOURCE\] PostgreSQL management.

## Objectif

Administration PostgreSQL sans calquer artificiellement MySQL.

## Pages / sous-vues

Overview; Databases; Roles; Privileges; Connections; Config; Logs;
Replication.

## Ressources et données

version, clusters/instances if model supports, DBs, roles, connections.

## Actions opérateur

DB/role operations; service/config actions.

## Monitoring, santé et logs

Connections, locks, replication lag if available.

## Sécurité, permissions et audit

Role semantics preserved; no MySQL terminology leakage.

## Contrat backend attendu

PostgreSQL-specific adapter/schema.

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

Les privilèges reflètent le modèle PostgreSQL.
