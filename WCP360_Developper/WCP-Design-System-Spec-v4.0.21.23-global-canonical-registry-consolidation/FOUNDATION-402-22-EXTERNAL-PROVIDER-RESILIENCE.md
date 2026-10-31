# V4.0.2 --- External Provider Resilience

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Providers

DNS APIs, registrar, ACME, S3/object storage, billing, notifications,
vulnerability feeds et autres SaaS.

## Contrat

Timeout borné, retry policy, idempotency, rate limit,
circuit/degradation policy, credential scope, request correlation et
normalized errors.

## Indisponibilité

Une panne provider ne doit pas bloquer les fonctions WCP sans dépendance
avec lui. Les Jobs peuvent passer `waiting_external` ou
`retry_scheduled`.

## Reconciliation

Après réponse ambiguë, relire l'état distant avant retry lorsque l'API
provider le permet.
