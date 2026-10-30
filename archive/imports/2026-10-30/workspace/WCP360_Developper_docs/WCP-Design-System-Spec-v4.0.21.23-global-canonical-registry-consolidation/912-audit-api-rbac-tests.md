# 912 --- Audit API / RBAC / Tests

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Permissions : `audit.read`, `audit.export`, `audit.retention.manage`.
Tests : mutation sans audit = échec de conformité, redaction, pagination
temporelle, retention/purge authorization, export outage, actor réel
lors impersonation, correlation job→audit.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
