# 764 --- Mail Jobs, Events & Audit

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

Jobs : mailbox provisioning, DKIM rotate, mail config apply, queue
action autorisée, deliverability recheck.

Events : `mailbox.created`, `mail.quota_changed`, `dkim.rotated`,
`mail.delivery_finding_changed`, `mail.service_degraded`.

Audit : target, actor, domain, quota/policy diff, queue action ; contenu
des emails et secrets exclus par défaut.

## V4.0.6 normative deepening

- `MAIL-406-28-MAIL-JOBS-EVENTS-RECONCILIATION.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
