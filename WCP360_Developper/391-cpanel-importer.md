# 391 --- cPanel / WHM Importer

## Positionnement

Adaptateur optionnel du Migration Center pour importer les artefacts
réellement accessibles depuis cPanel/WHM.

## Mapping attendu

Accounts/packages where available, domains, web files, databases/users,
mailboxes/forwarders, DNS, SSL, cron et autres éléments uniquement si
l'adaptateur sait les convertir de manière fiable.

## Règle

Ne jamais prétendre à une parité 100 % : le preflight liste `supported`,
`partial`, `unsupported`, `manual`.

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
