# V4.0.4 --- PHP Security, Isolation & Filesystem Boundaries

## Statut

**NORMATIVE --- V4.0.4 HOSTING CORE DEEP SPECIFICATION --- TRANCHE 1 ---
NO CODE**

Cette spécification approfondit Accounts / Plans / Reseller → Domains →
Web Stack → Apache/Nginx/LiteSpeed → PHP/PHP-FPM. Elle instancie V4.0.2
et V4.0.3 sans produire de code.

## Invariants

-   WCP-ADMIN administre hôte, capacités, politiques, moteurs et limites
    maximales.
-   WCP-USER administre uniquement les ressources déléguées de son
    scope.
-   Hiérarchie commerciale et RBAC restent distincts.
-   Toute ressource possède desired/observed state, generation et
    ownership.
-   Toute mutation sensible suit validate → authorize → entitlement →
    preflight → job/lock → apply → verify → reconcile → audit.
-   Les fichiers web/PHP générés sont des projections, jamais la source
    de vérité.
-   Aucun tenant ne fournit de configuration root arbitraire.

## Identity

PHP pool ne s'exécute jamais root. UID/GID et supplementary groups
minimaux.

## Filesystem

Write access limité aux paths nécessaires. WCP
releases/config/secrets/root broker sockets interdits.

## Temp/session/upload

Directories privées ou correctement isolées, permissions/cleanup/quotas.
Éviter shared world-writable sans protections adaptées.

## Functions/process

Les restrictions PHP ne remplacent pas Unix/cgroup/MAC isolation.
Shell/process execution policy dépend du plan et de la menace, mais doit
rester explicitement modélisée.

## Network

Future outbound restrictions peuvent être capability/policy ; ne pas
annoncer isolation réseau si elle n'est pas enforceable.

## Cross-tenant tests

Read/write/symlink/session/temp/socket/process boundary tests
obligatoires.
