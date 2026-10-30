# 891 --- Notification Delivery Contract

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Canaux : in-app, email, webhook et connectors explicitement installés.
Templates versionnés/localisables ; données sensibles minimisées.

Retry borné + backoff ; état delivered/failed ;
déduplication/correlation ; préférence utilisateur respectée sauf
notifications obligatoires de sécurité/administration définies par
policy.

## V4.0.10 normative deepening

- `OPS-4010-19-NOTIFICATION-ROUTING.md`
- `OPS-4010-20-NOTIFICATION-TEMPLATES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
