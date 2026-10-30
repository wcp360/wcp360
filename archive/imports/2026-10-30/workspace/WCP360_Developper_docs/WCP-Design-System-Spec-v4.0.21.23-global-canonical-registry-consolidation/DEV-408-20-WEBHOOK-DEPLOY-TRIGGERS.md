# V4.0.8 --- Git Webhooks & Deployment Triggers

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

## Endpoint

Random/non-guessable identifier plus provider signature
verification/secret; TLS required.

## Validation

Provider event type, repository identity, branch/ref and delivery ID.

## Replay

Deduplicate delivery/event IDs within retention window.

## Trigger

Webhook queues Deployment Job; it never runs privileged shell inline.

## Abuse

Rate limits, body size, timeout and secret rotation.

## Manual/API

Manual and API deploy share same pipeline/authorization, differing only
trigger metadata.

## V4.0.9 normative deepening

- `API-409-09-WEBHOOK-OUTBOUND-MODEL.md`
- `API-409-10-WEBHOOK-SIGNATURE-REPLAY.md`
- `API-409-11-WEBHOOK-DELIVERY-RETRY-DLQ.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
