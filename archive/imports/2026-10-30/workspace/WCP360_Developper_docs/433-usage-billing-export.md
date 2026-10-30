# 433 --- Usage / Billing Export

## Objectif

Exposer les données de consommation utiles aux systèmes de facturation
sans faire de WCP un ERP.

## API/Exports

Periodized usage records, tenant/account, metric, quantity, unit,
source, correction/version metadata.

## Connecteurs

WHMCS/Blesta/FossBilling/HostBill peuvent consommer ces données selon
leur modèle ; mapping spécifique au connecteur.

## Fiabilité

Records idempotents/versionnés ; corrections traçables ; fuseau/période
explicites.

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
