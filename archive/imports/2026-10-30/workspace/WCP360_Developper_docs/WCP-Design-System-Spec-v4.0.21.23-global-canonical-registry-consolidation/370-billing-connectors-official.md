# 370 --- Official Billing / ERP Connectors

## Origine

**\[V3.2\]** Connecteurs officiels WHMCS, Blesta, FossBilling et
HostBill.

## Relation avec V3.1

Complète `340-billing-provisioning-integrations.md` et ajoute
explicitement **FossBilling**.

## Connecteur

Version, compatibility matrix, API credentials/scopes, endpoint, package
mapping, lifecycle mapping, webhook secret, retries, last sync.

## Lifecycle

Create/provision; suspend for billing state; unsuspend; change plan;
terminate; password/service credential operation selon politique;
usage/status query.

## Fiabilité

Idempotency keys, retries avec backoff, dead-letter/failure view, replay
contrôlé, correlation ID.

## Responsabilité

Le billing décide de l'état commercial; WCP applique uniquement les
actions de provisioning autorisées et traçables.

## Contrat WCP

-   **WCP-ADMIN** configure moteurs, catalogues, politiques, capacités
    et limites.
-   **WCP-USER** expose uniquement les fonctions attribuées au
    client/revendeur.
-   Toute mutation sensible est vérifiée côté backend, auditée et
    corrélée à un job/événement.
-   Les fonctionnalités sont pilotées par `capabilities`; aucune
    intégration tierce n'est supposée installée.
-   États obligatoires : loading, empty, denied, unavailable, partial,
    stale, pending, failed, success confirmé.

## V4.0.9 normative deepening

- `API-409-13-BILLING-INTEGRATION-MODEL.md`
- `API-409-14-WHMCS-ADAPTER-CONTRACT.md`
- `API-409-15-BLESTA-FOSSBILLING-HOSTBILL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
