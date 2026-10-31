# V4.0.8 --- Developer Platform RBAC, Jobs, Events & Audit

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

## Permissions

application.*, runtime.*, dependency.*, repository.*, deployment.*,
process.*, worker.*, schedule.*, wordpress.*, secret.*.

## Jobs

app.install/update, runtime.change, dependency.install, git.deploy,
release.rollback, process.restart, worker.scale, schedule.run,
wordpress.clone/update.

## Events

Facts after verification: application.deployed, release.activated,
process.crashloop, dependency.vulnerable, wordpress.updated.

## Audit

Real/effective actor, app/account, commit/release, operation, secret
names not values, result/evidence.

## Locks

Application/release/deployment locks prevent concurrent destructive
deploy/update operations.
