# 763 --- Mail Deliverability Implementation Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## Findings

SPF, DKIM, DMARC, PTR, TLS, MTA-STS/TLS-RPT lorsque configurés,
queue/reject trends et observations de réputation.

## DKIM rotation

Generate → publish → verify DNS → activate signing → grace → retire old
selector.

## Principe

Les scores/réputations externes sont horodatés et explicables ; WCP ne
garantit jamais la livraison inbox.

## V4.0.6 normative deepening

- `MAIL-406-11-DKIM-KEY-LIFECYCLE.md`
- `MAIL-406-12-SPF-DEEP.md`
- `MAIL-406-13-DMARC-DEEP.md`
- `MAIL-406-20-REPUTATION-BLOCKLISTS.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
