# V4.0.21.2 --- Exact Plan/Demo/Trial API Registry

## Statut

**NORMATIVE INTEGRATION**

  --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Operation ID                   Method     Path                                                 Capability                  Scope                   Step-up      Execution           Audit
  ------------------------------ ---------- ---------------------------------------------------- --------------------------- ----------------------- ------------ ------------------- --------------------------------
  `PLAN-CREATE`                  `POST`     `/api/v1/plans`                                      `plan.manage`               `scope:plans`           `REQUIRED`   `SYNC`              `plan.created`

  `PLAN-UPDATE`                  `PATCH`    `/api/v1/plans/{plan_id}`                            `plan.manage`               `scope:plan`            `REQUIRED`   `SYNC`              `plan.changed`

  `PLAN-CLONE`                   `POST`     `/api/v1/plans/{plan_id}/clone`                      `plan.manage`               `scope:plan`            `REQUIRED`   `SYNC`              `plan.created`

  `PLAN-ARCHIVE`                 `POST`     `/api/v1/plans/{plan_id}/archive`                    `plan.manage`               `scope:plan`            `REQUIRED`   `SYNC`              `plan.changed`

  `PLAN-ASSIGN`                  `PUT`      `/api/v1/accounts/{account_id}/plan`                 `plan.assign`               `scope:account`         `REQUIRED`   `JOB`               `plan.assigned`

  `ENTITLEMENT-OVERRIDE-SET`     `PUT`      `/api/v1/accounts/{account_id}/entitlements/{key}`   `entitlement.override`      `scope:account`         `REQUIRED`   `SYNC`              `entitlement.override.changed`

  `ENTITLEMENT-OVERRIDE-RESET`   `DELETE`   `/api/v1/accounts/{account_id}/entitlements/{key}`   `entitlement.override`      `scope:account`         `REQUIRED`   `SYNC`              `entitlement.override.changed`

  `RESELLER-CEILING-UPDATE`      `PUT`      `/api/v1/resellers/{reseller_id}/ceiling`            `reseller.ceiling.manage`   `scope:reseller`        `REQUIRED`   `JOB`               `reseller.ceiling.changed`

  `DEMO-POLICY-CREATE`           `POST`     `/api/v1/demo-policies`                              `demo.manage`               `scope:demo`            `REQUIRED`   `SYNC`              `demo.policy.changed`

  `DEMO-POLICY-UPDATE`           `PATCH`    `/api/v1/demo-policies/{policy_id}`                  `demo.manage`               `scope:demo`            `REQUIRED`   `SYNC`              `demo.policy.changed`

  `DEMO-ACCOUNT-CREATE`          `POST`     `/api/v1/demo-accounts`                              `demo.manage`               `scope:demo`            `REQUIRED`   `SYNC_SIMULATION`   `demo.account.created`

  `DEMO-ACCOUNT-RESET`           `POST`     `/api/v1/demo-accounts/{account_id}/reset`           `demo.manage`               `scope:demo-account`    `REQUIRED`   `SYNC_SIMULATION`   `demo.account.reset`

  `TRIAL-ACCOUNT-CREATE`         `POST`     `/api/v1/trial-accounts`                             `trial.manage`              `scope:trial`           `REQUIRED`   `JOB`               `trial.created`

  `TRIAL-CONVERT`                `POST`     `/api/v1/trial-accounts/{account_id}/convert`        `trial.manage`              `scope:trial-account`   `REQUIRED`   `JOB`               `trial.converted`

  `TRIAL-EXTEND`                 `POST`     `/api/v1/trial-accounts/{account_id}/extend`         `trial.manage`              `scope:trial-account`   `REQUIRED`   `SYNC`              `trial.extended`
  --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

## Concurrency / idempotency

Toute mutation exige `If-Match` ou version de ressource lorsque la cible
existe. Les créations/job submissions acceptent une `Idempotency-Key`.
Un mismatch retourne un conflit sans mutation.

## DEMO

`SYNC_SIMULATION` n'est jamais routé vers le real-job dispatcher ou
`wcpd`.
