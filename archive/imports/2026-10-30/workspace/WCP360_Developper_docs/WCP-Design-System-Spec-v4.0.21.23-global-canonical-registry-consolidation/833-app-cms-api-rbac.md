# 833 --- App/CMS API & RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

API : `/applications`, `/application-templates`, `/wordpress`,
`/staging`, `/vulnerability-findings`. Permissions scoped
Website/Account : `apps.read/manage`, `wordpress.manage`,
`staging.manage`, `app_security.manage`. Install/update/clone/staging
sont Jobs.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
