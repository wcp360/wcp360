# 461 --- OIDC / OAuth2 Identity Provider

## Objectif

SSO via OpenID Connect ; OAuth2 seul n'est pas traité comme protocole
d'identité sans couche adaptée.

## Configuration

Issuer, client ID, secret reference, redirect URIs, scopes/claims, group
mapping, logout/session policy.

## Sécurité

PKCE/nonce/state selon flow retenu, issuer/audience validation, secrets
redacted.

## Contrat transversal WCP

-   **WCP-ADMIN** configure les moteurs, politiques, capacités globales
    et opérations d'infrastructure.
-   **WCP-USER** n'expose que les fonctions autorisées dans le scope
    client/revendeur.
-   Les autorisations sont contrôlées côté backend : rôle + scope +
    ownership + capability.
-   Toute mutation sensible est auditée ; toute opération longue devient
    un job Task Center.
-   Les secrets sont redacted dans logs, audit, notifications et IA.
-   Les intégrations tierces sont des adaptateurs/capabilities, jamais
    des dépendances implicites.
-   États UI : loading, empty, denied, unavailable, partial, stale,
    pending, failed, success confirmé.
