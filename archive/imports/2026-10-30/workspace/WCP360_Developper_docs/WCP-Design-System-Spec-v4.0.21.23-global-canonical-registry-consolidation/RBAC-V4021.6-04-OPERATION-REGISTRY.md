# V4.0.21.6 --- Exact RBAC/Delegation/Impersonation Operation Registry

## Statut

**NORMATIVE B2 --- AUTHORIZATION**

  ------------------------------------------------------------------------------------------------------
  Operation ID                 Capability                Scope             Step-up        Execution
  ---------------------------- ------------------------- ----------------- -------------- --------------
  ROLE-LIST                    rbac.read                 tenant/reseller   no             NO_JOB

  ROLE-CREATE                  rbac.manage               tenant/reseller   REQUIRED       NO_JOB

  ROLE-UPDATE                  rbac.manage               role scope        REQUIRED       NO_JOB

  ROLE-ARCHIVE                 rbac.manage               role scope        REQUIRED       NO_JOB

  ROLE-PERMISSION-SET          rbac.permissions.manage   role scope        REQUIRED       NO_JOB

  ROLE-BIND                    rbac.binding.manage       target scope      REQUIRED       NO_JOB

  ROLE-UNBIND                  rbac.binding.manage       target scope      REQUIRED       NO_JOB

  DELEGATION-CREATE            delegation.manage         bounded scope     REQUIRED       NO_JOB

  DELEGATION-REVOKE            delegation.manage         bounded scope     REQUIRED       NO_JOB

  EFFECTIVE-PERMISSIONS-READ   rbac.explain              target scope      conditional    NO_JOB

  AUTHZ-EXPLAIN                rbac.explain              target scope      conditional    NO_JOB

  IMPERSONATION-START          support.impersonate       target scope      REQUIRED       NO_JOB

  IMPERSONATION-STOP           support.impersonate       active session    no             NO_JOB
  ------------------------------------------------------------------------------------------------------

## Impersonation restrictions

Persistent banner, explicit reason, short expiry, initiator retained in
every audit event, and restricted actions. Secret export, MFA reset,
security-floor weakening, ownership transfer and destructive
infrastructure actions are denied by default while impersonating unless
a separate explicit emergency policy exists.
