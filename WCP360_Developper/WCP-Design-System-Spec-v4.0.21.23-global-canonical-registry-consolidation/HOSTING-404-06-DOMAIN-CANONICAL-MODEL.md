# V4.0.4 --- Domain, Subdomain & Alias Canonical Model

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

## Séparation

`Domain` d'hébergement ≠ `DNSZone` ≠ `RegistrarDomain` ≠ `Website` ≠
`MailDomain` ≠ `Certificate`.

## Types

Primary/addon hosting domain, subdomain, alias/parked domain et redirect
sont des intentions distinctes.

## Validation

IDN normalisé selon règles explicites, canonical ASCII representation,
lowercase DNS semantics, longueur/labels, public suffix awareness
lorsque pertinent, collision globale par node/tenant et reserved names.

## Ownership

Domain appartient à un HostingAccount. Un alias référence une cible
autorisée ; il ne devient pas une copie implicite du site.

## External DNS

Un domaine peut être hébergé web/mail avec DNS authoritative externe.
WCP doit afficher clairement qui contrôle DNS et quelles vérifications
sont possibles.
