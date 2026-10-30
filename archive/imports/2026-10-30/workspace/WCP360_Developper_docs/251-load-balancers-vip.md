# 251 --- HAProxy / Traefik / VRRP

## Statut et origine

\[SOURCE\] HAProxy/Traefik, VRRP/floating IP.

## Objectif

Gérer LB et IP flottantes sans mélanger les moteurs.

## Pages / sous-vues

Load balancers; frontends/routes; backends; VIPs; health.

## Ressources et données

engine, listener, target pool, VIP owner, health.

## Actions opérateur

Create/edit; drain backend; move VIP only if supported.

## Monitoring, santé et logs

Backend health, connections, errors.

## Sécurité, permissions et audit

High-impact network actions audited.

## Contrat backend attendu

Engine-specific adapters with normalized summaries.

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

Les options spécifiques HAProxy ne sont pas imposées à Traefik.
