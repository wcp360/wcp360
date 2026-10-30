# V4.0.4 --- Hosting Core Tranche 1 --- READY Gate

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

## Couverture

Accounts/Plans/Reseller, Domain model/orchestration, Website/vhost,
Nginx, Apache, Nginx→Apache, LiteSpeed/OLS, HTTP policies, logs/WAF, PHP
runtimes/FPM/INI/extensions/security/performance.

## Critères

Chaque surface historique doit être reliée aux contrats profonds ;
permissions et entitlements testés séparément ; negative cross-tenant
tests ; atomic apply/recovery ; multi-engine capability matrix ;
multi-PHP dependency graph ; suspend/terminate recovery ; account/domain
lifecycle.

## Statut

**TRANCHE 1 = SPECIFIED-DEEP. Pas GLOBAL READY.**

DNS/TLS, Database/Cache, Mail, Files/Backup et Security restent à
approfondir avant fermeture Hosting Core.
