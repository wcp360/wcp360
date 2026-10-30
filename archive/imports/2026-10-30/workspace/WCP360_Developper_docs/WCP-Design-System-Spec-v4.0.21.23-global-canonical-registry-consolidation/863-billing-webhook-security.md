# 863 --- Billing Webhook Security

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Signature/secret, timestamp/replay protection si protocole le permet, IP
restriction seulement en défense additionnelle, payload schema
validation, event allowlist. Un webhook crée une intention/job après
validation ; il n'exécute pas directement une commande root.

## V4.0.9 normative deepening

- `API-409-09-WEBHOOK-OUTBOUND-MODEL.md`
- `API-409-10-WEBHOOK-SIGNATURE-REPLAY.md`
- `API-409-11-WEBHOOK-DELIVERY-RETRY-DLQ.md`
- `API-409-13-BILLING-INTEGRATION-MODEL.md`
- `API-409-14-WHMCS-ADAPTER-CONTRACT.md`
- `API-409-15-BLESTA-FOSSBILLING-HOSTBILL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
