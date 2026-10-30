# V4.0.4 --- Nginx → Apache Reverse Proxy Mode

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

## Architecture

Nginx front listener → Apache backend listener privé/loopback → PHP-FPM
ou handler certifié.

## Ownership

WCP génère les deux projections depuis un Website unique ; éviter deux
sources de vérité.

## Client IP

Trusted proxy chain explicite. Ne jamais faire confiance à
`X-Forwarded-For` venant d'Internet hors proxy connu.

## TLS

Terminaison TLS généralement front ; backend transport et headers
(`Host`, scheme, client IP) définis.

## Ports

Backend ports alloués depuis registry, non exposés publiquement par
firewall.

## Failure

Nginx healthy + Apache down = Website degraded/unavailable ;
l'agrégateur ne doit pas déclarer le site healthy.
