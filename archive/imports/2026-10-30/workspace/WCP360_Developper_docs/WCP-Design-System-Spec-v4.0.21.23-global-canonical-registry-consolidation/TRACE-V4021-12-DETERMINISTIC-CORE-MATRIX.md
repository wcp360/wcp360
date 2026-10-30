# V4.0.21 --- Deterministic CORE Traceability Matrix

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

## Representative high-risk mutations

  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Mutation                   API Op ID         Capability               Job                       Exact privileged sequence                                                      Adapter                           Audit                       Acceptance
  -------------------------- ----------------- ------------------------ ------------------------- ------------------------------------------------------------------------------ --------------------------------- --------------------------- ------------------
  `hosting_account.create`   `ACC-CREATE`      `account.create`         `account.provision`       `UserCreate→DirectoryEnsure→QuotaApply→PermissionReconcile`                    `filesystem/quota`                `account.create`            `E2E-ACCEPT-002`

  `domain.create`            `DOM-CREATE`      `domain.manage`          `domain.provision`        `ManagedGenerationActivate only if bound site requires`                        `site+PowerDNS+ACME`              `domain.mutate`             `E2E-ACCEPT-006`

  `site.create`              `SITE-CREATE`     `site.manage`            `site.provision`          `DirectoryEnsure→ManagedFileInstall→ManagedGenerationActivate→ServiceReload`   `filesystem+Nginx+PHP-FPM`        `site.mutate`               `E2E-ACCEPT-007`

  `certificate.issue`        `CERT-ISSUE`      `certificate.manage`     `certificate.issue`       `ManagedFileInstall→ManagedGenerationActivate→ServiceReload`                   `ACME+Nginx`                      `certificate.action`        `E2E-ACCEPT-009`

  `dns_record.create`        `DNSR-CREATE`     `dns.manage`             `dns.record.create`       `none`                                                                         `PowerDNS`                        `dns.record.mutate`         `E2E-ACCEPT-010`

  `database.create`          `DB-CREATE`       `database.manage`        `database.create`         `none`                                                                         `MariaDB`                         `database.action`           `E2E-ACCEPT-011`

  `database_user.rotate`     `DBU-ROTATE`      `database.user.manage`   `database_user.rotate`    `none`                                                                         `MariaDB+secret lifecycle`        `database.user.action`      `SEC-ACCEPT-010`

  `mailbox.create`           `MBOX-CREATE`     `mailbox.manage`         `mailbox.create`          `DirectoryEnsure→PermissionReconcile→ServiceReload if generated map changed`   `Dovecot+Postfix`                 `mailbox.action`            `E2E-ACCEPT-013`

  `mailbox.rotate_secret`    `MBOX-ROTATE`     `mailbox.manage`         `mailbox.rotate_secret`   `none`                                                                         `Dovecot auth+secret lifecycle`   `mailbox.action`            `SEC-ACCEPT-010`

  `backup.start`             `BACKUP-START`    `backup.run`             `backup.run`              `typed dataset reads only`                                                     `backup+S3`                       `backup.run`                `E2E-ACCEPT-016`

  `restore.start`            `RESTORE-START`   `backup.restore`         `restore.run`             `typed ownership/config RPCs per manifest dataset`                             `backup+target adapters`          `restore.run`               `SEC-ACCEPT-018`

  `security.repair`          `SEC-REPAIR`      `security.manage`        `repair.apply`            `finding remediation allowlist exact sequence`                                 `target adapter`                  `security.finding.action`   `SEC-ACCEPT-014`

  `node.reconcile`           `NODE-RECON`      `system.operate`         `service.reconcile`       `drift-item-declared RPC sequence only`                                        `managed service adapters`        `system.node.action`        `E2E-ACCEPT-022`
  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## Control-plane-only mutations

Tenant/user/role-binding/reseller/plan/entitlement/backup-policy/API-token
metadata mutations are synchronous PostgreSQL transactions with **no
wcpd RPC and no external adapter**. Their deterministic contract is:
authorize → validate invariant/version → transaction → outbox/audit →
response.

## Closure

All high-risk execution families now resolve to a named operation, named
capability, named Job or explicit synchronous transaction, exact
privileged primitive set or explicit none, named adapter, named audit
action and named acceptance oracle.
