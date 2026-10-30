# 310 --- LiteSpeed / OpenLiteSpeed Manager

## Origine

**\[V3.1\]** Serveurs web alternatifs LiteSpeed / OpenLiteSpeed.

## Objectif

Les intégrer comme moteurs distincts d'Apache/Nginx, via
capabilities/adaptateurs.

## Pages

Overview; Virtual Hosts; Listeners; PHP/LSAPI linkage; Cache; TLS;
Configuration; Logs.

## Actions

Validate; reload/restart; gérer vhosts/listeners; politiques cache
supportées.

## WordPress Cache

L'intégration LSCache n'est proposée que si le backend/moteur
correspondant est présent. WCP ne doit pas simuler une compatibilité
universelle.

## Licence

Les différences de capacités entre LiteSpeed Enterprise et OpenLiteSpeed
doivent être exposées par l'adaptateur et non codées dans le design.

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
