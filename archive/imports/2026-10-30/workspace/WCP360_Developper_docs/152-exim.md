# 152 --- Exim Manager

## Statut et origine

\[SOURCE\] Exim possible MTA.

## Objectif

Administrer Exim si installé.

## Pages / sous-vues

Overview; Queue; Routing; Config; Logs.

## Ressources et données

version, state, queue, routers/transports represented safely.

## Actions opérateur

Reload/restart; supported configuration actions.

## Monitoring, santé et logs

Queue/deferred/errors.

## Sécurité, permissions et audit

Privileged and audited.

## Contrat backend attendu

Exim-specific adapter.

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

Pas de paramètres Postfix présentés dans ce module.
