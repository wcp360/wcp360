# 401 --- Mailing Lists

## Origine

La source initiale mentionnait les mailing lists ; la V3.2 ne les
détaillait pas suffisamment.

## Pages

Lists; Members; Moderation; Policies; Archives link si backend le
supporte.

## Capabilities

Le moteur de mailing list est un adaptateur ; aucune technologie
spécifique n'est obligatoire.

## Sécurité

Permissions d'administration séparées, anti-abuse/rate limits, données
membres protégées.

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

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
