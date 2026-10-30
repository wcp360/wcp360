# V4.0.5 --- Database Users, Grants & Credential Lifecycle

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

## User

ID WCP, physical principal, engine, account scope, login state,
credential reference et grants.

## Password

Generate/import selon policy ; jamais relisible après stockage si
architecture le permet. Rotation produit nouveau secret et vérifie
authentification avant retrait de l'ancien lorsque moteur/workflow le
permet.

## Grants

Catalogue de privilege profiles + granular grants certifiés. Refuser
cross-tenant object references.

## Shared users

Autoriser un principal sur plusieurs DB du même account seulement
explicitement ; cross-account interdit.

## Revocation

Revoke access ≠ drop user. Drop nécessite dependency preview.
