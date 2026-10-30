# 261 --- Notification Integrations

## Statut et origine

\[SOURCE\] Slack/Email/Webhook integrations.

## Objectif

Gérer canaux d'alerte et tests.

## Pages / sous-vues

Destinations; routing; test history.

## Ressources et données

type, endpoint redacted, event filters, state.

## Actions opérateur

Create/edit/test/disable/delete.

## Monitoring, santé et logs

Delivery success/failure.

## Sécurité, permissions et audit

Secrets write-only; webhook SSRF validation.

## Contrat backend attendu

Integration adapters.

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

Un test réussi ne signifie pas que toutes les alertes sont correctement
routées.

## V4.0.10 normative deepening

- `OPS-4010-19-NOTIFICATION-ROUTING.md`
- `OPS-4010-20-NOTIFICATION-TEMPLATES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
