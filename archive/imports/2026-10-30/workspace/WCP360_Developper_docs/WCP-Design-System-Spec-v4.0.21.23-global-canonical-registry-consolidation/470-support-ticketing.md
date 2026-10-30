# 470 --- Support / Ticketing

## Origine

Le ticketing/support était évoqué dans l'architecture initiale mais pas
suffisamment détaillé.

## Pages WCP-USER

Tickets; New Ticket; Conversation; Attachments; Related resources.

## Pages WCP-ADMIN

Queues; Assignment; Status/Priority; SLA fields if product decides;
Internal notes; Resource links.

## Sécurité

Attachments scanned/validated ; internal notes never exposed to user ;
tenant isolation.

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
