# 1020 --- OpenAPI / REST Conventions

## Statut

**CANONICAL --- DEVELOPER BLUEPRINT --- V3.9**

Ce document traduit les contrats `IMPLEMENTATION-READY` V3.8 en
organisation de code et ordre de construction. Il ne modifie pas les
décisions normatives de sécurité `650–669`.

`api/openapi/wcp-v1.yaml` est le contrat public consolidé, découpable
par références de modules.

Base `/api/v1`; JSON UTF-8; RFC3339 UTC; cursor pagination; allowlist
filters/sorts; typed Problem response; `X-Request-ID`/correlation ;
ETag/If-Match sur mutations concurrentes ; `Idempotency-Key` pour
opérations rejouables.

Une action longue retourne HTTP 202 avec `job_id` et état initial.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
