# 380 --- Domain & Registrar Manager

## Origine

**\[V3.3 --- Completeness Pass\]** Extension proposée lors de l'audit ;
la V3.2 couvrait DNS mais pas le cycle de vie registrar.

## Objectif

Unifier domaines hébergés et, lorsque des connecteurs registrar
existent, enregistrement, transfert, renouvellement et verrouillage.

## Pages

Domains; Registrar Connections; Registrations; Transfers; Renewals;
Contacts/Privacy metadata selon provider; Nameservers; Events.

## Actions

Register, transfer-in/out, renew, registrar lock/unlock, update
nameservers, sync status. Toute action n'apparaît que si le provider la
supporte.

## Sécurité

Credentials registrar write-only; opérations de transfert/lock à
confirmation renforcée ; aucun contact personnel n'est exposé au-delà
des permissions nécessaires.

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
