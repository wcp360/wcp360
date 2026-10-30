# 463 --- LDAP / Active Directory

## Objectif

Authentification/directory integration lorsque retenue.

## Configuration

Server/TLS, bind secret reference, base DN, user/group filters,
attribute mapping, nested groups policy.

## Sécurité

LDAPS/StartTLS selon politique ; bind secret write-only ; filtres
construits/échappés côté backend.

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
