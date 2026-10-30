# 320 --- Redis Manager

## Origine

**\[V3.1\]** Cache mémoire Redis.

## Pages

Instances; Assignments; ACL/Auth; Memory; Persistence; Clients; Logs.

## Données

Instance, version, state, bind/socket logical ref, memory limit/usage,
owner, database/ACL model selon version.

## Actions

Create/assign instance si architecture multi-instance; restart; flush
avec confirmation forte; rotate credential; change limits.

## Sécurité

Pas d'exposition publique par défaut; credentials redacted; tenant
isolation; `FLUSH*` traité comme destructif.

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
