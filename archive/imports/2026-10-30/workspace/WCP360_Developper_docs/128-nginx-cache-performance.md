# 128 --- Nginx Cache & Performance

## Statut et origine

\[SOURCE\] Cache + HTTP/2. \[V3\] performance view.

## Objectif

Configurer cache et options de performance supportées.

## Pages / sous-vues

Cache zones; policies; compression/protocol capabilities; purge.

## Ressources et données

cache keys/policies as structured settings, sizes, state.

## Actions opérateur

Edit policy; purge scoped cache; apply/reload.

## Monitoring, santé et logs

Hit ratio only if measured; cache size; errors.

## Sécurité, permissions et audit

Cache purge scope explicit; no fake metrics.

## Contrat backend attendu

Capability-driven settings.

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

Les options dépendent de la version/modules détectés.
