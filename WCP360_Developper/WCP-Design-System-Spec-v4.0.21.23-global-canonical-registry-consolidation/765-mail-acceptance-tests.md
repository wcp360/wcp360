# 765 --- Mail Acceptance Tests

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

-   mailbox/alias/quota scoped ;
-   Postfix/Dovecot/Rspamd health ;
-   config invalid rejected ;
-   password never readable ;
-   Sieve scoped ;
-   DKIM rotation safe ;
-   SPF/DKIM/DMARC diagnostics ;
-   queue permission separation ;
-   user cannot control global MTA ;
-   cross-tenant denial and audit coverage.

## V4.0.6 normative deepening

- `MAIL-406-29-MAIL-FAILURE-ABUSE-MATRIX.md`
- `MAIL-406-30-MAIL-E2E-ACCEPTANCE-READY-GATE.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
