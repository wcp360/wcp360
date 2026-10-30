# 374 --- Mail Domain Bootstrap: DKIM / SPF / DMARC

## Origine

**\[V3.2\]** Génération automatique lors de la création d'un domaine
mail.

## Workflow

Create mail domain → generate DKIM key securely → construct SPF
recommendation/effective record → create DMARC policy according to
selected template → publish locally if authoritative DNS is managed by
WCP, sinon fournir instructions → validate.

## Important

SPF/DMARC ne doivent pas écraser aveuglément des enregistrements
existants. WCP analyse et propose un merge/choix explicite.

## DKIM

Private key stockée côté serveur; UI expose selector, public DNS value,
fingerprint/status, rotation workflow.

## Validation

Expected vs observed DNS, timestamp et propagation state.

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
