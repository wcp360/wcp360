# V4.0.5 --- PostgreSQL Deep Contract

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

## Concepts

Database, role/login, schema et privileges ne sont pas identiques. WCP
V1 peut simplifier l'UX sans les confondre dans le modèle.

## Provision

Database owner role scoped, encoding/locale/template policy contrôlée,
connection endpoint et grants vérifiés.

## Superuser

Jamais tenant. `CREATEDB`, `CREATEROLE`, replication, bypass RLS et
autres attributs puissants sont policy-gated.

## Connections

pg_hba.conf n'est pas un textarea tenant. WCP gère profils d'accès
certifiés/local socket/TCP/TLS selon architecture.

## Drop

Bloquer/planifier selon active sessions et dependencies ; force
termination est action séparée et auditable.

## Extensions

PostgreSQL extensions suivent registry/certification ; certaines exigent
superuser et ne sont pas librement installables.
