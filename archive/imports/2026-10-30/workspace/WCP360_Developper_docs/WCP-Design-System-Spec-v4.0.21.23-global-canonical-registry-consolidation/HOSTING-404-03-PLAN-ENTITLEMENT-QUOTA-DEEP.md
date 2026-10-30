# V4.0.4 --- Hosting Plans, Entitlements, Quotas & Effective Values

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

## Modèle

`HostingPlan` contient des entitlements typés et versionnés.
`PlanAssignment` fixe la version appliquée. Les overrides sont
explicites et tracés.

## Dimensions

Disk bytes/inodes, bandwidth, domains/subdomains/aliases, websites,
DB/users, mailboxes/aliases, SFTP/SSH, cron, backups, applications,
PHP/runtime versions, Redis/Memcached, API, CPU/RAM/PIDs/I/O lorsque
enforceable.

## Valeur effective

Global policy → reseller allowance → plan → account override autorisé →
resource-specific limit. UI affiche valeur effective ET origine.

## Upgrade

Valider capacité parent, appliquer nouveaux entitlements, provisionner
capabilities si nécessaire, verify.

## Downgrade

Comparer usage et dépendances. Si incompatible : refuser, programmer
remediation/grace ou passer non-compliant selon policy ; jamais
supprimer automatiquement des ressources.

## V4.0.12 normative deepening

- `LIFE-4012-21-ENTITLEMENT-CANONICAL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
