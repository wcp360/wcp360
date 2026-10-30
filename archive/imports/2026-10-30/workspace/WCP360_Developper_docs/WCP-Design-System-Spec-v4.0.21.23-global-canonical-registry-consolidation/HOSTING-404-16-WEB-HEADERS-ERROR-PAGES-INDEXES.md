# V4.0.4 --- HTTP Headers, Error Pages & Directory Indexes

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

## Headers

Catalogue structuré pour HSTS, CSP templates, X-Content-Type-Options,
Referrer-Policy et custom headers autorisés. Validation nom/valeur, CRLF
interdits.

## HSTS

Avertissement fort sur includeSubDomains/preload ; ne jamais activer
preload automatiquement.

## Error pages

Fichiers sous FileRoot/site ou templates WCP sûrs. Codes autorisés ;
fallback si fichier absent.

## Directory listing

Disabled par défaut ; entitlement/policy pour activation.

## Index files

Liste bornée/validée ; aucun path traversal.

## Advanced

Directives engine-specific non modélisées restent WCP-ADMIN ou hors V1,
pas un textarea root.
