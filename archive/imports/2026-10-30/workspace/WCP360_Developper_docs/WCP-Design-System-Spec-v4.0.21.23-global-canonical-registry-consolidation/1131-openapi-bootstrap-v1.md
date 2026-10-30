# 1131 --- OpenAPI Bootstrap V1

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

## Endpoints Sprint 0

`GET /api/v1/health` `GET /api/v1/me` `GET /api/v1/nodes`
`GET /api/v1/nodes/{nodeId}` `GET /api/v1/nodes/{nodeId}/capabilities`
`GET /api/v1/jobs` `GET /api/v1/jobs/{jobId}`
`POST /api/v1/jobs/{jobId}/cancel` `GET /api/v1/audit-events`
`GET /api/v1/service-accounts` `POST /api/v1/service-accounts`
`POST /api/v1/service-accounts/{id}/credentials`
`DELETE /api/v1/service-accounts/{id}/credentials/{credentialId}`

Le bootstrap OpenAPI définit security schemes, Problem schema,
pagination cursor, Job, Node, Capability, AuditEvent, ServiceAccount et
headers request/correlation/idempotency.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
