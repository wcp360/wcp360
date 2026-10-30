# V4.0.5 --- ACME Account, Orders & Challenge Lifecycle

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

ACME account reference, order, authorization, challenge et certificate
result.

## Challenges

HTTP-01 et DNS-01 selon capability. TLS-ALPN-01 futur si certifié.

## HTTP-01

Le challenge path est géré temporairement sans permettre upload
arbitraire ; vérifier routage/redirect/proxy.

## DNS-01

Utilise DNS adapter/credential scoped. TXT challenge temporaire avec
cleanup idempotent ; wildcard requiert DNS-01.

## Rate limits

Issuer rate limits deviennent erreurs normalisées avec retry-after
lorsqu'exposé. Ne pas boucler agressivement.

## Ambiguïté

Après timeout, relire order/authorization avant de recréer une commande.
