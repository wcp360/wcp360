# V4.0.4 --- Website & VirtualHost Canonical Model

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

## Website

Ressource métier : account, domains, document root reference, web
engine/profile, PHP/runtime mapping, TLS binding, logging profile,
status.

## VirtualHost

Projection engine-specific du Website. L'utilisateur ne gère pas
directement un blob Nginx/Apache.

## DocumentRoot

Relative/canonical reference sous FileRoot autorisé ; symlink policy
appliquée ; pas de path absolu tenant arbitraire.

## Listeners

Les ports/adresses disponibles proviennent d'un registry WCP-ADMIN.
WCP-USER sélectionne des profils autorisés.

## Config generation

Desired Website → adapter render → syntax validate → conflict validate →
stage → atomic activate → reload → request/health verify → generation
observed.
