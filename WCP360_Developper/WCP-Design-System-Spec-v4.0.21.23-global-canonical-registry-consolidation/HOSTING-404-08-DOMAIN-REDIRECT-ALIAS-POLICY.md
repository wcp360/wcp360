# V4.0.4 --- Domain Aliases, Redirects & Canonical Host Policy

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

## Alias

Alias HTTP peut partager Website mais conserve identité/domain mapping
distinct. TLS doit couvrir chaque hostname réellement servi.

## Redirect

Source hostname/path pattern borné, destination URL validée, status code
autorisé, HTTPS policy et loop detection.

## Canonical host

Policy explicite `www`/apex/autre ; pas de redirect automatique caché.

## Security

Empêcher open redirect via paramètres non contrôlés, header injection et
target schemes dangereux. Les règles avancées arbitraires ne sont pas
exposées à WCP-USER.

## Collision

Un hostname ne peut appartenir simultanément à deux vhosts actifs sur
même listener sans routing contract explicite.
