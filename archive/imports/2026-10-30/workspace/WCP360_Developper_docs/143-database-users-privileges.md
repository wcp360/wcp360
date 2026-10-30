# 143 --- Database Users & Privileges

## Statut et origine

\[SOURCE\] DB users.

## Objectif

Pattern commun d'attribution des droits.

## Pages / sous-vues

Users/roles; grants; resource bindings.

## Ressources et données

principal, engine, scope, grants, host restriction where relevant.

## Actions opérateur

Create; rotate credential; grant/revoke; disable/delete.

## Monitoring, santé et logs

Auth failures if safely available.

## Sécurité, permissions et audit

Secret handling; object authorization; audit.

## Contrat backend attendu

Engine-specific privilege vocabulary normalized only where safe.

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

Aucun mot de passe existant n'est réaffiché.
