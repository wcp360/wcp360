# 400 --- Mail Aliases, Forwarders & Catch-all

## Objectif

Compléter les fonctions mail utilisateur et admin.

## Pages

Aliases; Forwarders; Catch-all policy; Domain routing summary.

## Actions

Create/edit/delete alias/forwarder ; enable catch-all uniquement si
policy ; validation de boucle et destination.

## Sécurité

Avertir sur forwarding externe ; anti-loop ; restrictions par plan ;
catch-all désactivable globalement par WCP-ADMIN.

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

## V4.0.6 normative deepening

- `MAIL-406-07-ALIAS-FORWARD-CATCHALL.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
