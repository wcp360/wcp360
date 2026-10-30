# 382 --- Nameservers & Glue Records

## Objectif

Gérer nameservers personnalisés et glue records lorsque le
registrar/provider le permet.

## Pages

Nameservers; Child/Host Records; Glue; Validation.

## Données

Hostname NS, IPv4/IPv6 glue, registrar capability, état attendu/observé.

## Actions

Create/update/delete host record, assign nameservers, validate
delegation.

## Sécurité

Vérifier ownership/scope du domaine ; distinguer modification registrar
de modification de zone DNS.

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
