# WCP --- Module Capability Catalog

## Statut

**NORMATIVE ADDENDUM --- V4.0.21.1.1 --- NO CODE --- REPOSITORY
BLOCKED**

## Objet

Cette passe ferme la conception fonctionnelle et de sécurité de la
gestion des plans, privilèges fonctionnels, modules, overrides,
revendeurs, comptes DEMO et TRIAL. Elle ne remplace pas RBAC : elle
ajoute la couche d'entitlements/features nécessaire à un panneau
d'hébergement comparable aux mécanismes de packages/feature lists des
panels professionnels.

## Principe fondamental

L'accès effectif à une action est accordé uniquement si **toutes** les
couches nécessaires l'autorisent :
`RBAC capability ∩ resolved scope ∩ module availability ∩ effective entitlement ∩ account mode ∩ resource state ∩ security policy`.

## États d'exposition

`ENABLED`, `DISABLED`, `READ_ONLY`, `SIMULATED`, `HIDDEN`.

  ----------------------------------------------------------------------------------------------------------------------------------------
  Module ID                   Module                    Plan       DEMO read     DEMO          Limit key                   Family
                                                        default                  interactive                               
                                                        example                                                            
  --------------------------- ------------------------- ---------- ------------- ------------- --------------------------- ---------------
  `web.sites`                 `Websites`                `ON`       `READ_ONLY`   `SIMULATED`   `sites.max`                 `web`

  `web.file_manager`          `File Manager`            `ON`       `READ_ONLY`   `SIMULATED`   `storage.bytes`             `web`

  `web.sftp`                  `SFTP`                    `ON`       `HIDDEN`      `DISABLED`    `sftp.accounts.max`         `web`

  `web.ssh`                   `SSH`                     `OFF`      `HIDDEN`      `DISABLED`    `ssh.sessions.max`          `web`

  `web.custom_nginx`          `Custom Nginx`            `OFF`      `READ_ONLY`   `SIMULATED`   `none`                      `web`

  `web.git`                   `Git Deploy`              `ON`       `READ_ONLY`   `SIMULATED`   `git.repos.max`             `developer`

  `runtime.php`               `PHP`                     `ON`       `READ_ONLY`   `SIMULATED`   `php.pools.max`             `runtime`

  `runtime.node`              `Node.js`                 `OFF`      `READ_ONLY`   `SIMULATED`   `node.apps.max`             `runtime`

  `runtime.python`            `Python`                  `OFF`      `READ_ONLY`   `SIMULATED`   `python.apps.max`           `runtime`

  `runtime.ruby`              `Ruby`                    `OFF`      `READ_ONLY`   `SIMULATED`   `ruby.apps.max`             `runtime`

  `runtime.go`                `Go Apps`                 `OFF`      `READ_ONLY`   `SIMULATED`   `go.apps.max`               `runtime`

  `cms.wordpress`             `WordPress Toolkit`       `ON`       `READ_ONLY`   `SIMULATED`   `wordpress.instances.max`   `application`

  `cms.installer`             `Application Installer`   `ON`       `READ_ONLY`   `SIMULATED`   `apps.max`                  `application`

  `dns.manage`                `DNS`                     `ON`       `READ_ONLY`   `SIMULATED`   `dns.zones.max`             `dns`

  `tls.manage`                `TLS / Let's Encrypt`     `ON`       `READ_ONLY`   `SIMULATED`   `certificates.max`          `security`

  `database.mariadb`          `MariaDB`                 `ON`       `READ_ONLY`   `SIMULATED`   `databases.max`             `database`

  `database.postgresql`       `PostgreSQL`              `OFF`      `READ_ONLY`   `SIMULATED`   `postgres.databases.max`    `database`

  `cache.redis`               `Redis`                   `OFF`      `READ_ONLY`   `SIMULATED`   `redis.instances.max`       `database`

  `cache.memcached`           `Memcached`               `OFF`      `READ_ONLY`   `SIMULATED`   `memcached.instances.max`   `database`

  `database.phpmyadmin_sso`   `phpMyAdmin SSO`          `ON`       `HIDDEN`      `SIMULATED`   `none`                      `database`

  `mail.domain`               `Mail Domains`            `ON`       `READ_ONLY`   `SIMULATED`   `mail.domains.max`          `mail`

  `mail.mailbox`              `Mailboxes`               `ON`       `READ_ONLY`   `SIMULATED`   `mailboxes.max`             `mail`

  `mail.alias`                `Aliases`                 `ON`       `READ_ONLY`   `SIMULATED`   `mail.aliases.max`          `mail`

  `mail.webmail`              `Webmail`                 `ON`       `HIDDEN`      `SIMULATED`   `none`                      `mail`

  `mail.spam`                 `Anti-spam`               `ON`       `READ_ONLY`   `SIMULATED`   `none`                      `mail`

  `mail.sieve`                `Sieve`                   `OFF`      `READ_ONLY`   `SIMULATED`   `none`                      `mail`

  `backup.manage`             `Backups`                 `ON`       `READ_ONLY`   `SIMULATED`   `backup.retained.max`       `backup`

  `cron.manage`               `Cron`                    `ON`       `READ_ONLY`   `SIMULATED`   `cron.jobs.max`             `developer`

  `security.malware_scan`     `Malware Scan`            `ON`       `READ_ONLY`   `SIMULATED`   `scan.frequency.max`        `security`

  `security.advanced`         `Advanced Security`       `OFF`      `READ_ONLY`   `SIMULATED`   `none`                      `security`

  `api.tokens`                `API Tokens`              `ON`       `HIDDEN`      `DISABLED`    `api.tokens.max`            `developer`

  `integration.webhooks`      `Webhooks`                `OFF`      `READ_ONLY`   `SIMULATED`   `webhooks.max`              `developer`
  ----------------------------------------------------------------------------------------------------------------------------------------

## Contrat d'un module

Chaque module enregistré doit déclarer : ID stable, dépendances système,
health prerequisites, entitlements, limit keys, capabilities RBAC,
opérations API, Jobs éventuels, compatibilité DEMO/TRIAL, règles de
désactivation, données conservées et tests d'acceptation.

`SIMULATED` interdit toute mutation de l'OS, des providers et de `wcpd`.
