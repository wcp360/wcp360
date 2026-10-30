# 771 --- Database / Cache API & RBAC

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 2**

Ce document dérive des décisions et contrats déjà présents dans la
V3.5/V3.6/V3.7. Les règles `650–669` restent normatives et prioritaires.

## API

`/api/v1/databases`, `/database-users`, `/database-grants`,
`/database-servers`, `/redis`, `/memcached`.

## Permissions

Admin : `database.read/manage`, `database.server.manage`,
`cache.read/manage`. User/reseller : DB/users/grants/instances attribués
dans quotas du plan.

## Jobs

Create/drop/import/export/restore et opérations serveur longues
utilisent Jobs.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
