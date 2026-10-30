# V4.0.8 --- Git Deployment Pipeline

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

## Pipeline

Fetch → resolve immutable commit → preflight → optional backup →
checkout/release stage → dependency install → build → migrations/hooks →
atomic/current switch → process reload → health verify → commit.

## Release

Deployment records commit SHA, release ID, artifact hashes where
available and previous release.

## Atomicity

Prefer release directories + current pointer/symlink only under safe
filesystem contract. In-place pull is lower-safety mode and clearly
marked.

## Failure

Before switch: discard stage. After switch: rollback previous release if
compatible; DB migrations may limit rollback.

## No false success

Fetch/pull success ≠ deployment success.
