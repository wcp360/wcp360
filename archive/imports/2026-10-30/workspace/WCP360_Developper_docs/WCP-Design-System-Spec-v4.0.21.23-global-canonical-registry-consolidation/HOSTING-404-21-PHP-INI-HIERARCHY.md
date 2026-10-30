# V4.0.4 --- PHP INI Hierarchy, Validation & Effective Configuration

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

## Hiérarchie

Vendor/default → WCP runtime baseline → plan/profile → pool/site
overrides autorisés. Afficher source de chaque valeur effective.

## Registry

Chaque directive exposée possède type, unit, range/enum, mutability,
security classification, PHP-version applicability et restart/reload
requirement.

## Dangerous directives

`disable_functions`, `open_basedir`, upload/temp/session paths,
auto_prepend/append, allow_url\_\* et similaires ont policies dédiées ;
aucun textarea INI libre WCP-USER.

## Apply

Validate schema → render owned fragment → syntax/readback → reload
pool/service si requis → verify effective value.

## Secrets

INI n'est pas un secret store.
