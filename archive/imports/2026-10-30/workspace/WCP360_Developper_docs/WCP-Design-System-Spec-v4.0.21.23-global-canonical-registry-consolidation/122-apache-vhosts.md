# 122 --- Apache Virtual Hosts

## Statut et origine

\[SOURCE\] Web Server Manager vhosts.

## Objectif

Gérer les vhosts avec vue effective et ownership.

## Pages / sous-vues

Vhost list; detail; domains; document root; directives; logs; TLS
linkage.

## Ressources et données

server names, ports, roots, owner, PHP/runtime linkage, status.

## Actions opérateur

Create/edit via supported schema; enable/disable; validate; reload.

## Monitoring, santé et logs

Per-vhost requests/errors if instrumentation exists.

## Sécurité, permissions et audit

Scope/tenant isolation; path validation; no arbitrary host path from
untrusted input.

## Contrat backend attendu

Structured vhost model plus advanced directives only if policy.

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

Le vhost montre la configuration générée/effective lorsque disponible.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-09-WEBSITE-VHOST-CANONICAL-MODEL.md`
- `HOSTING-404-12-APACHE-DEEP-CONTRACT.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
