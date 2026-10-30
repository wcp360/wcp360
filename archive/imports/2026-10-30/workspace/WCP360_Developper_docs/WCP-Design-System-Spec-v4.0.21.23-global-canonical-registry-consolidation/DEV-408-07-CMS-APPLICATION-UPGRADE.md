# V4.0.8 --- CMS / Framework Upgrade Contract

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

## Scope

Nextcloud, PrestaShop, Magento, Ghost et autres catalog apps ont des
upgrade adapters spécifiques.

## Pipeline

Compatibility matrix → backup → maintenance mode if needed →
artifact/dependency update → migrations → health probes →
commit/rollback boundary.

## Major versions

Toujours workflow distinct d'un patch/minor update.

## Requirements

PHP/runtime, DB, extensions, search/cache dependencies et disk headroom
vérifiés.

## No generic lie

Un bouton « Update all apps » ne doit pas masquer des workflows
incompatibles entre produits.

## V4.0.12 normative deepening

- `LIFE-4012-06-SINGLE-NODE-UPGRADE.md`
- `LIFE-4012-07-CLUSTER-ROLLING-UPGRADE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
