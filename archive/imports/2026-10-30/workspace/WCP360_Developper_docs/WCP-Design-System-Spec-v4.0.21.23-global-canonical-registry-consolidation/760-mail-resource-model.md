# 760 --- Mail Resource Model

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Ressources

`MailDomain`, `Mailbox`, `Alias`, `Forwarder`, `Autoresponder`,
`MailQuota`, `SievePolicy`, `DkimKey`, `DmarcPolicy`, `MailQueueItem`,
`MailService`, `DeliverabilityFinding`.

## Baseline V1

Postfix + Dovecot + Rspamd. Exim/SpamAssassin restent adapters
possibles.

## Relations

Domain → MailDomain → mailbox/aliases/policies. Les DNS SPF/DKIM/DMARC
restent des ressources DNS liées, pas des chaînes écrites directement
sans ownership.

## V4.0.6 normative deepening

- `MAIL-406-01-MAIL-RESOURCE-MODEL-DEEP.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
