# 861 --- Billing Connector Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Actions : create/provision, suspend, unsuspend, terminate, change plan,
credential reset lorsque contractuel, usage export, status sync. Toutes
les requêtes externes utilisent idempotency/correlation quand le
fournisseur le permet. Aucune suppression technique irréversible sur
simple webhook non vérifié.

## V4.0.9 normative deepening

- `API-409-13-BILLING-INTEGRATION-MODEL.md`
- `API-409-14-WHMCS-ADAPTER-CONTRACT.md`
- `API-409-15-BLESTA-FOSSBILLING-HOSTBILL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
