# V4.0.4 --- Accounts / Domains / Web / PHP Jobs & Events

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

## Jobs

`account.provision/suspend/unsuspend/terminate/purge`, `plan.apply`,
`domain.provision/move/suspend/delete`, `website.apply`,
`webengine.reload`, `php.runtime.install/remove`, `php.pool.apply`,
`php.extension.change`.

## Events

Facts après transitions vérifiées : account.*, plan.assignment_changed,
domain.*, website.config_applied, php.runtime_changed, php.pool_changed.

## Correlation

Un domain provisioning parent peut causer plusieurs child Jobs ;
correlation commune, causation distincte.

## Retry

Chaque Job mappe vers V4.0.2 retry/reconcile semantics ; aucun « retry
all » aveugle.
