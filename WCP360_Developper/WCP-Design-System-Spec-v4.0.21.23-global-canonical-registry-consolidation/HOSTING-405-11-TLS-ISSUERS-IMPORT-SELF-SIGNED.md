# V4.0.5 --- TLS Issuers, Import & Self-signed Policy

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

## Issuers

Let's Encrypt/ACME compatible via adapter ; autres CA futures. Staging
et production sont des environnements distincts.

## Import

Cert chain + private key matching + SAN/expiry/key policy validation.
Refuser mismatch ou chaîne invalide.

## Self-signed

Autorisé seulement pour usages internes/diagnostic selon policy ;
clairement marqué non publicly trusted.

## Key algorithms

Capability/policy pour RSA/ECDSA et paramètres certifiés. Pas de choix
cryptographique arbitraire non validé.

## Chain

Stocker/servir chaîne correcte ; vérifier hostname, dates et issuer
chain lors du déploiement.
