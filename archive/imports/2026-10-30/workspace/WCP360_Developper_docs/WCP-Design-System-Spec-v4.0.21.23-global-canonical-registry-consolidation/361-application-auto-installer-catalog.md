# 361 --- Application Auto-installer & Catalog

## Origine

**\[V3.2\]** Auto-installeur interne/open-source comparable
fonctionnellement à un catalogue d'applications.

## Catalogue

WordPress, Nextcloud, Laravel, PrestaShop, Magento, Ghost et templates
Node.js peuvent être proposés **si un manifeste maintenu existe**. Le
catalogue n'affiche pas une application non supportée.

## Manifeste d'application

ID/version, source vérifiée, checksum/signature si disponible, prérequis
PHP/runtime/extensions/DB, fichiers, permissions, install steps, health
check, update strategy, uninstall strategy, variables et secrets.

## Workflow

Select app → compatibility/preflight → domain/path → DB/runtime →
configuration → install job → health check → credentials result → audit.

## Sécurité supply-chain

Sources et checksums gouvernés; pas de téléchargement arbitraire depuis
une URL saisie par le tenant dans le flux 1-click. Les scripts
d'installation sont versionnés et revus.

## Updates

Une application n'est mise à jour automatiquement que si son manifeste
définit une procédure et une politique d'update compatible.

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

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-01-INSTALLER-ARCHITECTURE.md`
- `BOOT-4014-02-SYSTEM-PREFLIGHT.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
