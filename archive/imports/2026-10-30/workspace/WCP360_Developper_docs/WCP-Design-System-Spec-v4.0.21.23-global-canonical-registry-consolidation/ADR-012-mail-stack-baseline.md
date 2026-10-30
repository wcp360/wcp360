# ADR-012 --- Mail Stack Baseline

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

Stack mail de référence V1 : **Postfix + Dovecot + Rspamd**. Exim et
SpamAssassin restent adapters compatibles mais non baseline de
certification initiale.

Webmail : Roundcube baseline ; SnappyMail adapter optionnel.
DKIM/SPF/DMARC, Sieve, Autoconfig/Autodiscover et deliverability center
restent dans le contrat produit.

## Motivation

Réduire la matrice de tests et éviter deux stacks MTA équivalentes comme
dépendances obligatoires.

## V4.0.6 normative deepening

- `MAIL-406-03-POSTFIX-MTA-DEEP.md`
- `MAIL-406-05-DOVECOT-IMAP-POP3-DEEP.md`
- `MAIL-406-15-RSPAMD-ANTISPAM-DEEP.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
