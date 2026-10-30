# V4.0.8 --- WordPress Core, Plugin & Theme Updates

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

## Inventory

Core/plugins/themes avec version, source, status, update available et
vulnerability signals.

## Update pipeline

Preflight → recovery point → maintenance policy → update staged/applied
→ WP health/HTTP probe → DB migration observation → commit or rollback.

## Auto-update

Policy par component/risk window. Security auto-update peut être
autorisé mais garde backup/evidence.

## Compatibility

PHP version, WP requirements et plugin/theme compatibility sont des
signaux ; pas de garantie absolue.

## Failure

White screen/HTTP 500/DB error après update déclenche rollback policy
lorsque recovery artifact disponible.

## V4.0.12 normative deepening

- `LIFE-4012-04-UPDATE-CHANNELS.md`
- `LIFE-4012-05-UPDATE-POLICY-SCHEDULING.md`
- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
