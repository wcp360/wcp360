# 801 --- Domain & Registrar API / RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

API : `/api/v1/domains`, `/subdomains`, `/domain-aliases`,
`/registrars`, `/registrar-domains`. Permissions admin :
`domains.read/manage`, `registrar.read/manage`, `registrar.transfer`.
User/reseller : uniquement domaines de leur scope et actions autorisées
par plan/policy. Les contacts/identifiants registrar sensibles sont
redacted selon leur nature.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
