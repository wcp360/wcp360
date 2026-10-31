# V4.0.4 --- Hosting Core RBAC, Audit & Step-up Matrix

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

## Permission families

accounts.*, resellers.*, plans.*, domains.*, websites.*, web.service.*,
php.runtime.*, php.pool.*, impersonation.*, branding.*.

## WCP-USER

Jamais `web.service.control`, package install global, engine global
config, arbitrary vhost, system user primitive ou PHP runtime repository
control.

## Step-up

Impersonation start, terminate/purge account, destructive domain
move/delete, engine migration, security-sensitive overrides et global
service operations selon policy.

## Audit

Real/effective actor, tenant, resource, generation, diff summary, Job,
adapter/capability, verification result et reason pour actions
critiques.
