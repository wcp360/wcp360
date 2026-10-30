# ADR-004 --- Job & Event Backend

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

V1 utilise une **durable database queue PostgreSQL** pour jobs/commands
et une table/outbox transactionnelle pour événements. Aucun
RabbitMQ/Redis n'est requis pour faire fonctionner le control plane
initial.

## Évolution

Un `JobTransport`/`EventTransport` permet un backend spécialisé
ultérieur si les métriques démontrent le besoin.

## Garanties

At-least-once pour exécution de jobs ; handlers idempotents lorsque
rejouables ; lease/heartbeat ; retry borné ; dead-letter state ;
correlation/causation IDs.
