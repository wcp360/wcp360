# V4.0.8 --- Application Canonical Resource Model

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

## Ressources

`Application`, `ApplicationBinding`, `RuntimeBinding`,
`RepositoryBinding`, `Deployment`, `EnvironmentVariable`,
`ProcessDefinition`, `WorkerDefinition`, `ScheduledTask`,
`ApplicationRelease`.

## Application

Type/framework, account, FileRoot, website binding, runtime binding,
environment, lifecycle, health et release active.

## Séparation

Website gère HTTP/vhost/TLS ; Application gère code/runtime/processus ;
Database reste ressource DB ; Repository reste source de code.

## Etats

provisioning, active, deploying, degraded, stopped, suspended, deleting,
deleted, unknown.

## Ownership

Une application appartient à un HostingAccount ; aucun binding
cross-tenant.
