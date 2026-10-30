# V4.0.5 --- Database Health, Metrics & Degraded Modes

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

## Health

Service state + connection probe + engine catalog query.
`systemd active` seul insuffisant.

## Metrics

Connections, max connections, DB size, slow query indicators,
cache/buffer metrics seulement si collecte autorisée. Cardinalité
maîtrisée.

## Privacy

SQL text peut contenir données/secrets ; accès slow-query/process list
est permissionné et redacted.

## Degraded

Disk pressure, read-only/recovery mode, connection saturation,
replication issues futures, corrupt catalog indicators et auth failure
sont causes distinctes.

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-03-METRICS-MODEL.md`
- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-02-HEALTH-STATE-MODEL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
