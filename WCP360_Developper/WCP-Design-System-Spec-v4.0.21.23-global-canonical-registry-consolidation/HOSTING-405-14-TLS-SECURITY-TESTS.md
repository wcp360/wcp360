# V4.0.5 --- TLS Security, Failure & Acceptance Matrix

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

Wrong SAN, expired cert, key mismatch, incomplete chain, ACME HTTP
routing failure, DNS-01 cleanup failure, issuer unavailable, rate limit,
clock drift, deploy reload failure, stale certificate still served,
wildcard unauthorized, private-key access attempt, renewal job crash.

## Vérification

Filesystem/config evidence + live TLS handshake depuis listener concerné
lorsque possible.

## READY

Aucune réussite `renewed` tant que le nouveau certificat n'est pas
vérifié sur les bindings obligatoires.
