# V4.0.5 --- DNS Jobs, Audit & Acceptance Scenarios

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

zone.provision/delete/reconcile, rrset.apply, delegation.check,
dnssec.enable/disable/rollover, ptr.apply.

## Audit

Actor, zone, diff RRset sans secrets, provider, desired/observed
versions, result/evidence.

## Scénarios

API timeout après write ; provider rate limit ; invalid zone; CNAME
conflict ; NS mismatch ; lame delegation ; DNSSEC DS incorrect ;
rollover interrupted ; external drift ; provider credential revoked ;
authoritative service reload failure ; stale propagation evidence.

## READY

Tests négatifs cross-tenant, timeout ambiguous, concurrent RRset update
et provider drift obligatoires.
