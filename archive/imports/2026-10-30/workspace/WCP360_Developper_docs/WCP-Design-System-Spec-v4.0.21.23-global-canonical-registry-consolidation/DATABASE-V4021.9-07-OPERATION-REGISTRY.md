# V4.0.21.9 --- Database / Cache Exact Operation Registry

## Database

`DB-INSTANCE-LIST`, `DB-LIST`, `DB-READ`, `DB-CREATE`,
`DB-DELETE-REQUEST`, `DB-USER-LIST`, `DB-USER-CREATE`, `DB-USER-ROTATE`,
`DB-USER-DISABLE`, `DB-USER-DELETE`, `DB-GRANT-READ`, `DB-GRANT-SET`,
`DB-BACKUP`, `DB-RESTORE`, `DB-RECONCILE`, `DB-USAGE-READ`.

## Cache

`CACHE-LIST`, `CACHE-READ`, `CACHE-CREATE`, `CACHE-UPDATE`,
`CACHE-CREDENTIAL-ROTATE`, `CACHE-RESTART`, `CACHE-DELETE-REQUEST`,
`CACHE-BIND`, `CACHE-UNBIND`, `CACHE-RECONCILE`, `CACHE-USAGE-READ`.

Database DDL/provisioning and cache service mutations are Job-backed
where system/service state changes. Grant changes are typed and audited.
No generic SQL or Redis command executor is part of the public WCP API.
