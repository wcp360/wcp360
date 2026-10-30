# V4.0.21.16 --- Core Security Resource Registry

## Ressources canoniques du noyau

  ------------------------------------------------------------------------------------------------------------------------------------------
  Resource                     Owner/scope             Lifecycle principal                                     Secret/PII     Closure
  ---------------------------- ----------------------- ------------------------------------------------------- -------------- --------------
  `principal`                  platform                ACTIVE/DISABLED                                         PII            READY

  `credential`                 principal               ACTIVE/REVOKED/EXPIRED                                  SECRET         READY

  `mfa_factor`                 principal               PENDING/ACTIVE/REVOKED                                  SECRET         READY

  `session`                    principal               ACTIVE/REVOKED/EXPIRED                                  SECRET         READY
                                                                                                               metadata       

  `role`                       platform/tenant         ACTIVE/DISABLED                                         no             READY

  `role_binding`               scoped actor            ACTIVE/REVOKED                                          no             READY

  `reseller`                   platform                PROVISIONING/ACTIVE/SUSPENDED/DELETING                  PII            READY

  `hosting_account`            reseller/platform       PROVISIONING/ACTIVE/SUSPENDED/NONCOMPLIANT/DELETING     PII            READY

  `hosting_plan`               reseller/platform       DRAFT/ACTIVE/RETIRED                                    no             READY

  `entitlement_set`            plan/account            ACTIVE                                                  no             READY

  `quota_policy`               plan/account            ACTIVE                                                  no             READY

  `isolation_profile`          platform/plan/account   ACTIVE/UNAVAILABLE                                      security       READY

  `system_identity`            hosting_account         PROVISIONING/ACTIVE/LOCKED/DELETING                     security       READY

  `managed_root`               hosting_account         ACTIVE/READONLY/DELETING                                security       READY

  `job`                        operation scope         QUEUED/RUNNING/SUCCEEDED/FAILED/CANCELLED/RECONCILING   security       READY

  `policy_decision`            operation               ALLOW/DENY/STEP_UP/APPROVAL/BLOCKED                     security       READY

  `privileged_action_record`   job                     PENDING/RUNNING/SUCCEEDED/FAILED                        security       READY
  ------------------------------------------------------------------------------------------------------------------------------------------

## Invariants

Tenant ownership is immutable except through an explicit migration
operation. Suspend does not delete. Plan downgrade may create
NONCOMPLIANT state but does not silently destroy resources. Secrets are
references, never ordinary fields.
