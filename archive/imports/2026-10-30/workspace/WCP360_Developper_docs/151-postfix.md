# 151 --- Postfix Manager

## Statut et origine

\[SOURCE\] Postfix possible MTA.

## Objectif

Administrer Postfix si installé.

## Pages / sous-vues

Overview; Queues; Routing; Relay; Config; Logs.

## Ressources et données

version, state, queue, relay/routing effective settings.

## Actions opérateur

Reload/restart; config schema actions; queue actions delegated.

## Monitoring, santé et logs

Queue size, deferred, errors.

## Sécurité, permissions et audit

Privileged; secrets redacted; audit.

## Contrat backend attendu

Capability only when Postfix adapter present.

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

Module absent lorsque Postfix n'est pas utilisé.
