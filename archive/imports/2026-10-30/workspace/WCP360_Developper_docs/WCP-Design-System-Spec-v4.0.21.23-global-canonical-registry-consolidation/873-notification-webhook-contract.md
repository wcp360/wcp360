# 873 --- Notification & Automation Webhook Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 3**

Ce document complète les contrats existants. Les règles de sécurité
`650–669` et les contrats systèmes `700–795` restent normatifs.

Routes email/webhook/Slack/Discord/autres connectors selon
disponibilité. Secrets URL/token via SecretStore ; retries bornés avec
backoff ; dead-letter/failed state observable. Payload sortant minimal,
signé lorsque le receiver contract le permet.

## V4.0.9 normative deepening

- `API-409-09-WEBHOOK-OUTBOUND-MODEL.md`
- `API-409-10-WEBHOOK-SIGNATURE-REPLAY.md`
- `API-409-11-WEBHOOK-DELIVERY-RETRY-DLQ.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.10 normative deepening

- `OPS-4010-19-NOTIFICATION-ROUTING.md`
- `OPS-4010-20-NOTIFICATION-TEMPLATES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
