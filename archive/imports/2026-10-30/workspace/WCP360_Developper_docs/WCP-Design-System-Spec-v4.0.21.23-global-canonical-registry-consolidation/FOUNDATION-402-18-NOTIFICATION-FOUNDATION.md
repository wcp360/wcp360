# V4.0.2 --- Notification Foundation

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Modèle

Event/condition → notification policy → recipient resolution → channel
adapter → delivery attempt → receipt/failure.

## Classes

Information, warning, action-required, security-critical. Les alertes
critiques ne doivent pas dépendre d'un seul canal.

## Anti-spam

Deduplication, grouping, cooldown, escalation et recovery notification.

## Sécurité

Pas de secrets dans email/Slack/Discord/webhook. Les liens sensibles
exigent authentification normale.

## V4.0.10 normative deepening

- `OPS-4010-19-NOTIFICATION-ROUTING.md`
- `OPS-4010-20-NOTIFICATION-TEMPLATES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
