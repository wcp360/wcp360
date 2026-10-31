# V4.0.8 --- Environment Variables & Application Secrets

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

## Types

Plain configuration and secret values are distinct resources/flags.

## Secrets

Write-only after creation where possible; references injected at
runtime/deploy without returning plaintext.

## `.env`

Can be imported/exported only under explicit permission/policy. File
remains sensitive and File Manager preview/editor may restrict it.

## Scope

Application/environment-specific; no cross-app secret inheritance unless
explicit secret sharing feature exists.

## Rotation

Versioned secret reference, deployment/restart impact and verification.

## Audit

Variable names may be logged; secret values never.
