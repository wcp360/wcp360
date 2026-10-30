# ADR-004 --- Job Queue, Outbox & Event Persistence

## Statut

**ACCEPTED FOR V1 --- V4.0.16 --- NO CODE**

## Autorité

Cet ADR ferme un choix P0 identifié par `ADR-V4015-07-REGISTER.md`. Il
concrétise la baseline V4.0.15 sans élargir le scope fonctionnel.

## Règles

-   Toute implémentation incompatible exige un ADR de supersession.
-   Les secrets ne sont jamais placés en clair dans les arguments de
    processus, logs ou artefacts exportés.
-   Toute mutation privilégiée reste typée, auditable,
    idempotente/reconciliable et vérifiée par état observé.
-   Le V1 reste single-node-first tout en conservant des interfaces qui
    n'interdisent pas le cluster futur.

## Décision

**PostgreSQL est la source durable V1 pour Jobs, idempotency records,
transactional outbox et event metadata.** Pas de RabbitMQ/Kafka
obligatoire en V1.

## Modèle

API transaction → desired state + Job + outbox dans transaction
cohérente → worker lease/claim → privileged/system adapter → observed
verification → completion/event/audit.

## Concurrence

Leases avec expiration, `attempt`, backoff borné, dead-letter state
logique, heartbeat pour tâches longues et ownership explicite.

## Exactly-once

WCP ne promet pas exactly-once externe. Il fournit at-least-once +
idempotency + reconciliation.

## Evolution

Un broker externe peut être ajouté derrière l'interface event/queue sans
changer les contrats API.
