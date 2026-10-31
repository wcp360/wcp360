# V4.0.4 --- PHP Runtime Version Lifecycle

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

## PhpRuntime

Version, provider/repository, SAPI/FPM, installed state, support status,
extensions, service binding, capability/certification.

## Install

Plan package transaction via System layer → verify binary/FPM unit →
baseline INI → health → available.

## Removal

Dependency graph Websites/Pools/extensions → block if referenced →
explicit remap workflow → remove → verify.

## Defaults

Server default, plan allowed versions et Website selected version sont
distincts. Changer server default ne remappe pas silencieusement les
sites existants.

## EOL

Version EOL peut être `deprecated/blocked_for_new` tout en restant
temporairement active pour migration ; alerte et remediation.

## V4.0.12 normative deepening

- `LIFE-4012-01-PLATFORM-RELEASE-MODEL.md`
- `LIFE-4012-14-COMPATIBILITY-MATRIX.md`
- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
