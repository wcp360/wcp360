# V4.0.21.10 --- Existing Corpus Reconciliation: Files / Access / Apps / Runtimes

## Statut

**DEEP COMPLETENESS PASS --- NO CODE**

Cette passe ne remplace pas les familles déjà détaillées. Elle ferme les
interfaces entre les documents existants, notamment :
`820-file-manager-resource-model.md`,
`821-file-manager-api-security.md`,
`822-file-editor-archive-contract.md`,
`SECOPS-407-02-FILE-ENTRY-OPERATIONS.md`,
`SECOPS-407-04-FILE-EDITOR-MONACO.md`,
`SECOPS-407-06-ARCHIVE-CREATE-EXTRACT.md`,
`SECOPS-407-09-SFTP-FTP-ACCOUNT-DEEP.md`,
`SECOPS-407-10-SSH-USER-KEYS-SHELL.md`,
`DEV-408-01-APPLICATION-CANONICAL-MODEL.md`,
`DEV-408-03-WORDPRESS-TOOLKIT-MODEL.md`,
`DEV-408-08-RUNTIME-REGISTRY.md`, `DEV-408-09-NODEJS-MANAGER.md`,
`DEV-408-10-PYTHON-MANAGER.md`, `DEV-408-11-RUBY-PASSENGER-MANAGER.md`,
`DEV-408-12-GO-APPLICATION-MANAGER.md`,
`DEV-408-17-SCHEDULED-TASKS-CRON.md`,
`DEV-408-18-GIT-REPOSITORY-MODEL.md`,
`DEV-408-19-GIT-DEPLOYMENT-PIPELINE.md`,
`DEV-408-20-WEBHOOK-DEPLOY-TRIGGERS.md`,
`DEV-408-22-DEPLOYMENT-HOOKS-MIGRATIONS.md`.

## Fermetures recherchées

-   une taxonomie de chemins et racines commune à File Manager, SFTP,
    Git et runtimes ;
-   un modèle unique de credentials/secrets ;
-   un modèle de déploiement atomique commun ;
-   séparation stricte build-time / runtime ;
-   limites ressources reliées aux IsolationProfiles ;
-   staging/cloning WordPress sans fuite de secrets ou de données ;
-   hooks Git/webhooks sans shell arbitraire ;
-   cron/workers rattachés à l'identité Unix et au cgroup du compte ;
-   parité UI/API/CLI.
