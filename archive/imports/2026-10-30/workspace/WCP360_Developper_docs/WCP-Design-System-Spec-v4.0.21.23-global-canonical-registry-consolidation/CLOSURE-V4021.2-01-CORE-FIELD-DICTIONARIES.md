# V4.0.21.2 --- CORE Logical Field Dictionaries

## Statut

**NORMATIVE --- NO SQL --- LOGICAL CONTRACT**

## `hosting_account`

  -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Field                  Type          Required   Nullable   Mutability    Default          Domain/Reference                                        Writer                Class
  ---------------------- ------------- ---------- ---------- ------------- ---------------- ------------------------------------------------------- --------------------- -------------
  `id`                   `UUID`        `yes`      `no`       `immutable`   ---              ---                                                     `platform`            `INTERNAL`

  `tenant_id`            `UUID`        `yes`      `no`       `immutable`   ---              `tenant.id`                                             `platform`            `INTERNAL`

  `reseller_id`          `UUID`        `no`       `yes`      `immutable`   ---              `reseller.id`                                           `platform`            `INTERNAL`

  `login`                `STRING`      `yes`      `no`       `immutable`   ---              ---                                                     `platform`            `PII`

  `status`               `ENUM`        `yes`      `no`       `mutable`     `PROVISIONING`   `PROVISIONING|READY|SUSPENDED|DELETING|DELETED|ERROR`   `control-plane`       `INTERNAL`

  `account_mode`         `ENUM`        `yes`      `no`       `mutable`     `STANDARD`       `STANDARD|DEMO|TRIAL|SUSPENDED`                         `control-plane`       `INTERNAL`

  `plan_assignment_id`   `UUID`        `yes`      `no`       `mutable`     ---              `plan_assignment.id`                                    `control-plane`       `INTERNAL`

  `system_identity_id`   `UUID`        `no`       `yes`      `immutable`   ---              `system_identity.id`                                    `wcpd-orchestrator`   `SENSITIVE`

  `created_at`           `TIMESTAMP`   `yes`      `no`       `immutable`   ---              ---                                                     `system`              `INTERNAL`

  `version`              `UINT64`      `yes`      `no`       `mutable`     `1`              ---                                                     `system`              `INTERNAL`
  -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## `hosting_plan`

  ------------------------------------------------------------------------------------------------------------------------------------------------
  Field                   Type          Required   Nullable   Mutability    Default   Domain/Reference          Writer                Class
  ----------------------- ------------- ---------- ---------- ------------- --------- ------------------------- --------------------- ------------
  `id`                    `UUID`        `yes`      `no`       `immutable`   ---       ---                       `platform`            `INTERNAL`

  `owner_scope_id`        `UUID`        `yes`      `no`       `immutable`   ---       `tenant/reseller`         `platform`            `INTERNAL`

  `code`                  `STRING`      `yes`      `no`       `immutable`   ---       ---                       `platform/reseller`   `INTERNAL`

  `display_name`          `STRING`      `yes`      `no`       `mutable`     ---       ---                       `platform/reseller`   `PUBLIC`

  `version`               `UINT64`      `yes`      `no`       `mutable`     `1`       ---                       `system`              `INTERNAL`

  `status`                `ENUM`        `yes`      `no`       `mutable`     `DRAFT`   `DRAFT|ACTIVE|ARCHIVED`   `platform/reseller`   `INTERNAL`

  `feature_set_version`   `UINT64`      `yes`      `no`       `mutable`     `1`       ---                       `system`              `INTERNAL`

  `created_at`            `TIMESTAMP`   `yes`      `no`       `immutable`   ---       ---                       `system`              `INTERNAL`
  ------------------------------------------------------------------------------------------------------------------------------------------------

## `plan_assignment`

  -----------------------------------------------------------------------------------------------------------------------------------------------------
  Field                     Type          Required   Nullable   Mutability    Default    Domain/Reference                Writer            Class
  ------------------------- ------------- ---------- ---------- ------------- ---------- ------------------------------- ----------------- ------------
  `id`                      `UUID`        `yes`      `no`       `immutable`   ---        ---                             `system`          `INTERNAL`

  `account_id`              `UUID`        `yes`      `no`       `immutable`   ---        `hosting_account.id`            `control-plane`   `INTERNAL`

  `plan_id`                 `UUID`        `yes`      `no`       `mutable`     ---        `hosting_plan.id`               `control-plane`   `INTERNAL`

  `assigned_plan_version`   `UINT64`      `yes`      `no`       `mutable`     ---        ---                             `control-plane`   `INTERNAL`

  `state`                   `ENUM`        `yes`      `no`       `mutable`     `ACTIVE`   `ACTIVE|PENDING|NONCOMPLIANT`   `control-plane`   `INTERNAL`

  `effective_from`          `TIMESTAMP`   `yes`      `no`       `mutable`     ---        ---                             `control-plane`   `INTERNAL`
  -----------------------------------------------------------------------------------------------------------------------------------------------------

## `entitlement_override`

  ----------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Field               Type           Required   Nullable   Mutability    Default   Domain/Reference                                      Writer            Class
  ------------------- -------------- ---------- ---------- ------------- --------- ----------------------------------------------------- ----------------- -------------
  `id`                `UUID`         `yes`      `no`       `immutable`   ---       ---                                                   `system`          `INTERNAL`

  `account_id`        `UUID`         `yes`      `no`       `immutable`   ---       `hosting_account.id`                                  `control-plane`   `INTERNAL`

  `entitlement_key`   `STRING`       `yes`      `no`       `immutable`   ---       `module_catalog.key`                                  `control-plane`   `INTERNAL`

  `operation`         `ENUM`         `yes`      `no`       `mutable`     ---       `GRANT|DENY|SET_LIMIT|SET_ENUM_SET|RESET_TO_PARENT`   `control-plane`   `INTERNAL`

  `value`             `TYPED_JSON`   `no`       `yes`      `mutable`     ---       ---                                                   `control-plane`   `SENSITIVE`

  `reason`            `STRING`       `yes`      `no`       `mutable`     ---       ---                                                   `actor`           `INTERNAL`

  `expires_at`        `TIMESTAMP`    `no`       `yes`      `mutable`     ---       ---                                                   `actor`           `INTERNAL`

  `version`           `UINT64`       `yes`      `no`       `mutable`     `1`       ---                                                   `system`          `INTERNAL`
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

## `site`

  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Field                  Type             Required   Nullable   Mutability    Default          Domain/Reference                                        Writer            Class
  ---------------------- ---------------- ---------- ---------- ------------- ---------------- ------------------------------------------------------- ----------------- -------------
  `id`                   `UUID`           `yes`      `no`       `immutable`   ---              ---                                                     `system`          `INTERNAL`

  `account_id`           `UUID`           `yes`      `no`       `immutable`   ---              `hosting_account.id`                                    `control-plane`   `INTERNAL`

  `primary_fqdn`         `FQDN`           `yes`      `no`       `mutable`     ---              ---                                                     `account-admin`   `PUBLIC`

  `document_root`        `MANAGED_PATH`   `yes`      `no`       `immutable`   ---              ---                                                     `orchestrator`    `SENSITIVE`

  `runtime_profile_id`   `UUID`           `yes`      `no`       `mutable`     ---              `runtime_profile.id`                                    `account-admin`   `INTERNAL`

  `status`               `ENUM`           `yes`      `no`       `mutable`     `PROVISIONING`   `PROVISIONING|READY|SUSPENDED|ERROR|DELETING|DELETED`   `orchestrator`    `INTERNAL`

  `version`              `UINT64`         `yes`      `no`       `mutable`     `1`              ---                                                     `system`          `INTERNAL`
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## `domain`

  -----------------------------------------------------------------------------------------------------------------------------------------------------
  Field          Type     Required   Nullable   Mutability    Default           Domain/Reference                         Writer            Class
  -------------- -------- ---------- ---------- ------------- ----------------- ---------------------------------------- ----------------- ------------
  `id`           `UUID`   `yes`      `no`       `immutable`   ---               ---                                      `system`          `INTERNAL`

  `account_id`   `UUID`   `yes`      `no`       `immutable`   ---               `hosting_account.id`                     `control-plane`   `INTERNAL`

  `fqdn`         `FQDN`   `yes`      `no`       `immutable`   ---               ---                                      `account-admin`   `PUBLIC`

  `site_id`      `UUID`   `no`       `yes`      `mutable`     ---               `site.id`                                `account-admin`   `INTERNAL`

  `dns_mode`     `ENUM`   `yes`      `no`       `mutable`     `MANAGED_LOCAL`   `MANAGED_LOCAL|EXTERNAL`                 `account-admin`   `INTERNAL`

  `tls_policy`   `ENUM`   `yes`      `no`       `mutable`     `MANAGED_ACME`    `MANAGED_ACME|EXTERNAL|DISABLED`         `account-admin`   `INTERNAL`

  `status`       `ENUM`   `yes`      `no`       `mutable`     `PENDING`         `PENDING|READY|ERROR|DELETING|DELETED`   `orchestrator`    `INTERNAL`
  -----------------------------------------------------------------------------------------------------------------------------------------------------

## `database`

  --------------------------------------------------------------------------------------------------------------------------------------------------------------
  Field            Type       Required   Nullable   Mutability    Default          Domain/Reference                              Writer            Class
  ---------------- ---------- ---------- ---------- ------------- ---------------- --------------------------------------------- ----------------- -------------
  `id`             `UUID`     `yes`      `no`       `immutable`   ---              ---                                           `system`          `INTERNAL`

  `account_id`     `UUID`     `yes`      `no`       `immutable`   ---              `hosting_account.id`                          `control-plane`   `INTERNAL`

  `engine`         `ENUM`     `yes`      `no`       `immutable`   ---              `MARIADB|POSTGRESQL`                          `account-admin`   `INTERNAL`

  `logical_name`   `STRING`   `yes`      `no`       `immutable`   ---              ---                                           `account-admin`   `INTERNAL`

  `system_name`    `STRING`   `yes`      `no`       `immutable`   ---              ---                                           `orchestrator`    `SENSITIVE`

  `status`         `ENUM`     `yes`      `no`       `mutable`     `PROVISIONING`   `PROVISIONING|READY|ERROR|DELETING|DELETED`   `orchestrator`    `INTERNAL`
  --------------------------------------------------------------------------------------------------------------------------------------------------------------

## `mailbox`

  ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Field           Type           Required   Nullable   Mutability    Default          Domain/Reference                                        Writer             Class
  --------------- -------------- ---------- ---------- ------------- ---------------- ------------------------------------------------------- ------------------ -------------------
  `id`            `UUID`         `yes`      `no`       `immutable`   ---              ---                                                     `system`           `INTERNAL`

  `account_id`    `UUID`         `yes`      `no`       `immutable`   ---              `hosting_account.id`                                    `control-plane`    `INTERNAL`

  `address`       `EMAIL`        `yes`      `no`       `immutable`   ---              ---                                                     `account-admin`    `PII`

  `quota_bytes`   `UINT64`       `yes`      `no`       `mutable`     ---              ---                                                     `account-admin`    `INTERNAL`

  `secret_ref`    `SECRET_REF`   `yes`      `no`       `mutable`     ---              `secret.id`                                             `secret-service`   `SECRET_REF_ONLY`

  `status`        `ENUM`         `yes`      `no`       `mutable`     `PROVISIONING`   `PROVISIONING|READY|SUSPENDED|ERROR|DELETING|DELETED`   `orchestrator`     `INTERNAL`
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## `demo_policy`

  ------------------------------------------------------------------------------------------------------------------------------------------------------
  Field              Type                        Required   Nullable   Mutability    Default      Domain/Reference    Writer                Class
  ------------------ --------------------------- ---------- ---------- ------------- ------------ ------------------- --------------------- ------------
  `id`               `UUID`                      `yes`      `no`       `immutable`   ---          ---                 `system`              `INTERNAL`

  `owner_scope_id`   `UUID`                      `yes`      `no`       `immutable`   ---          `tenant/reseller`   `platform/reseller`   `INTERNAL`

  `name`             `STRING`                    `yes`      `no`       `mutable`     ---          ---                 `platform/reseller`   `PUBLIC`

  `version`          `UINT64`                    `yes`      `no`       `mutable`     `1`          ---                 `system`              `INTERNAL`

  `module_rules`     `ARRAY<DEMO_MODULE_RULE>`   `yes`      `no`       `mutable`     `[]`         ---                 `platform/reseller`   `INTERNAL`

  `reset_strategy`   `ENUM`                      `yes`      `no`       `mutable`     `SNAPSHOT`   `SNAPSHOT|EMPTY`    `platform/reseller`   `INTERNAL`
  ------------------------------------------------------------------------------------------------------------------------------------------------------

## `trial_policy`

  -----------------------------------------------------------------------------------------------------------------------------------
  Field                    Type           Required   Nullable   Mutability    Default   Domain/Reference    Writer       Class
  ------------------------ -------------- ---------- ---------- ------------- --------- ------------------- ------------ ------------
  `id`                     `UUID`         `yes`      `no`       `immutable`   ---       ---                 `system`     `INTERNAL`

  `duration_seconds`       `UINT64`       `yes`      `no`       `mutable`     ---       ---                 `platform`   `INTERNAL`

  `allowed_plan_id`        `UUID`         `yes`      `no`       `mutable`     ---       `hosting_plan.id`   `platform`   `INTERNAL`

  `grace_seconds`          `UINT64`       `yes`      `no`       `mutable`     ---       ---                 `platform`   `INTERNAL`

  `retention_seconds`      `UINT64`       `yes`      `no`       `mutable`     ---       ---                 `platform`   `INTERNAL`

  `outbound_mail_policy`   `POLICY_REF`   `yes`      `no`       `mutable`     ---       ---                 `platform`   `INTERNAL`
  -----------------------------------------------------------------------------------------------------------------------------------

## Invariants transversaux

Les UUID sont opaques. Les références inter-tenant sont interdites. Les
champs `SECRET_REF` ne contiennent jamais le secret. Toute mutation
porte un contrôle de version optimiste. Les noms système sont générés et
ne dérivent jamais naïvement d'un nom visible.
