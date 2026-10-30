# V4.0.5 --- TLS Protocol, Cipher & HSTS Policy Boundary

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

## Profiles

TLS profiles versionnés par WCP-ADMIN : modern/default/compatibility.
WCP-USER sélectionne uniquement profils autorisés.

## Protocols/ciphers

Pas de textarea OpenSSL libre. Les valeurs dépendent engine/version/OS
crypto policy et certification.

## HSTS

Reste une HTTP policy séparée du certificat. Certificat valide ne
justifie pas automatiquement preload/includeSubDomains.

## OCSP/stapling

Capability-dependent ; failure ne doit pas rendre faussement le
certificat invalid si le protocole ne l'exige pas.

## Legacy

Les exceptions de compatibilité sont explicites, temporaires si possible
et auditées.
