# 864 --- Billing / Provisioning API, RBAC & Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Permissions `billing_connectors.manage`, `provisioning.read/manage`,
`usage.export`. Tests : duplicate create, replay webhook, forged
webhook, failed plan change, suspend/unsuspend, termination confirmation
policy, reconciliation drift, connector timeout.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`
- `API-409-13-BILLING-INTEGRATION-MODEL.md`
- `API-409-14-WHMCS-ADAPTER-CONTRACT.md`
- `API-409-15-BLESTA-FOSSBILLING-HOSTBILL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
