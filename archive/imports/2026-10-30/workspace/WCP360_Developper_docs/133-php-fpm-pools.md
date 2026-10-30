# 133 --- PHP-FPM Pools

## Statut et origine

\[SOURCE\] PHP-FPM pools + isolation par compte.

## Objectif

Configurer pools par compte/site selon modèle backend.

## Pages / sous-vues

Pool list; detail; process manager; limits; env; domain mappings.

## Ressources et données

owner, PHP version, pm mode, limits, socket/listen, status.

## Actions opérateur

Create/edit/delete; move domain mapping; validate/reload.

## Monitoring, santé et logs

Workers, queue, memory, slow requests if available.

## Sécurité, permissions et audit

Isolation ownership; limit ranges validated backend; audit.

## Contrat backend attendu

Typed pool schema; effective config; conflict detection.

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

Le formulaire distingue valeurs héritées et overrides.
