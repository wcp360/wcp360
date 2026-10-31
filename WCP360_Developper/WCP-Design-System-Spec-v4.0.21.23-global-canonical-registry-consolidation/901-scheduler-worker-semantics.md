# 901 --- Scheduler / Worker Semantics

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Semantics V1 : at-least-once delivery + idempotency au niveau action. Le
système ne prétend pas garantir exactly-once.

Leases avec expiration, heartbeat, bounded retries, exponential
backoff/jitter, dead-letter après policy. Une exécution expirée est
réconciliée avant retry d'une action non triviale.
