# MASTER API IMPLEMENTATION CONTRACT

## Parité

Toute action métier disponible dans WCP-USER/WCP-ADMIN passe par les
mêmes services de domaine que l'API publique/administrative appropriée.
Une primitive root interne n'est pas automatiquement une API publique.

## Minimum à figer

Versioning, pagination, filtering/sorting, field errors, problem/error
schema, request/correlation ID, idempotency pour créations/actions
pertinentes, optimistic concurrency, async job representation, rate
limiting, deprecation.

## Auth

User sessions/tokens et Service Accounts restent des identités
distinctes mais traversent le même authorization engine.

## Audit

Une mutation API et la mutation UI équivalente produisent la même classe
d'événement d'audit.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
