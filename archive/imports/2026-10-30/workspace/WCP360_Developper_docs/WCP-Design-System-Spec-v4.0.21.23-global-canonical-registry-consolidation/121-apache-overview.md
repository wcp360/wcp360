# 121 --- Apache Manager

## Statut et origine

\[SOURCE\] Apache/Nginx : vhosts, proxy, cache, TLS, HTTP/2.

## Objectif

Administrer Apache comme module autonome.

## Pages / sous-vues

Overview; Virtual Hosts; Modules; Configuration; TLS; Performance; Logs.

## Ressources et données

version, service state, MPM if detected, vhosts, modules, config paths
logical refs.

## Actions opérateur

Config test; reload; restart; module operations if supported.

## Monitoring, santé et logs

Requests, workers, errors, CPU/RAM if available.

## Sécurité, permissions et audit

`apache.read/config/control`; config diff; rollback if supported.

## Contrat backend attendu

Apache adapter; validate config endpoint; mutations as jobs.

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

Un reload n'est proposé qu'après validation réussie ou override
explicitement autorisé.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-12-APACHE-DEEP-CONTRACT.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
