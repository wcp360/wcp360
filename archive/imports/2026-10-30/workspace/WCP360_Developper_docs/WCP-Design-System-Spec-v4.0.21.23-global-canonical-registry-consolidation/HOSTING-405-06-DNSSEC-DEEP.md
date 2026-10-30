# V4.0.5 --- DNSSEC Keys, Signing & DS Lifecycle

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

## Ressources

Signing policy, key references, DNSKEY observations, DS
desired/observed, signing status et rollover workflow.

## Clés

KSK/ZSK ou modèle provider-specific ; private key jamais exposée à
WCP-USER ni audit/logs.

## Enable

Capability → signing provision → DNSKEY verify → DS computation/provider
data → registrar/parent DS publication explicite → chain verification →
active.

## Disable

Ordre sûr obligatoire pour éviter validation failure ; retrait DS parent
et délai/cache avant arrêt de signature selon policy.

## Rollover

Workflow multi-étapes avec coexistence, publication, observation et
retirement. Jamais remplacement instantané aveugle.

## Failure

Bogus chain est critique ; WCP doit distinguer unsigned, insecure
delegation, signed-valid, signed-bogus et unknown.
