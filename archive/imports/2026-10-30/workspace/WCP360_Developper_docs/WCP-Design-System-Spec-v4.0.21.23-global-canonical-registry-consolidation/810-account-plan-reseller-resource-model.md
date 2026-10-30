# 810 --- Accounts, Plans & Reseller Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Ressources : `HostingAccount`, `ResellerAccount`, `HostingPlan`,
`PlanAssignment`, `Entitlement`, `ResourceLimit`, `UsageCounter`,
`Suspension`, `BrandProfile`.

Hiérarchie : Root/Admin → Reseller → HostingAccount. La hiérarchie ne
remplace jamais RBAC : elle fournit le scope de délégation.

Les quotas/limits sont des données typées, pas un blob arbitraire.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-01-HOSTING-ACCOUNT-DEEP.md`
- `HOSTING-404-02-RESELLER-DELEGATION-DEEP.md`
- `HOSTING-404-03-PLAN-ENTITLEMENT-QUOTA-DEEP.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
