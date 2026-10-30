# V4.0.8 --- Application Health & Readiness

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

## Signals

Process state, listener/socket, HTTP probe, dependency probes where
safe, framework-specific health and deployment freshness.

## Status

healthy, degraded, unhealthy, stopped, unknown with causes.

## Probe

Method/path/expected status/timeouts controlled; prevent SSRF by
restricting target to app bindings.

## Startup

Grace periods and failure thresholds avoid restart loops.

## Dependency

DB/cache unavailable may degrade app even if process remains running.

## V4.0.10 normative deepening

- `OPS-4010-02-HEALTH-STATE-MODEL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
