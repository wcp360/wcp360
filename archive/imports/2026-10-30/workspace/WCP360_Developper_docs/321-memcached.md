# 321 --- Memcached Manager

## Origine

**\[V3.1\]** Cache mémoire Memcached.

## Pages

Instances; Assignments; Limits; Stats; Service.

## Données

Version, state, memory allocation/usage, connections, owner/scope.

## Actions

Create/assign selon architecture; restart; change limits; flush avec
confirmation.

## Sécurité

Isolation réseau/socket et quotas; ne pas présenter un modèle d'ACL
Redis à Memcached s'il n'existe pas dans le backend retenu.

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
