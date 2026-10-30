# 761 --- Mail API & RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## API

`/api/v1/mail/domains`, `/mailboxes`, `/aliases`, `/autoresponders`,
`/sieve`, `/dkim`, `/queue`, `/deliverability`.

## Permissions

Admin : `mail.read/manage`, `mail.service.control`, `mail.queue.manage`,
`mail.deliverability.manage`. User/reseller : ressources de leurs
domaines, quotas et capacités du plan.

## Secrets

Mot de passe mailbox write-only ; reset plutôt que récupération en
clair.

## V4.0.6 normative deepening

- `MAIL-406-27-MAIL-RBAC-IMPERSONATION-AUDIT.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
