# V4.0.5 --- Database Canonical Resource Model

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

## Séparation

`DatabaseEngine`, `DatabaseInstance/Service`, `Database`,
`DatabaseUser`, `DatabaseGrant`, `DatabaseEndpoint`, `DatabaseBackup`.

## Tenant

Database et users appartiennent à HostingAccount. Les noms physiques
peuvent être préfixés/transformés ; ID WCP reste canonique.

## Lifecycle

provisioning, active, updating, suspended/locked selon engine
capability, deleting, deleted, degraded/unknown.

## Limits

Count, storage/usage, connections et engine-specific quotas seulement si
enforceables. Ne pas inventer une limite non supportée.

## Source of truth

Control-plane garde intent/ownership ; engine catalog est observé et
reconcilié.
