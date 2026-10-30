# 381 --- WHOIS / RDAP Domain Intelligence

## Objectif

Afficher des informations de registre/registrar horodatées sans les
confondre avec l'état local WCP.

## Pages

Lookup; Domain Evidence; Expiration/Status; Nameserver observations.

## Règles

Préférer RDAP lorsque disponible ; WHOIS est un fallback/adaptateur. Les
données externes sont marquées `observed`, avec source et timestamp.

## Usage

Diagnostic d'expiration, statut de transfert et cohérence nameservers.
Pas de décision automatique destructive basée sur une seule observation
externe.

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
