# 303 --- Ruby / Passenger Runtime

## Origine

**\[V3.1\]** Ruby avec Passenger lorsque disponible.

## Pages

Ruby Versions; Applications; Gems; Passenger; Logs.

## Actions

Choisir runtime; configurer application; installer dépendances
autorisées; restart Passenger app; deploy.

## Capabilities

Le module Passenger n'apparaît que si Apache/Nginx et l'intégration
Passenger correspondante sont réellement installés.

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
