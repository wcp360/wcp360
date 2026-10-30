# V4.0.5 --- Reverse DNS / PTR Boundary

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

## Principe

PTR dépend généralement du propriétaire du bloc IP/provider, pas de la
zone forward du tenant.

## Ressource

ReverseDnsBinding : IP, desired hostname, provider binding, observed
PTR, ownership/capability.

## Permissions

WCP-USER peut demander un PTR uniquement pour IP qui lui est allouée et
si provider policy l'autorise.

## Mail

PTR est un signal de délivrabilité, mais WCP ne garantit pas la
réputation. Vérifier forward-confirmed reverse DNS si policy.

## Shared IP

Empêcher plusieurs tenants de modifier le PTR d'une IP partagée.
