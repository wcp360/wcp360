# 639 --- Internal Event Bus & Job Semantics

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Objectif

Distinguer événements de domaine, commandes/jobs et notifications.

## Job

Command intent, actor/scope, target, idempotency key si pertinent,
attempts, timeout, progress evidence, result/error.

## Event

Fait immuable au passé avec event ID, timestamp, resource,
correlation/causation IDs.

## Technologie

Le choix RabbitMQ, Redis streams, database queue ou autre n'est pas fixé
par les sources V3.4 ; le contrat doit rester indépendant.
