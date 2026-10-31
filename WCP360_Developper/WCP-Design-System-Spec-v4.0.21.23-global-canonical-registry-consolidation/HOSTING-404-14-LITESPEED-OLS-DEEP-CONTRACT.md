# V4.0.4 --- LiteSpeed / OpenLiteSpeed Deep Contract

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

## Adapters distincts

LiteSpeed Enterprise et OpenLiteSpeed possèdent capability matrices
séparées malgré concepts communs.

## Ressources

Listeners, virtual hosts, mappings, LSAPI/PHP bindings, cache policies
et service health.

## Licence

État licence Enterprise observé sans mélanger licensing WCP.
Expiration/limitation devient dependency degraded.

## Cache

LSCache uniquement si engine/plugin/capability compatibles. Purge
actions scopées au Website.

## Config

Même pipeline render/validate/stage/reload/verify. Pas d'édition
XML/conf arbitraire tenant.

## Migration

Comparer rewrites/cache/.htaccess compatibility avant changement depuis
Apache/Nginx.
