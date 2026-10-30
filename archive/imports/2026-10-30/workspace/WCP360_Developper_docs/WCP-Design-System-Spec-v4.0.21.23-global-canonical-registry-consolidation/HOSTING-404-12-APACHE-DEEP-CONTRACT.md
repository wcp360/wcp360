# V4.0.4 --- Apache HTTP Server Deep Contract

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

## VHost

Address/listener profile, ServerName/Alias, DocumentRoot, directory
policy, logs, TLS, PHP integration/proxy, approved modules/features.

## MPM

Observer MPM actif et compatibility PHP model. mod_php n'est pas
baseline ; PHP-FPM est préféré.

## Modules

Enable/disable seulement depuis registry certifié avec
dependency/conflict preflight. Aucun nom de module arbitraire root.

## .htaccess

Policy par plan/site : disabled, limited, enabled. Expliquer
coût/performance/sécurité. Overrides permis via AllowOverride profile,
pas chaîne arbitraire.

## Validation/reload

Config test → stage → graceful reload → probes → rollback/reconcile.

## Security

Directory defaults deny-by-default, server tokens/signature policy,
unsafe CGI/options contrôlés, symlink policy et WAF capability.
