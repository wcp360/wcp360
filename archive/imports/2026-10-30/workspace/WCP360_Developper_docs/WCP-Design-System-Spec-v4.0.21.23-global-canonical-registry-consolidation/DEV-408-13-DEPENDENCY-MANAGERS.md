# V4.0.8 --- Composer, npm/yarn/pnpm, pip & Bundler

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

## Principle

Dependency manager execution is application-scoped code execution and
supply-chain surface.

## Context

Run as tenant/app identity, inside FileRoot/workspace, with
CPU/RAM/time/network constraints.

## Lockfiles

Prefer deterministic locked installs for deployment. Missing/stale
lockfile surfaced.

## Credentials

Private registry tokens are SecretReferences and injected ephemerally.

## Output

Logs redacted and size-limited. Dependency inventory feeds SBOM/security
findings.

## Prohibited

No global/system package install from WCP-USER dependency manager.
