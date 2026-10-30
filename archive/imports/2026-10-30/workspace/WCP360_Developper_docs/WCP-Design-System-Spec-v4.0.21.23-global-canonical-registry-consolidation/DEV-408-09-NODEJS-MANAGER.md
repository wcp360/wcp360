# V4.0.8 --- Node.js Application Manager

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

## Application

Node version, package manager, working directory, entrypoint, env refs,
process mode, port/socket binding and health probe.

## Package managers

npm baseline; yarn/pnpm only if certified. Lockfile detection and
immutable/frozen install mode preferred for deployments.

## Start

No privileged port direct bind. App listens on allocated loopback/socket
and Web proxy binding routes traffic.

## Scripts

`package.json` scripts are tenant code. Running arbitrary named scripts
requires developer execution permission/context, resource limits and
audit metadata.

## Production

`NODE_ENV`, dependency install policy, restart and graceful stop.

## V4.0.11 normative deepening

- `CLUSTER-4011-01-CLUSTER-NODE-MODEL.md`
- `CLUSTER-4011-02-NODE-ENROLLMENT-TRUST.md`
- `CLUSTER-4011-05-NODE-CORDON-DRAIN.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
