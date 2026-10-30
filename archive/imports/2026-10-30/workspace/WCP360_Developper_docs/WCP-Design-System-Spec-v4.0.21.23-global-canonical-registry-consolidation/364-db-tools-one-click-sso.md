# 364 --- One-click DB Tools SSO

## Origine

**\[V3.2\]** Renforce `322-database-web-tools-sso.md`.

## Intégrations

phpMyAdmin pour MySQL/MariaDB, pgAdmin pour PostgreSQL, Adminer lorsque
configuré.

## UX

Depuis une base ou un moteur : `Open phpMyAdmin/pgAdmin/Adminer`. Aucun
formulaire ne redemande le mot de passe si une session SSO/broker sûre
peut être créée.

## Sécurité

Session courte durée, audience limitée à l'outil, scope DB/tenant,
SameSite/CSRF approprié au mécanisme retenu, révocation et audit. Jamais
de credentials DB dans URL, referer, logs ou analytics.

## Contrat WCP

-   **WCP-ADMIN** configure moteurs, catalogues, politiques, capacités
    et limites.
-   **WCP-USER** expose uniquement les fonctions attribuées au
    client/revendeur.
-   Toute mutation sensible est vérifiée côté backend, auditée et
    corrélée à un job/événement.
-   Les fonctionnalités sont pilotées par `capabilities`; aucune
    intégration tierce n'est supposée installée.
-   États obligatoires : loading, empty, denied, unavailable, partial,
    stale, pending, failed, success confirmé.
