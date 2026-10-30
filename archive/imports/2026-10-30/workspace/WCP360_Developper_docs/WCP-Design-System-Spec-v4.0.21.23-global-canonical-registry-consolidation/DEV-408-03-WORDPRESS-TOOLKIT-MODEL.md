# V4.0.8 --- WordPress Toolkit --- Canonical Model

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

## Instance

`WordPressInstance` référence Application, Website, FileRoot, Database,
WP core version, environment et management state.

## Discovery

Détection d'instances existantes sans prendre automatiquement ownership.
Validation wp-config/path/database/URL avant adoption.

## Actions

install, update core/plugin/theme, activate/deactivate, staging/clone,
backup-before-change, security hardening, maintenance mode et health.

## Boundary

WCP ne modifie pas directement une instance non adoptée/managed sans
confirmation.

## Multisite

Capability distincte avec règles spécifiques ; ne pas traiter multisite
comme simple single-site.
