# 702 --- Control Plane REST API

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Endpoints V1

`GET /api/v1/nodes` `GET /api/v1/nodes/{id}`
`GET /api/v1/nodes/{id}/capabilities` `GET /api/v1/jobs`
`GET /api/v1/jobs/{id}` `POST /api/v1/jobs/{id}/cancel`
`GET /api/v1/audit-events` `GET/POST /api/v1/service-accounts`
`POST /api/v1/service-accounts/{id}/credentials`
`DELETE /api/v1/service-accounts/{id}/credentials/{credentialId}`

## Contrat

Pagination cursor, filtering, request/correlation ID, typed problem
responses, ETag sur ressources versionnées. Les actions longues
retournent `202 Accepted` + représentation/référence de Job.

## Idempotence

Les créations/actions rejouables acceptent `Idempotency-Key`; le scope
inclut acteur/tenant/opération.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
