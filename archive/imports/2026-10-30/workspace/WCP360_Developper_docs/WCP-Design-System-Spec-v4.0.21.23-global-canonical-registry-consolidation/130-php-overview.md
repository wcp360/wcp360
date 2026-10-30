# 130 --- PHP Runtime Manager

## Statut et origine

\[SOURCE\] PHP & Runtime Manager : multi-version, FPM pools, extensions,
INI, isolation.

## Objectif

Point d'entrée PHP global WCP-ADMIN.

## Pages / sous-vues

Versions; FPM; Pools; Extensions; INI; Domain mappings; Health.

## Ressources et données

installed versions, default, support status if source available,
SAPI/FPM, site mappings.

## Actions opérateur

Install/remove via package layer; set defaults; open
pools/extensions/INI.

## Monitoring, santé et logs

FPM health, workers, memory, errors if available.

## Sécurité, permissions et audit

`php.read/manage`; removal preflight checks dependent sites.

## Contrat backend attendu

Runtime adapter per installed PHP version.

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

Impossible de retirer une version encore requise sans workflow d'impact.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-19-PHP-RUNTIME-VERSION-LIFECYCLE.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
