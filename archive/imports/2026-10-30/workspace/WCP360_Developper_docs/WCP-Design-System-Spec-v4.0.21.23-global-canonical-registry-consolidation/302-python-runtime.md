# 302 --- Python WSGI / ASGI Runtime

## Origine

**\[V3.1\]** Python, virtualenv, WSGI/ASGI.

## Pages

Versions; Virtual Environments; Applications; Dependencies; Processes;
Logs.

## Données

Python version, venv, application type WSGI/ASGI, entrypoint,
process/backend, app root.

## Actions

Créer venv; choisir version; installer dépendances; bind application;
restart; deploy.

## Dépendances

pip/requirements et autres formats ne sont activés que selon capacités
retenues.

## Sécurité

Aucune installation système globale depuis WCP-USER; opérations
exécutées dans le contexte du tenant.

## États UI obligatoires

Loading, skeleton, empty, no-results, permission denied, partial data,
stale data, unavailable, maintenance, pending, failed et success
confirmé par le backend.

## Règles transversales

-   WCP-ADMIN administre la capacité globale, les services, politiques
    et limites.
-   WCP-USER n'accède qu'aux ressources et fonctions explicitement
    attribuées à son compte/revendeur.
-   Toute mutation sensible est autorisée côté backend, auditée et
    reliée à un correlation/job ID.
-   Toute opération longue ou massive passe par Task Center.
-   Les secrets ne sont jamais écrits dans les logs, notifications,
    audit ou sorties IA.
-   Les modules sont pilotés par capabilities : une technologie non
    installée n'est pas présentée comme disponible.
