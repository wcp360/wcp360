# V4.0.8 --- Applications & Developer Platform Closure Audit

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

## Résultat

30 nouveaux contrats normatifs ont été ajoutés. **1 documents
historiques correspondant exactement au corpus** ont été reliés.

## Couverture

Application model/catalog, WordPress Toolkit/update/staging/hardening,
CMS upgrades, runtime registry, Node/Python/Ruby/Go,
Composer/npm/pip/Bundler, environment/secrets, process supervision,
workers, cron, Git repository/deploy/webhooks, CI/CD boundary,
hooks/migrations, releases/rollback, logs/health, SBOM/supply-chain,
build isolation et container boundary.

## Sécurité fermée

Pas de root build, pas de Docker root socket, pas de private key
utilisateur demandée, Git host verification, webhook signatures/replay
protection, dependency execution tenant-scoped, secrets
write-only/reference-based, health verification après deploy et
cross-tenant negative tests.

## Décisions restantes

Versions/outils exacts par OS, choix final process adapters par runtime,
catalogue V1 réellement certifié, politique réseau des builds, providers
advisory/SBOM et éventuel sous-système OCI complet restent à figer par
ADR/matrice de compatibilité avant implémentation.
