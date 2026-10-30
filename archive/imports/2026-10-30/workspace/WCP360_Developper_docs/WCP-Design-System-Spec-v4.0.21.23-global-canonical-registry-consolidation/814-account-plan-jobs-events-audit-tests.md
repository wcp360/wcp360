# 814 --- Accounts / Plans Jobs, Events, Audit & Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Jobs : account provision/suspend/unsuspend/terminate, plan migration,
usage reconcile. Tests : reseller boundary, plan downgrade conflicts,
hard/soft quota, impersonation attribution, suspended account behavior,
termination confirmation/recovery, cross-tenant denial.

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-27-ACCOUNT-DOMAIN-WEB-PHP-JOBS-EVENTS.md`
- `HOSTING-404-29-HOSTING-CORE-E2E-SCENARIOS.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.
