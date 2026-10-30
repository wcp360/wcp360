# 1130 --- API Error Contract V1

## Statut

**CANONICAL --- V4.0 IMPLEMENTATION FOUNDATION --- SPRINT 0**

Ce document matérialise le Sprint 0 défini par `1090-sprint0-plan.md`.
Les normes `650–669` et contrats `700–1093` restent prioritaires en cas
de conflit.

## Format

``` json
{
  "type": "urn:wcp:error:authorization-denied",
  "title": "Authorization denied",
  "status": 403,
  "code": "AUTHORIZATION_DENIED",
  "detail": "The requested action is not permitted.",
  "request_id": "req_...",
  "correlation_id": "cor_...",
  "errors": []
}
```

## Codes Sprint 0

`VALIDATION_FAILED`, `AUTHENTICATION_REQUIRED`, `AUTHORIZATION_DENIED`,
`RESOURCE_NOT_FOUND`, `RESOURCE_CONFLICT`, `PRECONDITION_FAILED`,
`RATE_LIMITED`, `IDEMPOTENCY_CONFLICT`, `JOB_NOT_CANCELLABLE`,
`CAPABILITY_UNAVAILABLE`, `IPC_UNAVAILABLE`, `ACTION_REJECTED`,
`INTERNAL_ERROR`.

Aucun stack trace, SQL, path secret, command line, credential ou contenu
IPC sensible dans `detail`.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
