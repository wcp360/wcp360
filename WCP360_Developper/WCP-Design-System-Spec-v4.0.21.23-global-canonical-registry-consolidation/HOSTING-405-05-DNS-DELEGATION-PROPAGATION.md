# V4.0.5 --- DNS Delegation, Nameservers & Propagation

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

## Delegation

Observer parent NS lorsque possible et comparer aux NS attendus. WCP
distingue configuration de zone et délégation registrar.

## Glue

Host records/glue sont opérations registrar distinctes ; jamais simulées
par simple création d'un A record dans la zone enfant.

## Propagation

Pas de pourcentage fictif. Afficher evidence : authoritative answers,
parent delegation, resolver observations facultatives et timestamps.

## Migration

Pré-baisser TTL si possible → attendre fenêtre → changer
records/delegation → vérifier → restaurer TTL. Le cache Internet reste
hors contrôle WCP.

## Split-brain

NS parent et zone NS incohérents, serveurs répondant avec serials
divergents et lame delegation sont des états dégradés distincts.
