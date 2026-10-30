# 392 --- Plesk Importer

## Positionnement

Adaptateur optionnel Plesk pour Migration Center.

## Mapping

Subscriptions/accounts, domains, files, DB, mail, DNS, certificates,
scheduled tasks et application metadata selon ce que le connecteur peut
lire et convertir.

## Validation

Rapport de compatibilité avant transfert ; aucun paramètre Plesk inconnu
n'est silencieusement abandonné.

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
