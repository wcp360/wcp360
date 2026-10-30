# V4.0.21.16 --- Reseller / Account / Plan Registry

  ---------------------------------------------------------------------------------------------------------
  Operation               Resource          Permission          Scope               Job         Closure
  ----------------------- ----------------- ------------------- ------------------- ----------- -----------
  `RESELLER-CREATE`       reseller          reseller.create     platform            REQUIRED    READY

  `RESELLER-SUSPEND`      reseller          reseller.suspend    platform            REQUIRED    READY

  `ACCOUNT-CREATE`        hosting_account   account.create      reseller/platform   REQUIRED    READY

  `ACCOUNT-SUSPEND`       hosting_account   account.suspend     owner scope         REQUIRED    READY

  `ACCOUNT-UNSUSPEND`     hosting_account   account.unsuspend   owner scope         REQUIRED    READY

  `ACCOUNT-DELETE`        hosting_account   account.delete      owner scope         REQUIRED    READY

  `PLAN-CREATE`           hosting_plan      plan.create         reseller/platform   NO_JOB      READY

  `PLAN-UPDATE`           hosting_plan      plan.update         owner scope         NO_JOB      READY

  `PLAN-RETIRE`           hosting_plan      plan.retire         owner scope         NO_JOB      READY

  `ACCOUNT-PLAN-ASSIGN`   hosting_account   plan.assign         owner scope         REQUIRED    READY
  ---------------------------------------------------------------------------------------------------------

Account creation/deletion Jobs orchestrate child resources through typed
actions. Plan changes calculate compliance impact before applying
ceilings.
