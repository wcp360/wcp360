# 341 --- Provisioning API & Account Lifecycle

## Origine

**\[V3.1\]** Création/suspension automatique des comptes.

## Cycle de vie

Requested → provisioning → active → suspended → terminating →
terminated, avec failed/retry states.

## Opérations

Create account; assign package; change limits; suspend/unsuspend;
terminate; query usage; rotate service credentials selon politique.

## Préconditions

Package valide, capacité disponible, domaine/identifiant unique, node
placement si multi-node, quotas cohérents.

## Résultat

Toute opération renvoie ressource ou job traçable. Les retries ne
doivent pas créer de doublons.

## Audit

Actor externe identifié par integration/client ID, correlation ID et
payload sensible redacted.

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
