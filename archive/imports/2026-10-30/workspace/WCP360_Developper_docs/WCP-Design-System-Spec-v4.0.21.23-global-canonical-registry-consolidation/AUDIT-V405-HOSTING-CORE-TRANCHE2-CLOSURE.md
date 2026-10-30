# V4.0.5 --- Hosting Core Tranche 2 Closure Audit

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

## Résultat

30 nouveaux contrats normatifs. 1 documents historiques existants reliés
lorsque présents.

## Couverture approfondie

DNS zone/RRsets/providers/authoritative/delegation/DNSSEC/PTR ; TLS
certificate/ACME/import/renewal/policies ; MySQL/MariaDB/PostgreSQL ; DB
users/grants/network/TLS/backup/upgrade/health ; Redis/Memcached ; DB
tools SSO ; Jobs/audit/failure/E2E.

## Points de sécurité fermés

Pas d'open resolver implicite, pas de faux indicateur de propagation,
DNSSEC multi-step, private keys protégées, renouvellement sans écraser
l'ancien certificat avant vérification, DB superuser non tenant, DDL
timeout reconcilié, DB publique non implicite, Redis DB index non
considéré comme isolation forte, Memcached privé, SSO sans password en
URL.

## Reste du Hosting Core

Mail/deliverability puis Files/Backup/Security.
