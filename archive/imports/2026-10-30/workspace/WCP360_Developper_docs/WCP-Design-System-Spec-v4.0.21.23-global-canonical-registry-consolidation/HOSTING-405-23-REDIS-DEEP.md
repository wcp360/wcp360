# V4.0.5 --- Redis Deep Contract

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

## Modèle

Redis service/instance logique, endpoint/socket, account/app binding,
memory limit/policy, auth reference, persistence policy et status.

## Isolation

Préférer instance/socket/ACL architecture certifiée plutôt que simple DB
index comme isolation de sécurité. Les numbered databases ne constituent
pas une frontière tenant robuste.

## Network

No public exposure by default. Loopback/private socket, firewall and
auth.

## Memory

maxmemory et eviction policy bornés par plan/system capacity. cgroup
limit et Redis maxmemory sont complémentaires.

## Persistence

RDB/AOF policy explicitement choisie ; un cache peut être ephemeral.
Backup n'est promis que si persistence/backup contract le permet.

## Dangerous commands

Admin/config/debug/shutdown/module commands non délégables aux tenants
par défaut.
