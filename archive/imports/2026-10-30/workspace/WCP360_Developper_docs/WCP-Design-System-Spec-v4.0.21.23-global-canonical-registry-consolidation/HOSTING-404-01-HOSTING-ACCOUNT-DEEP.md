# V4.0.4 --- Hosting Account --- Deep Resource Contract

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

## Ressource

`HostingAccount` représente l'unité technique de délégation d'un client
final. Elle possède ID opaque, parent reseller éventuel, plan
assignment, Unix identity reference, home/FileRoot, lifecycle,
entitlements effectifs, usage, suspension state et resource relations.

## Lifecycle

`pending → provisioning → active → suspending → suspended → updating → terminating → deleted`,
plus `failed/degraded/unknown`.

## Provisioning

Réserver identité logique → vérifier parent/plan/quota → allouer Unix
identity → préparer FileRoot/home → appliquer resource limits → créer
ressources minimales → verify ownership/isolation → active.

## Suspension

La suspension ne supprime rien. Elle définit explicitement les effets
sur web, login SSH/SFTP, mail, DB, cron/apps et API. Les effets doivent
être réversibles et observables.

## Termination

Impact preview obligatoire : domains, websites, mail, DB, runtimes,
cron, backups, DNS/TLS et external resources. Rétention et purge sont
deux étapes distinctes.

## Rename

Le display/login name peut évoluer sans changer l'ID canonique. Tout
rename Unix/path exige workflow de migration séparé.
