# 844 --- Developer API / RBAC / Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Permissions : `runtime.read/manage`, `git.read/manage`, `deploy.run`,
`dependencies.run`, `env.manage`. Tests : malicious repo URL/options,
webhook replay/signature, env secrecy, runtime mismatch, failed deploy
rollback/recovery, process isolation, dependency timeout, cross-tenant.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
