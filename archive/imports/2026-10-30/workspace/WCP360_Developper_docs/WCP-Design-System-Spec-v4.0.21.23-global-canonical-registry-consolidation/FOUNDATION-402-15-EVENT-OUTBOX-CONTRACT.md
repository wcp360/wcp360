# V4.0.2 --- Events, Outbox & Causation Contract

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Événement

Fait passé immuable : type versionné, event_id, occurred_at,
aggregate/resource, tenant, correlation, causation et payload minimal.

## Outbox

Une mutation de state et l'intention de publier son événement doivent
être atomiquement liées au niveau control-plane. Publication retryable
et consommateurs idempotents.

## Versioning

Ne pas modifier rétroactivement la signification d'un event type.
Ajouter version/champs compatibles ou nouveau type.

## Données sensibles

Les events ne sont pas un canal de secrets.
