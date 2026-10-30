# V4.0.4 --- Reseller Delegation, Scope & Impersonation

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

## ResellerAccount

Scope parent de HostingAccounts, avec allowance, brand profile,
permissions délégables et usage agrégé.

## Délégation

`effective_reseller_allowance = min(admin policy, assigned reseller allowance)`.
Le revendeur ne peut déléguer que des features autorisées et des
quantités restant disponibles.

## Oversubscription

Policy explicite par ressource : interdite, soft commercial overcommit,
ou techniquement enforceable. CPU/RAM/disk ne doivent pas être confondus
avec des compteurs commerciaux.

## Impersonation

Conserver `real_actor`, `effective_actor`, reseller/account scope,
reason, started_at, expires_at. Bannière permanente. Certaines actions
critiques peuvent être interdites même avec impersonation.

## White-label

Logo/couleurs/domain d'accès ne modifient ni trust boundary, ni
cookie/session scope, ni certificats, ni ownership DNS sans workflows
dédiés.
