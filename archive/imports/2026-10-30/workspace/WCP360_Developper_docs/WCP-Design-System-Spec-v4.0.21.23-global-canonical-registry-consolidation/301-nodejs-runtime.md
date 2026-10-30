# 301 --- Node.js Runtime

## Origine

**\[V3.1\]** Gestion Node.js.

## Pages

Versions; Applications; Process Manager; Environment; Logs;
Dependencies.

## Données

Node version, package manager disponible, entrypoint, port/socket
interne, process state, app root.

## Actions

Installer/attribuer version selon policy; create app; install
dependencies; start/restart; deploy.

## Processus

Support via adaptateur choisi par l'architecture (systemd user service,
Supervisor ou autre). Ne pas imposer PM2 sans décision explicite.

## Sécurité

Variables d'environnement secrètes masquées; commandes package exécutées
dans le scope du compte.

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

## V4.0.11 normative deepening

- `CLUSTER-4011-01-CLUSTER-NODE-MODEL.md`
- `CLUSTER-4011-02-NODE-ENROLLMENT-TRUST.md`
- `CLUSTER-4011-05-NODE-CORDON-DRAIN.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
