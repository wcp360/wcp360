# 132 --- PHP-FPM Service

## Statut et origine

\[SOURCE\] PHP-FPM pools.

## Objectif

Gérer le service FPM par version.

## Pages / sous-vues

Service status; versions; workers; logs; config.

## Ressources et données

master state, socket/listen refs, process counts, service version.

## Actions opérateur

Reload/restart; config test; open pools.

## Monitoring, santé et logs

Active/idle workers, queue and memory only if instrumentation supports
them.

## Sécurité, permissions et audit

Control permissions; audit/job.

## Contrat backend attendu

FPM adapter with supported metrics.

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

Les métriques non disponibles affichent Unknown, pas zéro.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-20-PHP-FPM-POOL-DEEP.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
