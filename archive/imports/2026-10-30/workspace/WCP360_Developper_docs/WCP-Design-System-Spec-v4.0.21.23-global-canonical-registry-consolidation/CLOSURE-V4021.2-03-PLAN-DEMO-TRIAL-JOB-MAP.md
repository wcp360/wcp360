# V4.0.21.2 --- Exact Plan/Demo/Trial Job Map

## Statut

**NORMATIVE**

  ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Job ID                         Operation                   Decision                    Adapter set                                                         Oracle
  ------------------------------ --------------------------- --------------------------- ------------------------------------------------------------------- -----------------------------------------------------------
  `plan.assignment.reconcile`    `PLAN-ASSIGN`               `DEC-PLAN-IMPACT-001`       `ADP-FS;ADP-NGINX;ADP-PHPFPM;ADP-MARIADB;ADP-POSTFIX;ADP-DOVECOT`   `effective entitlements + non-destructive reconciliation`

  `reseller.ceiling.reconcile`   `RESELLER-CEILING-UPDATE`   `DEC-PLAN-IMPACT-001`       `control-plane only + affected resource observers`                  `descendants compliant or NONCOMPLIANT`

  `trial.account.provision`      `TRIAL-ACCOUNT-CREATE`      `DEC-TRIAL-PROVISION-001`   `ADP-FS;ADP-NGINX;ADP-PHPFPM;ADP-POWERDNS;ADP-ACME;ADP-MARIADB`     `real resources verified inside trial limits`

  `trial.account.convert`        `TRIAL-CONVERT`             `DEC-PLAN-IMPACT-001`       `owned adapters selected by exact resource inventory`               `STANDARD mode + target plan effective`
  ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## Explicit no-job operations

Plan CRUD, override CRUD, DemoPolicy CRUD, DEMO create/reset and TRIAL
extension are control-plane transactions and do not invoke privileged
system mutation. DEMO create/reset mutate only the isolated Demo Store.
