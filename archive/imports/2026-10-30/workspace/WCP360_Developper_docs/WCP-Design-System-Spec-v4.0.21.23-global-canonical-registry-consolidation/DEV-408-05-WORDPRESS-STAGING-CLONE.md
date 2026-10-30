# V4.0.8 --- WordPress Staging, Clone & Push-to-production

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

## Clone

Files + DB + config transformation + URL/domain rewrite via WP-aware
tooling, pas simple string replace aveugle.

## Staging

Environment isolé, noindex/access policy, mail suppression/redirection
policy et separate secrets when required.

## Push

Files-only, DB-only selected tables, full push ou future granular
capability. Production backup obligatoire selon policy.

## Conflicts

Production may change after staging creation. WCP affiche
divergence/freshness ; ne prétend pas merge automatique universel.

## Sensitive

Payments/webhooks/email en staging sont neutralisés ou explicitement
contrôlés.
