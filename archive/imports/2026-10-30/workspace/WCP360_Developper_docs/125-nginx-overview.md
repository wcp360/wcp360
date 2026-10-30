# 125 --- Nginx Manager

## Statut et origine

\[SOURCE\] Apache/Nginx : vhosts/server blocks, proxy, cache, TLS,
HTTP/2.

## Objectif

Administrer Nginx comme reverse proxy ou serveur web.

## Pages / sous-vues

Overview; Server Blocks; Upstreams; Reverse Proxy; Cache; TLS; Config;
Logs.

## Ressources et données

version, state, connections, server blocks, upstreams, topology.

## Actions opérateur

Config test; reload; restart; open submodules.

## Monitoring, santé et logs

Connections, requests, errors, latency if available.

## Sécurité, permissions et audit

`nginx.read/config/control`; diff and audit.

## Contrat backend attendu

Nginx adapter with `nginx -t` equivalent performed backend-side.

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

Jamais de reload marqué réussi si validation/apply échoue.
