# 480 --- License & Subscription Manager

## Objectif

Gérer les licences WCP et intégrations commerciales sans rendre la pile
commerciale obligatoire.

## Pages

Licenses; Products; Assigned nodes; Status; Expiration; Alerts;
Activation/Refresh.

## Données

Product, license identifier redacted, status, edition/features, node
binding, expiry, last check.

## Sécurité

License secrets/keys masqués ; appels vendor via adaptateurs ; aucun
blocage arbitraire de la pile open-source.

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
