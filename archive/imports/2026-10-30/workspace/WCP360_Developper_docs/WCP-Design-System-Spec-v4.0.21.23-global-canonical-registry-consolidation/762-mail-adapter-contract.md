# 762 --- Mail Stack Adapter Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Postfix

Domains/maps, routing, queue observation/actions autorisées,
reload/health.

## Dovecot

Mailbox/auth/quota/Sieve capability, config validation, reload/health.

## Rspamd

Policy/health/statistics interfaces supportées ; aucune dépendance UI à
une structure interne non contractuelle.

## Config

Fragments WCP gérés, génération atomique, validation avant reload.

## V4.0.6 normative deepening

- `MAIL-406-03-POSTFIX-MTA-DEEP.md`
- `MAIL-406-05-DOVECOT-IMAP-POP3-DEEP.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
