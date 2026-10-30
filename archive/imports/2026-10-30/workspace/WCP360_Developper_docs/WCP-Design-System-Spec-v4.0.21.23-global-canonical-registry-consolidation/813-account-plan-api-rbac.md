# 813 --- Accounts / Plans / Reseller API & RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

API : `/accounts`, `/resellers`, `/plans`, `/entitlements`, `/usage`,
`/suspensions`, `/brand-profiles`. Permissions : `accounts.*`,
`plans.*`, `resellers.*`, `impersonation.start`, `branding.manage`.
WCP-USER ne peut jamais augmenter son propre entitlement.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-28-HOSTING-CORE-RBAC-AUDIT.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
