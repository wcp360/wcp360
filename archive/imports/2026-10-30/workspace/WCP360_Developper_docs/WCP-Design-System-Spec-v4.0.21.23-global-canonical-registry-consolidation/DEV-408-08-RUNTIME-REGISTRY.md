# V4.0.8 --- Runtime Registry & Version Lifecycle

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

## Registry

Runtime family, version, provider/source, install state, EOL/security
state, supported execution modes, extensions/modules and constraints.

## Families

PHP reste Web contract ; cette passe approfondit Node.js, Python, Ruby
et Go.

## Selection

Plan/account/app entitlement + installed/certified versions. Une version
EOL peut être blocked/warned selon policy.

## Coexistence

Multi-version support doit être explicite par runtime/backend ; ne pas
promettre si OS/package strategy ne le permet pas.

## Upgrade

Changing runtime version is an application deployment/change requiring
preflight and health verify.
