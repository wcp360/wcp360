# V4.0.5 --- DNS Provider Adapter Contract

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

## Providers

Serveur DNS local et APIs tierces sont des adapters derrière un contrat
commun.

## Opérations

discover zone, read RRsets, plan diff, create/update/delete RRset,
verify readback, DNSSEC capability et provider health.

## Idempotency

Après timeout d'API, relire l'état distant avant retry. Ne pas créer de
doublon parce qu'une réponse est perdue.

## Rate limits

Respecter limites et Retry-After ; backoff+jitter ; état
`waiting_external` si nécessaire.

## Credentials

Scopes minimaux, secret references, rotation et provider-account
identity distincte du tenant.

## Drift

Diff provider vs desired : classify → alert/reconcile selon ownership.
Une zone external-unmanaged n'est jamais écrasée automatiquement.
