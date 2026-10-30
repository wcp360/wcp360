# V4.0.16 --- Dependency & Trust Boundaries

## Trust zones

1.  Browser/operator/user.
2.  HTTP edge / panel TLS.
3.  PHP control-plane non-root.
4.  PostgreSQL control-plane.
5.  Privileged local IPC boundary.
6.  Go `wcpd` root boundary.
7.  Tenant processes/data.
8.  Managed infrastructure services.
9.  External providers/internet.

## Interdictions

-   Browser → wcpd direct : interdit.
-   Tenant → control-plane DB : interdit.
-   PHP → arbitrary root shell : interdit.
-   External webhook/provider → privileged mutation sans service
    validation/Job : interdit.
-   Managed service config → overwrite global unmanaged config :
    interdit.

## Dependencies

Identity/RBAC → API/service → desired state → Job → privileged adapter →
observed state → event/audit. Backup, secrets, observability et recovery
sont transversaux.
