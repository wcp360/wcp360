# V4.0.5 --- Database/Cache Jobs, Events & Audit

## Statut

**NORMATIVE --- V4.0.5 HOSTING CORE DEEP SPECIFICATION --- TRANCHE 2 ---
NO CODE**

Portée : DNS authoritative/providers/DNSSEC → TLS/ACME/certificats →
MySQL/MariaDB → PostgreSQL → Redis/Memcached → outils DB/SSO.

## Invariants

-   `Domain`, `DNSZone`, `RegistrarDomain`, `Certificate`, `Database`,
    `DatabaseUser`, `CacheInstance` restent des ressources distinctes.
-   WCP distingue toujours control-plane desired state, état réellement
    observé et état externe.
-   Les credentials ne sont ni des identifiants de ressource ni des
    données de log/audit.
-   Les adapters externes et moteurs locaux n'acceptent que des
    opérations typées et bornées.
-   Toute opération destructive, rotation de secret ou mutation
    DNS/TLS/DB sensible possède verify/reconcile.
-   WCP-USER n'obtient jamais de primitives root, superuser DB, accès
    inter-tenant ou configuration fournisseur arbitraire.

## Jobs

db.create/drop, dbuser.create/rotate/drop, grant.apply,
db.backup/restore, db.migrate, redis.provision/resize/delete,
memcached.provision/resize/delete.

## Events

Facts vérifiés sans credentials : database.created, credential.rotated,
grant.changed, restore.completed, cache.resized, etc.

## Audit

Actor, account, engine, target IDs, privilege diff, destructive
confirmation, backup reference, verification. Jamais password/dump
contents.

## Retry

DDL ambigu après timeout nécessite catalog readback avant retry.
