# 911 --- Audit Integrity / Retention / Export

## Statut

**CANONICAL --- V3.8 CLOSURE / IMPLEMENTATION CONTRACT**

Ce document complète le corpus existant. Les règles de sécurité
`650–669` et les contrats `700–874` restent normatifs.

Baseline V3.6 : rétention audit 365 jours par défaut, configurable selon
édition/policy. Toute purge est privilégiée et auditée.

Les événements possèdent timestamp, actor, target, action, result,
request/correlation IDs. Secrets redacted.

Export SIEM/syslog/connector peut être asynchrone. Une panne d'export ne
supprime pas l'événement local canonique.
