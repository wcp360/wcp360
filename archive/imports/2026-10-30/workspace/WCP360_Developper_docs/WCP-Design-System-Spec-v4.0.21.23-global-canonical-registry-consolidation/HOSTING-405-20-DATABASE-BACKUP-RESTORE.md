# V4.0.5 --- Database Backup, Restore & Consistency

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

## Méthodes

Logical dump baseline ; physical/hot backup selon engine/capability
ultérieure. Méthode et consistency guarantees affichées.

## Backup

Scope DB, engine/version, charset/encoding metadata, timestamp,
checksum, encryption reference, size et tool/provider version.

## Restore

New database ou overwrite workflow distinct. Preflight
version/space/active connections/ownership ; staging lorsque possible.

## Overwrite

Action destructive avec backup préalable selon policy, step-up et
rollback réaliste documenté.

## Verification

Exit status ne suffit pas : inspecter catalog/objects et effectuer
checks définis ; restore test périodique recommandé.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`
- `BOOT-4014-26-CONTROL-PLANE-RESTORE.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
