# V4.0.5 --- Authoritative DNS Service & Local Engine Boundary

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

## Engine

BIND/PowerDNS/Knot ou autre moteur futur uniquement via adapter certifié
; le contrat n'impose pas encore le moteur.

## Zones

WCP génère les projections depuis le modèle DNS. Pas d'éditeur libre de
zone root pour WCP-USER.

## Reload

Render → syntax/semantic validation → stage → atomic activate →
reload/reconfigure → authoritative query verify.

## Recursion

Authoritative et recursive resolver sont des rôles distincts. Un service
authoritative public ne doit pas devenir open resolver par défaut.

## Transfer

AXFR/IXFR, TSIG et secondaries sont capabilities avancées avec secrets
dédiés, ACL et audit.

## Health

Service active n'implique pas zone healthy : effectuer probes
SOA/NS/record selon scope.
