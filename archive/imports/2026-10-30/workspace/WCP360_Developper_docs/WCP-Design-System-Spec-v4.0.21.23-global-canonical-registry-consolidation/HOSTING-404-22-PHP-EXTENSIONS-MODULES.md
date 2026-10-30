# V4.0.4 --- PHP Extensions & Module Governance

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

## Inventory

Extension name canonical, runtime version, provider package,
installed/enabled, config fragment, dependencies/conflicts.

## Install/remove

Via package layer certifiée. WCP-USER peut demander uniquement
extensions autorisées ; installation système reste WCP-ADMIN/job.

## Shared impact

Une extension installée pour une runtime peut affecter plusieurs
pools/sites. Preview obligatoire avant disable/remove.

## PECL

Pas d'installation arbitraire depuis Internet en V1. Tout canal tiers
futur nécessite supply-chain policy, signatures/checksums/build
isolation et certification.

## Verification

CLI/FPM contexts peuvent différer ; vérifier le SAPI réellement utilisé.
