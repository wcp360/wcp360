# 403 --- Outbound Spam & Reputation

## Objectif

Détecter et contenir l'abus sortant tout en fournissant des preuves à
l'opérateur.

## Signaux

Volume sortant, taux de rejet/deferred, authentification, compte/domain
source, anomalies et réputation/blocklist uniquement si une source
fiable est configurée.

## Actions

Throttle/suspend outbound mail selon policy, investigate account, rotate
credential, open queue/logs.

## Sécurité

Une réputation externe est une observation, pas une preuve unique de
compromission.

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
