# 270 --- API Tokens & Webhooks

## Statut et origine

\[SOURCE\] API Manager, token generator, webhooks, developer docs.

## Objectif

Gérer accès programmatiques.

## Pages / sous-vues

Tokens; scopes; webhooks; deliveries; API docs link.

## Ressources et données

token metadata/fingerprint, scopes, expiry, webhook endpoint redacted,
delivery state.

## Actions opérateur

Create/revoke/rotate token; create/test webhook.

## Monitoring, santé et logs

Webhook delivery status/rate.

## Sécurité, permissions et audit

Token secret one-time; least privilege; audit.

## Contrat backend attendu

API auth/webhook service.

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

Un token révoqué ne peut être réaffiché ou réactivé silencieusement.
