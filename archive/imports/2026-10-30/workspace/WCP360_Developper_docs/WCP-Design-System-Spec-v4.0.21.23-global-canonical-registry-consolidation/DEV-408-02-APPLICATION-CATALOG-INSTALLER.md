# V4.0.8 --- Application Catalog & Auto-installer

## Statut

**NORMATIVE --- V4.0.8 APPLICATIONS & DEVELOPER PLATFORM DEEP
SPECIFICATION --- NO CODE**

Cette passe approfondit la plateforme applicative et développeur déjà
décrite dans le corpus : CMS/toolkits, auto-installer, Git/déploiement,
runtimes, dépendances, variables d'environnement, processus, workers,
cron et sécurité supply-chain.

## Invariants

-   Une `Application` WCP est distincte d'un `Website`, d'un `Runtime`,
    d'un `Deployment` et d'un `Repository`.
-   WCP-USER n'obtient jamais root, socket Docker global, systemd
    système arbitraire, commande shell privilégiée ou secret d'un autre
    tenant.
-   Les runtimes et versions disponibles proviennent d'un
    registry/capability certifié.
-   Les secrets applicatifs ne sont jamais injectés dans URL, audit,
    events ou logs.
-   Un déploiement réussi exige vérification de l'état cible ;
    `git pull` ou exit code 0 ne suffit pas.
-   Les installers/toolkits ne contournent jamais ownership, quotas,
    FileRoot, DB, TLS ou backup.
-   Toute exécution de commande est issue d'une opération
    typée/allowlistée ou d'un contexte explicitement autorisé par la
    politique développeur.

## Catalogue

Templates versionnés : application ID, versions supportées, runtime
requirements, DB requirements, install steps typés, health checks,
upgrade/migration notes.

## V1 candidates

WordPress, Nextcloud, Laravel starter, PrestaShop, Magento/OpenSearch
requirements, Ghost et templates Node/Python selon certification réelle.

## Installation

Entitlements → domain/path availability → runtime/DB/storage preflight →
backup/conflict check → staged install → secrets → DB → files → web
binding → health verify → active.

## Sécurité

Pas de script tiers téléchargé/exécuté arbitrairement à chaque
installation. Artifacts/source/version/checksum/signature policy selon
registry.

## Failure

Installation partielle produit Job/reconcile/cleanup ; ne laisse pas
faux état active.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-01-INSTALLER-ARCHITECTURE.md`
- `BOOT-4014-02-SYSTEM-PREFLIGHT.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
