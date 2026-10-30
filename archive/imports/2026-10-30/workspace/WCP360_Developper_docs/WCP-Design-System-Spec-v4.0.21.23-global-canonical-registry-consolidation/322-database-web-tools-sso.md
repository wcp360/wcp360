# 322 --- Database Web Tools & SSO

## Origine

**\[V3.1\]** Intégration/SSO vers phpMyAdmin, pgAdmin ou Adminer.

## Objectif

Ouvrir un outil DB autorisé sans exposer durablement le mot de passe de
la base.

## Pages

Tools; Availability; SSO Sessions; Policy.

## Workflow SSO

Utilisateur authentifié WCP → contrôle de permission → jeton/session
courte durée ou mécanisme broker retenu → outil DB →
expiration/révocation.

## Sécurité

Pas de mot de passe DB dans l'URL; tokens courts; CSRF/session
isolation; scope base/tenant; audit du lancement SSO.

## Capabilities

phpMyAdmin, pgAdmin et Adminer sont des intégrations séparées et ne sont
affichées que si installées/configurées.

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
