# 280 --- Plugin & Extension Manager

## Statut et origine

\[SOURCE\] Marketplace, Plugin/Extension Manager, SDK, UI Builder.

## Objectif

Gérer extensions compatibles WCP.

## Pages / sous-vues

Installed; available; permissions; updates; failures.

## Ressources et données

id, version, vendor/source, compatibility, permissions, state.

## Actions opérateur

Install/update/disable/remove under policy.

## Monitoring, santé et logs

Extension health/errors.

## Sécurité, permissions et audit

Declared permissions; signature/trust policy to be decided by
architecture.

## Contrat backend attendu

Extension contract from 82.

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

Une extension défaillante n'empêche pas le shell WCP principal.
