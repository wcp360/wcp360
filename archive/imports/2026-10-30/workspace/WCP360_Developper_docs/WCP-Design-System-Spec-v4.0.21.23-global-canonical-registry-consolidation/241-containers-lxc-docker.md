# 241 --- LXC / Docker Containers

## Statut et origine

\[SOURCE\] LXC/Docker + registry + lifecycle.

## Objectif

Gérer conteneurs selon runtime détecté.

## Pages / sous-vues

Containers; Images; Registries; Networks/volumes links; logs.

## Ressources et données

runtime, image, state, resources, node.

## Actions opérateur

Create/start/stop/restart/delete; image operations per policy.

## Monitoring, santé et logs

CPU/RAM/network/logs if available.

## Sécurité, permissions et audit

Runtime isolation; registry credentials redacted.

## Contrat backend attendu

Runtime-specific adapter.

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

LXC et Docker conservent leurs capacités propres.
