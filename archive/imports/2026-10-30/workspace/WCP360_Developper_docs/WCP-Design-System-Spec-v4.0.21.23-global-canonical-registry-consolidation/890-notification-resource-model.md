# 890 --- Notification Resource Model

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Ressources : `Notification`, `NotificationTemplate`,
`NotificationRoute`, `NotificationPreference`, `DeliveryAttempt`,
`EscalationPolicy`.

Un événement métier produit une notification normalisée ; le canal de
livraison est un adapter. Email/webhook et connectors disponibles ne
deviennent pas de la logique métier.

## V4.0.10 normative deepening

- `OPS-4010-19-NOTIFICATION-ROUTING.md`
- `OPS-4010-20-NOTIFICATION-TEMPLATES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
