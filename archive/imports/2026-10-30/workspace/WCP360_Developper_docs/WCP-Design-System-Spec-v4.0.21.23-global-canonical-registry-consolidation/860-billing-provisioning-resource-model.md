# 860 --- Billing / Provisioning Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Ressources : `BillingConnector`, `ExternalCustomerRef`,
`ExternalServiceRef`, `ProvisioningRequest`, `ProvisioningMapping`,
`UsageExport`, `WebhookDelivery`.

WHMCS, Blesta, FossBilling, HostBill sont connectors ; WCP reste source
de vérité de l'état technique, le billing reste source de vérité
commerciale selon mapping.

## V4.0.9 normative deepening

- `API-409-13-BILLING-INTEGRATION-MODEL.md`
- `API-409-14-WHMCS-ADAPTER-CONTRACT.md`
- `API-409-15-BLESTA-FOSSBILLING-HOSTBILL.md`
- `API-409-16-PROVISIONING-REQUEST-MODEL.md`
- `API-409-17-ACCOUNT-PROVISIONING-SAGA.md`
- `API-409-20-EXTERNAL-STATE-RECONCILIATION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
