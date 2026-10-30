# 362 --- Runtime GUI Contract (Node/Python/Ruby/Go)

## Origine

**\[V3.2\]** Formalise l'interface GUI commune des managers déjà
présents en V3.1.

## Formulaire Application

Runtime; version; domaine/path; application root; entrypoint (`app.js`,
module WSGI/ASGI, Passenger app, binaire Go selon runtime); working
directory; process count si supporté; environment variables;
startup/restart policy; health check.

## Environment Variables

Table clé/valeur, secret flag, source inherited/local, masked display,
bulk import contrôlé. `.env` peut être importé/édité selon politique
mais n'est jamais copié dans audit/logs.

## Actions

Start, stop, restart, deploy, change runtime version avec preflight,
view logs. Les actions réellement possibles proviennent du runtime
adapter.

## WCP-USER

GUI orientée application; aucun besoin de connaître systemd/Supervisor
sous-jacent sauf diagnostic autorisé.

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
