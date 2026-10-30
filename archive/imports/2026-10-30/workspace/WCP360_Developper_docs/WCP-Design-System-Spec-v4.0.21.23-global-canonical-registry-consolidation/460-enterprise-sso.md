# 460 --- Enterprise Identity & SSO

## Objectif

Permettre fournisseurs d'identité externes sans supprimer le contrôle
local de sécurité WCP.

## Pages

Identity Providers; Domain/tenant mappings; Group→Role mappings; Login
policy; Sessions; Break-glass policy.

## Providers

OIDC/OAuth2, SAML 2.0 et LDAP/Active Directory sont des adaptateurs
séparés.

## Sécurité

Mapping groupes→rôles explicitement revu ; compte break-glass/local
selon politique ; MFA assurance provenant de l'IdP interprétée
uniquement si le contrat le permet.

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
