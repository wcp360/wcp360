# V4.0.8 --- Build Isolation, Resource & Network Controls

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

## Threat

Tenant build scripts execute arbitrary tenant code. They must never
inherit control-plane privileges.

## Identity

Dedicated tenant/app UID context; filesystem scope; no root; no WCP
sockets/secrets.

## Resources

CPU, RAM, pids, I/O, duration, temp space and output limits using System
isolation capabilities.

## Network

Policy may allow package registries while restricting metadata/internal
control-plane destinations; exact enforcement capability-dependent.

## Cleanup

Ephemeral build workspace/temp secrets removed after run.

## Abuse

Fork bombs, disk fill, crypto-mining-like sustained load and network
scanning included in tests.
