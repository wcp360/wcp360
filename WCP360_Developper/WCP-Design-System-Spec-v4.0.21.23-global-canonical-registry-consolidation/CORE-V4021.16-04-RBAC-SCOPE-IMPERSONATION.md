# V4.0.21.16 --- RBAC / Scope / Impersonation Registry

## Authorization

Effective access = permission ∩ scope ∩ entitlement ∩ capability ∩
lifecycle ∩ security floor.

  ---------------------------------------------------------------------------------------------
  Operation               Permission            Scope             Step-up        Closure
  ----------------------- --------------------- ----------------- -------------- --------------
  `ROLE-CREATE`           rbac.role.create      tenant/platform   yes            READY

  `ROLE-UPDATE`           rbac.role.update      owned role        yes            READY

  `ROLE-DELETE`           rbac.role.delete      owned role        yes            READY

  `ROLE-BIND`             rbac.binding.create   actor+target      yes            READY
                                                scope                            

  `ROLE-UNBIND`           rbac.binding.delete   actor+target      yes            READY
                                                scope                            

  `IMPERSONATION-START`   support.impersonate   subordinate       yes            READY
                                                tenant/account                   

  `IMPERSONATION-STOP`    support.impersonate   active chain      no             READY
  ---------------------------------------------------------------------------------------------

## Impersonation invariants

Initiator identity is never replaced. Audit records initiator +
effective actor. Impersonation cannot exceed initiator permission,
target scope or security floor. Credential/MFA/security-sensitive
actions may be explicitly denied while impersonating.
