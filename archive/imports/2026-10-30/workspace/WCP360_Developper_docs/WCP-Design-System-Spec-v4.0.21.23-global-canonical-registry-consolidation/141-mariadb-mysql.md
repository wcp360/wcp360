# 141 --- MariaDB / MySQL Manager

## Statut et origine

\[SOURCE\] MariaDB/MySQL management.

## Objectif

Administration du moteur et ressources compatibles.

## Pages / sous-vues

Overview; Databases; Users; Privileges; Variables; Logs; Replication.

## Ressources et données

version, state, DBs, users, connections, selected safe variables.

## Actions opérateur

Service control; DB/user operations; configuration through approved
schema.

## Monitoring, santé et logs

Connections, slow queries if enabled, buffer metrics if available.

## Sécurité, permissions et audit

Never expose plaintext passwords; least privilege; audit.

## Contrat backend attendu

DB adapter; secrets write-only; typed grants.

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

Création d'utilisateur retourne le secret selon politique sans le
journaliser.
