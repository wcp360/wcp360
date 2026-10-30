# V4.0.5 --- DNS Zone Canonical Resource Model

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

`DNSZone` est l'autorité logique d'une zone. Elle référence un `Domain`
si pertinent mais peut avoir un lifecycle différent.

## Ressources

Zone, RecordSet/RRset, Record, provider binding, SOA policy, nameserver
set, DNSSEC state et delegation observation.

## Modes

`local_authoritative`, `external_provider_managed`,
`external_unmanaged/read-only-observed`, et futur `secondary`.

## Etat

desired records, observed provider records, serial/version,
propagation/delegation evidence, health/freshness.

## Ownership

Une zone appartient à un scope tenant/account. Le provider credential
est séparé et peut être global, reseller ou tenant selon policy.

## Suppression

Supprimer la zone WCP ne doit jamais supprimer un domaine registrar.
Impact preview : mail records, ACME, service discovery, custom records.
