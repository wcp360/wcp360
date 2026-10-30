# V4.0.4 --- Web Engine Capability & Compatibility Matrix

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

## Engines

Nginx baseline recommandé, Apache Tier 1, Nginx→Apache reverse proxy
possible, LiteSpeed Enterprise/OpenLiteSpeed via adapters certifiés.

## Capability

HTTP versions, reverse proxy, rewrite model, per-dir overrides, WAF,
compression, cache, PHP integration, WebSocket, static files, access
log, graceful reload.

## Règle

L'UI présente uniquement l'intersection entre capability engine, OS
profile, admin policy et plan entitlement.

## Migration engine

Changer d'engine est un workflow : compatibility scan → unsupported
directives/features → render preview → maintenance strategy → apply →
verify → rollback/reconcile.

## No false equivalence

Une option Apache `.htaccess` n'est pas prétendue compatible Nginx ; une
fonctionnalité LiteSpeed commerciale n'est pas simulée sur OLS.
