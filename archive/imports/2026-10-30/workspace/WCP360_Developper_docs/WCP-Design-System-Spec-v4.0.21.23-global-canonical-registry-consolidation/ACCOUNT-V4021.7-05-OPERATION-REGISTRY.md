# V4.0.21.7 --- Exact Account & Reseller Operation Registry

## Account operations

  -----------------------------------------------------------------------------------------------------
  Operation ID                    Capability            Scope             Step-up        Execution
  ------------------------------- --------------------- ----------------- -------------- --------------
  ACCOUNT-LIST                    account.read          tenant/reseller   no             NO_JOB

  ACCOUNT-READ                    account.read          account           no             NO_JOB

  ACCOUNT-CREATE                  account.create        tenant/reseller   policy         JOB

  ACCOUNT-UPDATE                  account.manage        account           conditional    NO_JOB/JOB by
                                                                                         field class

  ACCOUNT-SUSPEND                 account.suspend       account           REQUIRED       JOB

  ACCOUNT-RESUME                  account.suspend       account           REQUIRED       JOB

  ACCOUNT-DELETE-REQUEST          account.delete        account           REQUIRED       JOB

  ACCOUNT-RECONCILE               account.reconcile     account           REQUIRED admin JOB

  ACCOUNT-EFFECTIVE-POLICY-READ   account.policy.read   account           no             NO_JOB
  -----------------------------------------------------------------------------------------------------

## Reseller operations

  ------------------------------------------------------------------------------------------------
  Operation ID              Capability                Scope          Step-up        Execution
  ------------------------- ------------------------- -------------- -------------- --------------
  RESELLER-LIST             reseller.read             platform       no             NO_JOB

  RESELLER-CREATE           reseller.manage           platform       REQUIRED       JOB

  RESELLER-UPDATE           reseller.manage           reseller       conditional    NO_JOB

  RESELLER-SUSPEND          reseller.suspend          reseller       REQUIRED       JOB

  RESELLER-RESUME           reseller.suspend          reseller       REQUIRED       JOB

  RESELLER-CEILING-UPDATE   reseller.ceiling.manage   reseller       REQUIRED       JOB

  RESELLER-USAGE-READ       reseller.usage.read       reseller       no             NO_JOB
  ------------------------------------------------------------------------------------------------

All mutating operations are audited and versioned. Job-backed operations
must be idempotent.
