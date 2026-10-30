# 127 --- Nginx Reverse Proxy & Upstreams

## Statut et origine

\[SOURCE\] Proxy management.

## Objectif

Gérer upstreams et reverse proxy sans éditer aveuglément un fichier
brut.

## Pages / sous-vues

Upstreams; members; proxy routes; health.

## Ressources et données

backend targets, weights, health state if measured, timeout policy.

## Actions opérateur

Create/edit route/upstream; enable/disable member; validate.

## Monitoring, santé et logs

Backend health, errors, latency if available.

## Sécurité, permissions et audit

SSRF-sensitive target validation; permissions; audit.

## Contrat backend attendu

Backend validates allowed target ranges/policies.

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

Une cible interdite est refusée côté serveur, même si UI contournée.
