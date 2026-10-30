# V4.0.4 --- Hosting Core Tranche 1 Closure Audit

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

## Travail effectué

**30 nouveaux contrats normatifs** ont été créés et **33 documents
historiques** ont été reliés à cette profondeur.

## Fermé au niveau SPECIFIED-DEEP

Accounts, reseller delegation, plans/entitlements/quotas, usage,
suspension/termination, domain canonical model/lifecycle,
aliases/redirects, Website/vhost, engine capability, Nginx, Apache,
proxy mode, LiteSpeed/OLS, cache/compression, headers/error pages, logs,
WAF boundary, PHP runtime/FPM/INI/extensions/isolation/OPcache, atomic
apply, abuse cases, Jobs/events/RBAC/audit et E2E scenarios.

## Non fermé

DNS authoritative et DNSSEC, ACME/TLS, DB/cache, mail/deliverability,
files/backups et security operations restent hors de cette tranche.

## Gate

NO CODE maintenu. `SPECIFIED-DEEP` n'est pas `GLOBAL READY`.
