# V4.0.5 --- Hosting Core Tranche 2 READY Gate

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

## Couverture

DNS authoritative/providers/delegation/DNSSEC/PTR,
TLS/ACME/certificates/renewal, MySQL/MariaDB, PostgreSQL, DB
credentials/grants/network/TLS/backup/migration/health, Redis, Memcached
et DB web tools/SSO.

## Critères

Provider timeout reconciliation, DNSSEC chain safety, ACME rate-limit
handling, live certificate verification, DB cross-tenant negatives, DDL
timeout readback, backup/restore verification, cache isolation et SSO
secret safety.

## Statut

**TRANCHE 2 = SPECIFIED-DEEP. Pas GLOBAL READY.**

Mail/deliverability puis Files/Backup/Security restent à approfondir.
