# V4.0.5 --- Database & Cache Failure / Edge-case Matrix

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

## Cas

DB name collision, timeout après CREATE, credential rotation partielle,
grant cross-tenant, disk full during dump, restore version mismatch,
active sessions on drop, max connections reached, engine service down,
TLS mismatch, public bind accidentally requested, Redis OOM/eviction,
Redis persistence failure, Memcached public exposure, cache memory
overcommit, tool SSO token replay.

## Traitement

Evidence → resource state → Job state → retryability → verify →
reconcile/rollback → notification → audit → operator action → acceptance
test.
