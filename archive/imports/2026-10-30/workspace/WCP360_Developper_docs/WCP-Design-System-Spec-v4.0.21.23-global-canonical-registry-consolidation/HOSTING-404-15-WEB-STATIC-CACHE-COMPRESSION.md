# V4.0.4 --- Static Files, Cache & Compression Policies

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

## Static

Extensions/types et cache-control par profils versionnés. MIME source
contrôlée par admin/OS adapter.

## Compression

gzip/brotli seulement si capability. Types, minimum size et niveaux
bornés ; ne pas compresser secrets/flux inadaptés selon baseline.

## Browser cache

Profiles safe/default/aggressive avec effective config visible.
Overrides tenant bornés.

## Server cache

Distinguer static caching, reverse-proxy cache, FastCGI cache et
application cache. Aucun bouton générique « cache on » sans expliquer la
couche.

## Purge

Purge scoped au Website/cache key namespace ; audit pour purge massive.
