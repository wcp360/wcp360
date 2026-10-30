# 375 --- Sieve Filters, Vacation & Forwarding

## Origine

**\[V3.2\]** Filtres serveur, répondeurs automatiques et redirections.

## Pages WCP-USER

Filters; Vacation; Forwarders; Rules order; Test/preview si backend le
supporte.

## Sieve

Rule builder structuré : conditions sender/recipient/subject/header/size
selon moteur, actions fileinto, redirect, reject/discard uniquement si
policy, vacation, stop.

## Vacation

Start/end, subject, message, frequency/duplicate suppression selon Sieve
implementation.

## Forwarding

Destination validation; loop detection lorsque possible; external
forwarding warning/policy.

## Sécurité

Pas d'éditeur de script Sieve brut par défaut; mode avancé seulement
avec validation backend.

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

## V4.0.6 normative deepening

- `MAIL-406-08-SIEVE-VACATION-FILTERS.md`

Ces contrats approfondissent cette surface sans remplacer les décisions antérieures compatibles.
