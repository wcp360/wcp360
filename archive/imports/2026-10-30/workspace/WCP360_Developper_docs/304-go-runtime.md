# 304 --- Go Runtime

## Origine

**\[V3.1\]** Gestion Go.

## Pages

Versions; Applications; Build; Processes; Logs.

## Modèle

Source/deployment, Go version, build command structuré/policy, binary
artifact, service process, environment.

## Actions

Build; deploy artifact; restart; rollback si releases conservées.

## Sécurité

Build exécuté avec quotas, timeout et isolation; pas de commande root
arbitraire.

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
