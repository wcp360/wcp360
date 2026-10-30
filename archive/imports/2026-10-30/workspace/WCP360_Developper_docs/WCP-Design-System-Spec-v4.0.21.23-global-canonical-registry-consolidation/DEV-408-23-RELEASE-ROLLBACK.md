# V4.0.8 --- Application Releases & Rollback

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

## Release

Immutable-ish application release metadata: commit/artifact,
dependencies/build refs, created time, deploy result and health.

## Retention

Keep N/age releases subject to disk quota; active/previous protected
from cleanup.

## Rollback

Switch application files/processes to selected compatible release then
health verify.

## Data

Database/schema/user-generated files may not roll back with code. UI
must expose this boundary.

## WordPress/CMS

Toolkit restore may require coordinated files+DB recovery instead of
generic release rollback.

## V4.0.12 normative deepening

- `LIFE-4012-01-PLATFORM-RELEASE-MODEL.md`
- `LIFE-4012-02-RELEASE-MANIFEST-ARTIFACTS.md`
- `LIFE-4012-16-ROLLBACK-RECOVERY-BOUNDARY.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
