# V4.0.21 --- Exact Job → RPC → Adapter Registry

## Statut

**NORMATIVE --- V4.0.21 DETERMINISTIC CONTRACT CLOSURE --- NO CODE**

## Mandat

Cette passe ferme exclusivement les 12 blockers P0 de
`AUDIT-V4020-02-P0-BLOCKERS.md`. Aucune capability produit n'est
ajoutée. Les identifiants ci-dessous sont des contrats de conception non
exécutables.

## Règle

Aucun `CRUD`, wildcard `*`, `when needed`, `policy based`,
`according to adapter`, `if large` ou `N/A` non justifié n'est autorisé
dans un registry normatif final.

  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Job ID                           Resource               Retry                      Lock                             Ordered wcpd RPCs                                                                    Adapter(s)                                  Verification oracle
  -------------------------------- ---------------------- -------------------------- -------------------------------- ------------------------------------------------------------------------------------ ------------------------------------------- ----------------------------------------------------------------
  `account.provision`              `hosting_account`      `RECONCILE_BEFORE_RETRY`   `account:{id}`                   `UserCreate → DirectoryEnsure → QuotaApply → PermissionReconcile`                    `filesystem/quota/systemd`                  `uid/gid exists; root path owned; quota observed`

  `account.suspend`                `hosting_account`      `RECONCILE_BEFORE_RETRY`   `account:{id}`                   `UserLock → ServiceReload only for generated dependent configs`                      `identity + managed services`               `login locked; managed workloads disabled`

  `account.resume`                 `hosting_account`      `RECONCILE_BEFORE_RETRY`   `account:{id}`                   `UserUnlock → dependent config reconcile`                                            `identity + managed services`               `login/workloads match desired`

  `account.delete`                 `hosting_account`      `NO_AUTOMATIC_RETRY`       `account:{id}`                   `dependency DAG → UserLock → managed data policy → UserDelete`                       `all owned adapters`                        `all required children absent/retained by policy; uid absent`

  `domain.provision`               `domain`               `RECONCILE_BEFORE_RETRY`   `domain:{id}`                    `none unless generated web config activation required`                               `site + PowerDNS + ACME orchestration`      `binding/DNS/TLS policy observed`

  `domain.reconcile`               `domain`               `RECONCILE_BEFORE_RETRY`   `domain:{id}`                    `ManagedGenerationActivate if site config changes`                                   `site/PowerDNS/ACME`                        `desired bindings observed`

  `domain.delete`                  `domain`               `NO_AUTOMATIC_RETRY`       `domain:{id}`                    `ManagedGenerationActivate for detach`                                               `site/PowerDNS/mail`                        `domain detached and managed records/config absent per policy`

  `site.provision`                 `site`                 `RECONCILE_BEFORE_RETRY`   `site:{id}`                      `DirectoryEnsure → ManagedFileInstall → ManagedGenerationActivate → ServiceReload`   `filesystem/Nginx/PHP-FPM`                  `syntax valid; sockets/listener/http probe`

  `site.reconfigure`               `site`                 `RECONCILE_BEFORE_RETRY`   `site:{id}`                      `ManagedGenerationActivate → ServiceReload`                                          `Nginx/PHP-FPM`                             `generation fingerprint + probe`

  `site.delete`                    `site`                 `NO_AUTOMATIC_RETRY`       `site:{id}`                      `ManagedGenerationActivate → ServiceReload → data retention action`                  `Nginx/PHP-FPM/filesystem`                  `vhost/socket absent; data state matches policy`

  `php_pool.render_apply_verify`   `runtime_profile`      `RECONCILE_BEFORE_RETRY`   `site:{site_id}`                 `ManagedGenerationActivate → ServiceReload`                                          `PHP-FPM`                                   `pool config fingerprint/socket/process observed`

  `certificate.issue`              `certificate`          `EXTERNAL_BACKOFF`         `certificate-subject:{domain}`   `ManagedFileInstall → ManagedGenerationActivate → ServiceReload`                     `ACME + Nginx`                              `served certificate fingerprint/expiry matches`

  `certificate.renew`              `certificate`          `EXTERNAL_BACKOFF`         `certificate:{id}`               `ManagedFileInstall → ManagedGenerationActivate → ServiceReload`                     `ACME + Nginx`                              `new fingerprint served`

  `certificate.revoke`             `certificate`          `NO_AUTOMATIC_RETRY`       `certificate:{id}`               `ManagedGenerationActivate if detach required`                                       `ACME + Nginx`                              `provider revoke result + no active binding`

  `certificate.verify`             `certificate`          `SAFE_RETRY`               `certificate:{id}`               `none`                                                                               `TLS observer`                              `served chain/hostname/expiry result recorded`

  `dns.zone.create`                `dns_zone`             `EXTERNAL_BACKOFF`         `zone:{id}`                      `none`                                                                               `PowerDNS`                                  `authoritative SOA/serial query`

  `dns.zone.update`                `dns_zone`             `EXTERNAL_BACKOFF`         `zone:{id}`                      `none`                                                                               `PowerDNS`                                  `authoritative serial/content query`

  `dns.zone.delete`                `dns_zone`             `NO_AUTOMATIC_RETRY`       `zone:{id}`                      `none`                                                                               `PowerDNS`                                  `authoritative NXDOMAIN/not-authoritative evidence`

  `dns.zone.reconcile`             `dns_zone`             `EXTERNAL_BACKOFF`         `zone:{id}`                      `none`                                                                               `PowerDNS`                                  `desired RRsets equal observed`

  `dns.record.create`              `dns_record`           `EXTERNAL_BACKOFF`         `zone:{zone_id}`                 `none`                                                                               `PowerDNS`                                  `RRset observed`

  `dns.record.update`              `dns_record`           `EXTERNAL_BACKOFF`         `zone:{zone_id}`                 `none`                                                                               `PowerDNS`                                  `new RRset observed`

  `dns.record.delete`              `dns_record`           `EXTERNAL_BACKOFF`         `zone:{zone_id}`                 `none`                                                                               `PowerDNS`                                  `record absence observed`

  `database.create`                `database`             `RECONCILE_BEFORE_RETRY`   `db:{id}`                        `none`                                                                               `MariaDB`                                   `schema exists with expected charset/owner metadata`

  `database.delete`                `database`             `NO_AUTOMATIC_RETRY`       `db:{id}`                        `none`                                                                               `MariaDB`                                   `schema absent`

  `database.backup`                `database`             `SAFE_RETRY`               `db:{id}`                        `none`                                                                               `MariaDB backup + backup adapter`           `artifact checksum/manifest`

  `database.restore`               `database`             `NO_AUTOMATIC_RETRY`       `db:{id}`                        `none`                                                                               `MariaDB + backup adapter`                  `restore verification/query + manifest relation`

  `database_user.create`           `database_user`        `RECONCILE_BEFORE_RETRY`   `db:{database_id}`               `none`                                                                               `MariaDB + secret service`                  `principal/grants observed`

  `database_user.rotate`           `database_user`        `NO_AUTOMATIC_RETRY`       `dbuser:{id}`                    `none`                                                                               `MariaDB + secret lifecycle`                `new credential verified then old invalidated`

  `database_user.delete`           `database_user`        `RECONCILE_BEFORE_RETRY`   `db:{database_id}`               `none`                                                                               `MariaDB`                                   `principal/grants absent`

  `mailbox.create`                 `mailbox`              `RECONCILE_BEFORE_RETRY`   `mailbox:{id}`                   `DirectoryEnsure → PermissionReconcile → ServiceReload if generated maps changed`    `Dovecot/Postfix/Rspamd`                    `auth + delivery target + quota observed`

  `mailbox.suspend`                `mailbox`              `RECONCILE_BEFORE_RETRY`   `mailbox:{id}`                   `ServiceReload if map changed`                                                       `Dovecot/Postfix`                           `auth/submission denied; retention preserved`

  `mailbox.resume`                 `mailbox`              `RECONCILE_BEFORE_RETRY`   `mailbox:{id}`                   `ServiceReload if map changed`                                                       `Dovecot/Postfix`                           `auth/delivery restored`

  `mailbox.rotate_secret`          `mailbox`              `NO_AUTOMATIC_RETRY`       `mailbox:{id}`                   `none`                                                                               `Dovecot auth backend + secret lifecycle`   `new auth succeeds; old auth fails after cutover`

  `mailbox.delete`                 `mailbox`              `NO_AUTOMATIC_RETRY`       `mailbox:{id}`                   `ServiceReload → data retention action`                                              `Dovecot/Postfix/filesystem`                `routing/auth absent; data retained/purged by policy`

  `backup.run`                     `backup_run`           `EXTERNAL_BACKOFF`         `backup-scope:{scope}`           `filesystem snapshot/read operations are typed adapters, no arbitrary RPC`           `backup + S3-compatible`                    `manifest complete, checksums and remote object verification`

  `restore.run`                    `restore_run`          `NO_AUTOMATIC_RETRY`       `restore-target:{scope}`         `typed ownership/config activation operations per dataset`                           `backup + target service adapters`          `dataset-specific verification + readiness`

  `cron.reconcile`                 `cron_job`             `RECONCILE_BEFORE_RETRY`   `account:{account_id}`           `ManagedGenerationActivate`                                                          `systemd/cron adapter`                      `canonical schedule installed/absent`

  `integration.verify`             `integration`          `EXTERNAL_BACKOFF`         `integration:{id}`               `none`                                                                               `provider adapter`                          `authenticated non-mutating probe`

  `audit.export`                   `audit_record`         `SAFE_RETRY`               `audit-export:{actor}:{range}`   `none`                                                                               `audit export adapter`                      `artifact count/hash and authorization scope`

  `notification.test`              `notification_route`   `EXTERNAL_BACKOFF`         `notification:{id}`              `none`                                                                               `notification provider`                     `provider acknowledgement correlated`

  `security.scan`                  `security_finding`     `SAFE_RETRY`               `security-scan:{scope}`          `typed read-only scanner RPCs only where root visibility required`                   `security scanners`                         `findings persisted with evidence`

  `repair.apply`                   `security_finding`     `RECONCILE_BEFORE_RETRY`   `repair:{resource}`              `exact remediation RPC list from finding remediation_type allowlist`                 `target adapter`                            `finding-specific postcondition`

  `readiness.evaluate`             `node`                 `SAFE_RETRY`               `node:{id}`                      `read-only ServiceStatus/ListenerInspect as required`                                `health adapters`                           `readiness finding set complete`

  `service.reconcile`              `node`                 `RECONCILE_BEFORE_RETRY`   `node:{id}`                      `only RPCs declared by drift item; no wildcard execution`                            `managed service adapters`                  `desired/observed equality or explicit conflict`
  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## RPC decision rule

`none` means the Job is privileged only inside a service/provider
credential boundary and **must not call wcpd**. A listed RPC sequence is
the maximum privileged primitive set for that Job; adding another RPC
requires contract revision.

## Job control

`job.retry` and `job.cancel` are synchronous control-plane state
transitions, not Jobs. Retry creates a new attempt only when the
canonical retry class permits it. Cancel moves to `CANCEL_REQUESTED`;
workers honor it only at declared safe cancellation points.
