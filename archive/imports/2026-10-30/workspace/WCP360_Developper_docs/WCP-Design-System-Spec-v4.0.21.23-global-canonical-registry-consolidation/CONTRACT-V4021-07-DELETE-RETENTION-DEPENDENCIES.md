# V4.0.21 --- Delete, Retention & Dependency Matrix

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

  -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Resource            Delete class                     Blocking dependencies                       Cascade rule                                              Retention                          Observed absence
  ------------------- -------------------------------- ------------------------------------------- --------------------------------------------------------- ---------------------------------- -------------------------------------------------------------
  `tenant`            `RESTRICT`                       `users, accounts, integrations, bindings`   `none implicit`                                           `manual dependency closure`        `tenant resource absent; audit retained`

  `user`              `DISABLE_THEN_PURGE`             `sessions,tokens,bindings`                  `revoke sessions/tokens`                                  `identity retention policy`        `login impossible; references tombstoned`

  `hosting_account`   `ORCHESTRATED`                   `sites,domains,db,mail,cron,data`           `explicit delete plan; no implicit destructive cascade`   `data retention choice required`   `uid/workloads/config absent; retained datasets catalogued`

  `domain`            `ORCHESTRATED`                   `site binding,DNS,TLS,mail`                 `detach according explicit request`                       `DNS/TLS artifacts policy`         `no active binding/managed zone relation`

  `site`              `ORCHESTRATED`                   `runtime,domains,files`                     `domains detach/rebind explicit`                          `files retain/purge explicit`      `vhost/pool absent`

  `dns_zone`          `RESTRICT_OR_EXPLICIT_CASCADE`   `records,domain links`                      `records cascade only on explicit zone delete`            `provider history external`        `not authoritative/managed`

  `database`          `RESTRICT_OR_EXPLICIT_CASCADE`   `database_users,backups`                    `users may cascade only confirmed`                        `backup retention independent`     `schema/principals absent as selected`

  `mail_domain`       `RESTRICT`                       `mailboxes,aliases`                         `explicit cascade request required`                       `mailbox retention independent`    `routing/domain absent`

  `mailbox`           `SUSPEND_THEN_RETENTION`         `aliases targeting mailbox,data`            `routing aliases handled by conflict plan`                `mail retention window`            `auth/routing absent; data state recorded`

  `backup_policy`     `ARCHIVE`                        `backup_runs`                               `no run deletion`                                         `runs keep immutable snapshot`     `policy inactive`

  `backup_run`        `RETENTION_PURGE`                `restore references`                        `cannot purge if protected restore/legal hold`            `policy/legal hold`                `manifest/objects absent after verified purge`

  `api_token`         `REVOKE`                         `none`                                      `none`                                                    `metadata retained`                `token auth rejected`

  `secret_ref`        `REVOKE_THEN_PURGE`              `owning resources`                          `cannot purge active referenced version`                  `crypto retention`                 `all providers reject retired material`

  `integration`       `DISABLE_THEN_DELETE`            `policies/routes using integration`         `RESTRICT until references removed`                       `audit retained`                   `no provider use`

  `node`              `PROTECTED`                      `services/jobs`                             `N/A V1 active node`                                      `N/A`                              `active V1 node cannot be deleted`
  -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## Universal rule

No destructive cascade is inferred from HTTP `DELETE`. The request must
resolve a canonical delete plan before Job creation. A resource becomes
`DELETED` only after required external/system absence is observed.
