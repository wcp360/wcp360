# V4.0.21 --- Exact API Operation Registry

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

## Read contracts

Every resource with a user-visible surface has `GET collection` and/or
`GET /{id}` under its canonical collection; reads are `SYNC_READ`, safe,
tenant-filtered before pagination, and never create Jobs.

## Mutation registry

  ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Operation ID       Method     Path                                                 Capability               Execution                            Success   Precondition/idempotency       Principal error
  ------------------ ---------- ---------------------------------------------------- ------------------------ ------------------------------------ --------- ------------------------------ ----------------------------------------
  `TEN-LIST`         `GET`      `/api/v1/tenants`                                    `tenant.view`            `SYNC_READ`                          `200`     `none`                         `safe`

  `TEN-CREATE`       `POST`     `/api/v1/tenants`                                    `tenant.manage`          `SYNC_DB`                            `201`     `Idempotency-Key`              `409 duplicate slug`

  `TEN-UPDATE`       `PATCH`    `/api/v1/tenants/{tenant_id}`                        `tenant.manage`          `SYNC_DB`                            `200`     `If-Match`                     `412 version mismatch`

  `USR-CREATE`       `POST`     `/api/v1/users`                                      `identity.manage`        `SYNC_DB`                            `201`     `Idempotency-Key`              `409 normalized identity collision`

  `USR-DISABLE`      `POST`     `/api/v1/users/{user_id}:disable`                    `identity.manage`        `SYNC_DB`                            `200`     `If-Match`                     `409 last-required-admin guard`

  `RBAC-BIND`        `POST`     `/api/v1/role-bindings`                              `rbac.manage`            `SYNC_DB`                            `201`     `Idempotency-Key`              `403 delegation ceiling`

  `ACC-CREATE`       `POST`     `/api/v1/accounts`                                   `account.create`         `JOB account.provision`              `202`     `Idempotency-Key`              `409 name/uid allocation conflict`

  `ACC-SUSPEND`      `POST`     `/api/v1/accounts/{id}:suspend`                      `account.operate`        `JOB account.suspend`                `202`     `If-Match + Idempotency-Key`   `409 invalid lifecycle`

  `ACC-RESUME`       `POST`     `/api/v1/accounts/{id}:resume`                       `account.operate`        `JOB account.resume`                 `202`     `If-Match + Idempotency-Key`   `409 invalid lifecycle`

  `ACC-DELETE`       `DELETE`   `/api/v1/accounts/{id}`                              `account.delete`         `JOB account.delete`                 `202`     `If-Match + Idempotency-Key`   `409 unresolved dependencies`

  `DOM-CREATE`       `POST`     `/api/v1/domains`                                    `domain.manage`          `JOB domain.provision`               `202`     `Idempotency-Key`              `409 managed FQDN collision`

  `DOM-UPDATE`       `PATCH`    `/api/v1/domains/{id}`                               `domain.manage`          `JOB domain.reconcile`               `202`     `If-Match + Idempotency-Key`   `409 dependency conflict`

  `DOM-DELETE`       `DELETE`   `/api/v1/domains/{id}`                               `domain.manage`          `JOB domain.delete`                  `202`     `If-Match + Idempotency-Key`   `409 dependent mail/site policy`

  `SITE-CREATE`      `POST`     `/api/v1/sites`                                      `site.manage`            `JOB site.provision`                 `202`     `Idempotency-Key`              `409 path/domain conflict`

  `SITE-UPDATE`      `PATCH`    `/api/v1/sites/{id}`                                 `site.manage`            `JOB site.reconfigure`               `202`     `If-Match + Idempotency-Key`   `422 invalid runtime/config`

  `SITE-DELETE`      `DELETE`   `/api/v1/sites/{id}`                                 `site.manage`            `JOB site.delete`                    `202`     `If-Match + Idempotency-Key`   `409 retention/dependency`

  `RUNTIME-UPDATE`   `PUT`      `/api/v1/sites/{id}/runtime`                         `site.runtime.manage`    `JOB php_pool.render_apply_verify`   `202`     `If-Match + Idempotency-Key`   `422 unsupported runtime`

  `CERT-ISSUE`       `POST`     `/api/v1/certificates:issue`                         `certificate.manage`     `JOB certificate.issue`              `202`     `Idempotency-Key`              `422 ACME prerequisites`

  `CERT-RENEW`       `POST`     `/api/v1/certificates/{id}:renew`                    `certificate.manage`     `JOB certificate.renew`              `202`     `Idempotency-Key`              `409 not renewable`

  `CERT-REVOKE`      `POST`     `/api/v1/certificates/{id}:revoke`                   `certificate.manage`     `JOB certificate.revoke`             `202`     `If-Match + Idempotency-Key`   `409 already revoked`

  `CERT-VERIFY`      `POST`     `/api/v1/certificates/{id}:verify`                   `certificate.manage`     `JOB certificate.verify`             `202`     `Idempotency-Key`              `404 missing`

  `DNSZ-CREATE`      `POST`     `/api/v1/dns-zones`                                  `dns.manage`             `JOB dns.zone.create`                `202`     `Idempotency-Key`              `409 zone collision`

  `DNSZ-UPDATE`      `PATCH`    `/api/v1/dns-zones/{id}`                             `dns.manage`             `JOB dns.zone.update`                `202`     `If-Match + Idempotency-Key`   `422 invalid zone`

  `DNSZ-DELETE`      `DELETE`   `/api/v1/dns-zones/{id}`                             `dns.manage`             `JOB dns.zone.delete`                `202`     `If-Match + Idempotency-Key`   `409 managed domain dependency`

  `DNSZ-RECON`       `POST`     `/api/v1/dns-zones/{id}:reconcile`                   `dns.manage`             `JOB dns.zone.reconcile`             `202`     `Idempotency-Key`              `409 conflicting external change`

  `DNSR-CREATE`      `POST`     `/api/v1/dns-zones/{zid}/records`                    `dns.manage`             `JOB dns.record.create`              `202`     `Idempotency-Key`              `422 DNS invariant`

  `DNSR-UPDATE`      `PATCH`    `/api/v1/dns-zones/{zid}/records/{id}`               `dns.manage`             `JOB dns.record.update`              `202`     `If-Match + Idempotency-Key`   `422 DNS invariant`

  `DNSR-DELETE`      `DELETE`   `/api/v1/dns-zones/{zid}/records/{id}`               `dns.manage`             `JOB dns.record.delete`              `202`     `If-Match + Idempotency-Key`   `409 protected system record`

  `DB-CREATE`        `POST`     `/api/v1/databases`                                  `database.manage`        `JOB database.create`                `202`     `Idempotency-Key`              `409 allocated name conflict`

  `DB-DELETE`        `DELETE`   `/api/v1/databases/{id}`                             `database.manage`        `JOB database.delete`                `202`     `If-Match + Idempotency-Key`   `409 users/retention`

  `DB-BACKUP`        `POST`     `/api/v1/databases/{id}:backup`                      `database.manage`        `JOB database.backup`                `202`     `Idempotency-Key`              `409 unavailable`

  `DB-RESTORE`       `POST`     `/api/v1/databases/{id}:restore`                     `database.manage`        `JOB database.restore`               `202`     `Idempotency-Key`              `409 conflict plan required`

  `DBU-CREATE`       `POST`     `/api/v1/databases/{id}/users`                       `database.user.manage`   `JOB database_user.create`           `202`     `Idempotency-Key`              `409 name collision`

  `DBU-ROTATE`       `POST`     `/api/v1/databases/{id}/users/{uid}:rotate-secret`   `database.user.manage`   `JOB database_user.rotate`           `202`     `Idempotency-Key`              `409 rotation in progress`

  `DBU-DELETE`       `DELETE`   `/api/v1/databases/{id}/users/{uid}`                 `database.user.manage`   `JOB database_user.delete`           `202`     `If-Match + Idempotency-Key`   `409 protected owner`

  `MBOX-CREATE`      `POST`     `/api/v1/mail/mailboxes`                             `mailbox.manage`         `JOB mailbox.create`                 `202`     `Idempotency-Key`              `409 address collision`

  `MBOX-SUSPEND`     `POST`     `/api/v1/mail/mailboxes/{id}:suspend`                `mailbox.manage`         `JOB mailbox.suspend`                `202`     `If-Match + Idempotency-Key`   `409 lifecycle`

  `MBOX-RESUME`      `POST`     `/api/v1/mail/mailboxes/{id}:resume`                 `mailbox.manage`         `JOB mailbox.resume`                 `202`     `If-Match + Idempotency-Key`   `409 lifecycle`

  `MBOX-ROTATE`      `POST`     `/api/v1/mail/mailboxes/{id}:rotate-secret`          `mailbox.manage`         `JOB mailbox.rotate_secret`          `202`     `Idempotency-Key`              `409 rotation in progress`

  `MBOX-DELETE`      `DELETE`   `/api/v1/mail/mailboxes/{id}`                        `mailbox.manage`         `JOB mailbox.delete`                 `202`     `If-Match + Idempotency-Key`   `409 retention hold`

  `BACKUP-START`     `POST`     `/api/v1/backups/runs`                               `backup.run`             `JOB backup.run`                     `202`     `Idempotency-Key`              `409 concurrent exclusive run`

  `RESTORE-START`    `POST`     `/api/v1/backups/restores`                           `backup.restore`         `JOB restore.run`                    `202`     `Idempotency-Key`              `409 unresolved conflict plan`

  `JOB-RETRY`        `POST`     `/api/v1/jobs/{id}:retry`                            `job.operate`            `SYNC_CONTROL`                       `200`     `If-Match + Idempotency-Key`   `409 retry class forbids`

  `JOB-CANCEL`       `POST`     `/api/v1/jobs/{id}:cancel`                           `job.operate`            `SYNC_CONTROL`                       `200`     `If-Match + Idempotency-Key`   `409 non-cancellable phase`

  `AUDIT-EXPORT`     `POST`     `/api/v1/audit:export`                               `audit.export`           `JOB audit.export`                   `202`     `Idempotency-Key`              `422 invalid range`

  `NOTIFY-TEST`      `POST`     `/api/v1/notification-routes/{id}:test`              `notification.manage`    `JOB notification.test`              `202`     `Idempotency-Key`              `409 disabled route`

  `SEC-SCAN`         `POST`     `/api/v1/security:scan`                              `security.manage`        `JOB security.scan`                  `202`     `Idempotency-Key`              `409 scan conflict`

  `SEC-REPAIR`       `POST`     `/api/v1/security/findings/{id}:repair`              `security.manage`        `JOB repair.apply`                   `202`     `If-Match + Idempotency-Key`   `409 remediation no longer applicable`

  `READY-EVAL`       `POST`     `/api/v1/readiness:evaluate`                         `system.operate`         `JOB readiness.evaluate`             `202`     `Idempotency-Key`              `409 maintenance lock`

  `NODE-RECON`       `POST`     `/api/v1/system/node:reconcile`                      `system.operate`         `JOB service.reconcile`              `202`     `Idempotency-Key`              `409 conflicting unmanaged state`
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## HTTP invariants

-   `201` only for synchronous resource creation; `202` for accepted
    asynchronous mutation.
-   `If-Match` is mandatory where an existing versioned resource is
    mutated.
-   Reusing an `Idempotency-Key` with a different canonical request
    fingerprint returns conflict.
-   `DELETE` of an asynchronous managed resource returns `202`; absence
    is proven by its delete Job.
-   Validation errors are `422`; authorization `403`; hidden
    cross-tenant resources resolve according anti-enumeration policy
    without leaking ownership.
