# V4.0.8 --- Application Logs & Developer Observability

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

## Sources

Web access/error refs, application stdout/stderr, process manager,
framework logs and deployment logs.

## Scope

Only application/account-owned streams. No arbitrary `/var/log` path
tenant input.

## Streaming

Bounded tail/follow with backpressure, reconnect and rate limits.

## Redaction

Known secret patterns/headers filtered where feasible; application may
still log secrets, surfaced as security risk.

## Retention

Per source policy and quotas; log rotation remains System/logging
contract.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
