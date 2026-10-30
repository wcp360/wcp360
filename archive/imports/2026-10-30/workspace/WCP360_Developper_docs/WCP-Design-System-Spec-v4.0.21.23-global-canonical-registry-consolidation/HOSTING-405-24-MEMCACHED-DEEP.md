# V4.0.5 --- Memcached Deep Contract

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

## Nature

Cache volatile sans persistence/tenant auth native robuste selon
déploiement classique.

## Isolation

Instance dédiée ou socket/network isolation par account/app selon
architecture ; jamais instance publique partagée non protégée.

## Limits

Memory, connections et item size via profiles certifiés. cgroup en
complément.

## Network

UDP désactivé par défaut sauf besoin explicitement justifié ; TCP/socket
privé.

## Lifecycle

Provision/start/stop/restart/delete instance avec verify endpoint.
Suppression = perte de cache attendue, pas de restore.

## UX

Présenter clairement Memcached comme cache éphémère, pas comme base de
données durable.
