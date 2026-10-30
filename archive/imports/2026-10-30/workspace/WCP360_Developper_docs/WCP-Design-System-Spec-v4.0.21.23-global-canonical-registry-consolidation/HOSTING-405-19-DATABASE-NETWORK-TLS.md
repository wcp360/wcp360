# V4.0.5 --- Database Network Exposure, Endpoints & TLS

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

## Baseline

DB locales écoutent loopback/socket ou réseau privé selon architecture.
Exposition publique n'est jamais implicite.

## Endpoint

Host/socket, port, TLS requirement, allowed source policy et engine
reference.

## Firewall

Ouverture coordonnée avec System firewall contract. DB bind + firewall +
auth doivent converger ; un seul élément ne suffit pas.

## TLS

Server cert binding et client verification selon profile. Ne pas
afficher « SSL enabled » sans vérifier endpoint effectif.

## Tenant remote access

IP/CIDR allowlist bornée, expiration optionnelle, audit et anti-lockout
local admin.

## V4.0.14 normative deepening

- `BOOT-4014-07-NETWORK-IPAM-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
