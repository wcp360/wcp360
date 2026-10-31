# 126 --- Nginx Server Blocks

## Statut et origine

\[SOURCE\] Web server vhosts/proxy.

## Objectif

Configurer les hôtes Nginx par ressources structurées.

## Pages / sous-vues

List; detail; domains; locations; TLS; logs; advanced directives.

## Ressources et données

listen, server names, root/proxy target, locations, owner.

## Actions opérateur

Create/edit/enable/disable; validate; apply.

## Monitoring, santé et logs

Per-site metrics if available.

## Sécurité, permissions et audit

Tenant/path/target validation; advanced directives permission separate.

## Contrat backend attendu

Structured schema and effective config representation.

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

Les conflits de `server_name/listen` sont détectés côté backend.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-09-WEBSITE-VHOST-CANONICAL-MODEL.md`
- `HOSTING-404-11-NGINX-DEEP-CONTRACT.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
