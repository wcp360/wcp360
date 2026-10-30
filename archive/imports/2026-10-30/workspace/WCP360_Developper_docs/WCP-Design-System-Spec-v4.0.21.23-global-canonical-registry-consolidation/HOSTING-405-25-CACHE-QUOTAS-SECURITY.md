# V4.0.5 --- Cache Entitlements, Quotas & Security

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

## Entitlements

Nombre d'instances, memory allocation, max connections, persistence
capability Redis, network access et engine versions.

## Parent limits

Somme/allocation revendeur/account ne dépasse pas capacité autorisée
selon overcommit policy.

## Secrets

Redis ACL/password/token references suivent secret lifecycle ; Memcached
isolation ne doit pas être présentée comme password auth si backend ne
l'offre pas.

## Abuse

Memory exhaustion, connection flood, key cardinality, large values et
cross-tenant endpoint discovery sont testés.

## Suspension

Définir si cache reste accessible pendant account suspension ; baseline
: deny tenant/app access ou stop selon policy, sans affecter autres
tenants.
