# V4.0.5 --- TLS Renewal, Deployment & Rollback

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

## Scheduler

Renewal window avant expiration avec jitter et retry policy. Une
tentative échouée ne remplace pas le certificat encore valide.

## Pipeline

Order → validate → issue → verify certificate/key → stage bindings →
web/mail config validate → atomic deploy → reload → live handshake
verify → active.

## Rollback

Si deploy/reload/handshake échoue, restaurer binding précédent
lorsqu'encore valide.

## Alerts

Seuils multi-niveaux avant expiration et alerte critique si aucun
certificat valide déployable.

## Multi-service

Un même certificate peut avoir plusieurs bindings ; chaque binding
possède résultat de déploiement.
