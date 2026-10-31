# V4.0.4 --- PHP-FPM Pool Deep Contract

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

## Pool

Un pool appartient à HostingAccount/Website selon policy et s'exécute
sous Unix identity dédiée/scopée.

## Socket

Unix socket préféré lorsque architecture locale le permet ; path issu
registry, owner/group/mode contrôlés. TCP binding si nécessaire reste
privé et conflict-checked.

## Process manager

`static`, `dynamic`, `ondemand` uniquement si version supports ;
paramètres bornés et cohérents. Effective values visibles.

## Limits

memory/process/request/time/resource limits coordonnés avec
cgroups/plan. Ne pas promettre qu'un php.ini memory_limit remplace une
limite RAM système.

## Environment

Env allowlist + secret references ; clear_env/security policy explicite.

## Health

Master unit, socket availability, status metrics si enabled,
queue/workers/slow requests avec Unknown si instrumentation absente.
