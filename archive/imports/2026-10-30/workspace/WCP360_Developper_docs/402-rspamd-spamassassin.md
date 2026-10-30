# 402 --- Rspamd / SpamAssassin Anti-spam Engines

## Objectif

Formaliser les moteurs antispam comme adaptateurs derrière les
politiques de `156-mail-antispam-relay.md`.

## Pages

Engine status; Scores/Policies; Rules; Allow/Deny; Training/learning si
supporté; Logs.

## Règle

Rspamd et SpamAssassin conservent leurs capacités propres ; l'UI commune
n'invente pas une fonctionnalité absente d'un moteur.

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
