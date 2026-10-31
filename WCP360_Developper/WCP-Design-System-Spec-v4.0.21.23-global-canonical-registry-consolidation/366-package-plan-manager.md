# 366 --- Package / Hosting Plan Manager

## Origine

**\[V3.2\]** Plans/quota stricts pour hébergeurs.

## Pages

Plans; Create/Edit; Features; Quotas; Assignments; Overrides; Usage;
Migration Impact.

## Quotas

Disk, inodes, bandwidth, domains/subdomains, databases, DB users,
mailboxes, aliases, FTP/SFTP accounts, cron jobs, backups, applications,
CPU/RAM/I/O/IOPS/process limits lorsque le backend d'isolation les
supporte.

## Features

PHP/runtime versions autorisées, Git, staging, SSH, DB tools,
Redis/Memcached, backups, mail features, app catalog, API access selon
capability.

## Héritage

Global policy → Plan → Reseller allowance → Account override. Afficher
valeur effective et origine.

## Changement de plan

Preflight compare consommation réelle au nouveau plan. Un downgrade
incompatible ne s'applique pas silencieusement.

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

## V4.0.4 normative deepening

Les contrats approfondis suivants s'appliquent :

- `HOSTING-404-03-PLAN-ENTITLEMENT-QUOTA-DEEP.md`
- `HOSTING-404-04-USAGE-METERING-BANDWIDTH.md`

En cas d'ambiguïté, la V4.0.4 précise la sémantique sans annuler les exigences de sécurité antérieures.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
