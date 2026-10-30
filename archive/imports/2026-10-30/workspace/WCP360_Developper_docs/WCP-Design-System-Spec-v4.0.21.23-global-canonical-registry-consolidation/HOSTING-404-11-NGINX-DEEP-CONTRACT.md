# V4.0.4 --- Nginx Deep Contract

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

## Ownership

WCP gère includes/sites dédiés selon profil OS ; ne remplace pas
`nginx.conf` externe complet sans ownership explicite.

## Server block

listen profile, server_names, root, index policy, access/error logs, TLS
binding, PHP upstream, static/cache policy, redirects et approved
advanced features.

## Validation

`nginx -t` conceptuel via adapter fermé + détection WCP des collisions
avant activation. Stdout brut n'est pas API.

## Reload

Graceful reload privilégié. Vérifier master/service state et probes du
Website ; rollback vers dernière génération valide si config/reload
échoue.

## Security

Deny sensitive dotfiles/config/secrets selon baseline, safe symlink
policy, body/header/time limits par profiles, no arbitrary `include`.

## HTTP/2/3

Exposés seulement si build/version/listener/TLS capability certifiés.
