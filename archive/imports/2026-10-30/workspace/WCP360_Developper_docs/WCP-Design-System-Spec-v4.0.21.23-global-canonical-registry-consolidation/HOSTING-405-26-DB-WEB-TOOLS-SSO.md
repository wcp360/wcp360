# V4.0.5 --- phpMyAdmin / pgAdmin / Adminer Integration & SSO

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

## Principe

Les outils web DB sont des applications privilégiées à forte surface
d'attaque, pas de simples liens.

## Outils

phpMyAdmin pour MySQL/MariaDB, pgAdmin pour PostgreSQL, Adminer
optionnel selon policy/certification.

## SSO

WCP ne transmet pas le mot de passe DB dans URL. Utiliser mécanisme de
session/bootstrap éphémère ou credential handoff sécurisé propre à
l'outil si certifié.

## Scope

L'utilisateur arrive uniquement sur moteurs/DB autorisés ; l'outil doit
encore appliquer les permissions DB natives.

## Sessions

Courtes, CSRF protected, same-site/origin policy, logout/revocation,
real/effective actor correlation si possible.

## Exposure

Accès derrière auth WCP/reverse proxy, rate limiting et security headers
; possibilité de désactiver globalement.
