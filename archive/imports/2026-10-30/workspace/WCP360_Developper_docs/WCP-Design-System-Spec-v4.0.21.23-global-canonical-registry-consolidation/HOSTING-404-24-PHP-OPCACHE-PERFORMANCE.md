# V4.0.4 --- PHP OPcache & Performance Profiles

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

## Scope

OPcache est runtime/service-level avec impacts multi-sites ; les
réglages globaux sont WCP-ADMIN.

## Profiles

Conservative/default/high-density/development peuvent être versionnés.
Valeurs exactes dépendent version/RAM/workload et ne sont pas
universelles.

## Metrics

Memory usage/free/wasted, hit rate, restarts si instrumentation fiable.
Unknown sinon.

## Reset

OPcache reset est action sensible potentiellement multi-site ;
scope/impact affichés et audités.

## Deployment

Déploiements atomiques doivent considérer realpath/opcache semantics ;
ne pas recommander reset systématique sans besoin.
