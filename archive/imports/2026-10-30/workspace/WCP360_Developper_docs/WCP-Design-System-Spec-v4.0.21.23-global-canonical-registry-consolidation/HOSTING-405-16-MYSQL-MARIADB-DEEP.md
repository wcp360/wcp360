# V4.0.5 --- MySQL / MariaDB Deep Contract

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

## Engines

MySQL et MariaDB ont matrices/version lifecycles distincts malgré
compatibilité SQL partielle.

## Provision

Nom physique sûr → CREATE via adapter typé → charset/collation policy →
owner/grants → verify catalog.

## Root/admin

Credentials superuser ne sont jamais exposés tenant. Adapter utilise
service identity/credential minimal compatible avec tâches
d'administration.

## Charset

utf8mb4 baseline lorsque version/profile le permet ; collations exposées
depuis registry compatible, pas chaîne arbitraire.

## Operations

create/drop DB, create/rotate/drop user, grant/revoke approved
privileges, observe size/connections/status, backup/restore hooks.

## Dangerous

FILE, SUPER, SYSTEM_USER, PROCESS global, replication/admin privileges
et équivalents ne sont pas délégables par défaut.
