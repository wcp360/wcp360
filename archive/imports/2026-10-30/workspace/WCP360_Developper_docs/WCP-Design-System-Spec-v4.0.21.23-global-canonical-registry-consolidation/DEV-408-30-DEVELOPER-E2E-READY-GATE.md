# V4.0.8 --- Developer Platform E2E & READY Gate

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

## Scénarios

1.  WordPress one-click install.
2.  Existing WP discovery/adoption.
3.  WP plugin update with failed health and rollback.
4.  WP staging clone/push.
5.  Nextcloud/CMS major upgrade preflight failure.
6.  Node app deploy with npm lockfile.
7.  Python ASGI app with venv.
8.  Ruby bundle/deploy.
9.  Go prebuilt binary deploy.
10. Private Git deploy key + host-key mismatch.
11. Webhook replay rejected.
12. Deployment crash after release switch.
13. DB migration non-reversible warning.
14. Secret rotation + restart.
15. Worker graceful deployment.
16. Cron overlap skip.
17. Build CPU/RAM/pid exhaustion.
18. Attempt to access WCP socket from build.
19. Vulnerable dependency finding/SBOM.
20. App log tenant isolation.
21. Health probe SSRF attempt.
22. Cross-tenant repository/app binding rejected.
23. Disk full during build.
24. EOL runtime policy.
25. Docker socket exposure attempt rejected.

## Gate

Applications & Developer Platform deviennent **SPECIFIED-DEEP** quand
catalog/toolkit/runtimes/deploy/process/security negative tests sont
cohérents. Le projet entier reste non GLOBAL READY.
