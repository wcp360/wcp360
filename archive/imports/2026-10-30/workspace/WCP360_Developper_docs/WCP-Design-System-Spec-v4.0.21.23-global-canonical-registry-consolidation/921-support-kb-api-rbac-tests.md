# 921 --- Support / KB API / RBAC / Tests

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Permissions : `support.ticket.read/manage`, `kb.read/manage`,
`announcement.manage`. Pièces jointes passent par les mêmes règles
upload/malware/path que File Manager. Tests : ticket tenant isolation,
malicious attachment, staff permission, impersonation linkage, KB
draft/publish, announcement scope.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
