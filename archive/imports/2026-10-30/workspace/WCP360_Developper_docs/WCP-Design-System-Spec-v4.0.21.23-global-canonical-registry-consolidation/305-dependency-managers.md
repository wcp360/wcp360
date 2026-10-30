# 305 --- Dependency Managers

## Origine

**\[V3.1\]** Composer, npm/yarn et pip intégrés.

## Objectif

Unifier l'expérience sans masquer les différences de lockfiles et
environnements.

## Pages

Project Dependencies; Install/Update Job; Security/Outdated results si
une source fiable est disponible; History.

## Managers

Composer pour PHP; npm/yarn selon projet Node; pip dans environnement
Python. D'autres managers nécessitent capability explicite.

## Actions

Install depuis lockfile; update uniquement avec confirmation d'impact;
run approved scripts selon policy.

## Sécurité

Timeout, quotas, réseau sortant selon politique, redaction des registry
credentials, exécution sous compte non-root.

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
