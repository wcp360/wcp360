# 751 --- DNS / TLS API & RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## API

`/api/v1/dns/zones`, `/records`, `/transfers`, `/dnssec`,
`/certificates`, `/acme/accounts`, `/certificate-orders`.

## Mutations longues

DNSSEC enable/rotation, zone transfer changes, certificate
order/renew/deploy retournent un Job.

## Permissions

Admin : `dns.read/manage`, `dns.transfer.manage`, `dnssec.manage`,
`tls.read/manage`, `acme.manage`. User/reseller : uniquement
zones/certificats appartenant à leurs domaines et selon plan/policy.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
