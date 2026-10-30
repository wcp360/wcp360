# V4.0.18 — Audit & Event Coverage Audit

## Statut
**NORMATIVE AUDIT — V4.0.18 — NO CODE**

## Base auditée
V4.0.17 et ses 30 contrats `IMPL-4017-*`. Cette passe n'invente pas les mappings absents : `PASS` signifie explicitement supporté par le corpus ; `PARTIAL` signifie que la surface existe mais que la chaîne n'est pas fermée ; `N/A` doit être justifié ; `BLOCKED` empêche le Repository Entry Gate.

## PASS
Structure d'audit, real/effective actor, correlation, outbox, webhook signing/replay et tamper-evidence sont spécifiés.

## PARTIAL
Il manque un catalogue stable `action_id/event_type` pour toutes les mutations CORE, avec safe-diff policy, severity/classification, tenant visibility et retention class.

## Règle
Les noms d'événements ne doivent pas être inventés au fil du code.
