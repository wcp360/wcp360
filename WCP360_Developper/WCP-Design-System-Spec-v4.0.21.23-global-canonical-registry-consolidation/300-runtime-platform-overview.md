# 300 --- Application Runtime Platform

## Origine

**\[V3.1 --- exigence ajoutée par le projet\]** Node.js, Python, Ruby et
Go en complément de PHP.

## Objectif

Fournir une abstraction commune de runtime tout en conservant les
différences de chaque écosystème.

## Pages

Runtimes; Applications; Processes; Environment; Dependencies; Logs;
Deployments.

## Modèle application

Owner, domain/path, runtime, version, entrypoint, environment variables
redacted, process model, working directory, deployment source, health.

## Actions

Créer application; changer version avec preflight; restart; stop/start;
deploy; rollback si mécanisme disponible.

## Isolation

Chaque application s'exécute sous le tenant/sandbox défini par le
backend. Aucun runtime ne doit permettre de contourner quotas ou
isolation.

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
