# V4.0.21.2 --- Exact Acceptance Bindings

## Statut

**NORMATIVE**

  -------------------------------------------------------------------------------------------------------------------------------------------------------------
  Test ID           Operation                     Job                            Decision                           Oracle
  ----------------- ----------------------------- ------------------------------ ---------------------------------- -------------------------------------------
  `E2E-PLAN-001`    `PLAN-ASSIGN`                 `plan.assignment.reconcile`    `DEC-PLAN-IMPACT-001`              `assigned plan effective`

  `E2E-PLAN-002`    `ENTITLEMENT-OVERRIDE-SET`    `none`                         `effective entitlement resolver`   `only target account changed`

  `SEC-PLAN-003`    `RESELLER-CEILING-UPDATE`     `reseller.ceiling.reconcile`   `DEC-PLAN-IMPACT-001`              `no descendant exceeds ceiling`

  `SEC-PLAN-004`    `ENTITLEMENT-OVERRIDE-SET`    `none`                         `security floor`                   `unsafe grant rejected`

  `E2E-PLAN-005`    `PLAN-ASSIGN`                 `plan.assignment.reconcile`    `DEC-PLAN-IMPACT-001`              `NONCOMPLIANT and no deletion`

  `E2E-DEMO-001`    `DEMO-ACCOUNT-CREATE`         `none`                         `DEC-DEMO-ROUTE-001`               `zero real Job/RPC`

  `SEC-DEMO-002`    `simulated module mutation`   `none`                         `DEC-DEMO-ROUTE-001`               `direct API cannot reach real dispatcher`

  `SEC-DEMO-003`    `DEMO-ACCOUNT-RESET`          `none`                         `DEC-DEMO-ROUTE-001`               `zero wcpd/provider mutation`

  `E2E-TRIAL-001`   `TRIAL-ACCOUNT-CREATE`        `trial.account.provision`      `DEC-TRIAL-PROVISION-001`          `real resources within trial ceiling`

  `E2E-TRIAL-003`   `TRIAL-CONVERT`               `trial.account.convert`        `DEC-PLAN-IMPACT-001`              `compatible resources retained`
  -------------------------------------------------------------------------------------------------------------------------------------------------------------

No acceptance row may use only an action family when an Operation ID
exists.
