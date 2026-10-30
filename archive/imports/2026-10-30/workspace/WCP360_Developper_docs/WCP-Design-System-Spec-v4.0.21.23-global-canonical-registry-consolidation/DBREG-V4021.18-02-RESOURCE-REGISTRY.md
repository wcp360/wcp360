# V4.0.21.18 --- Database / Cache Resource Registry

  ------------------------------------------------------------------------------------------------------------------------------------
  Resource                         Owner                  Lifecycle                                      Secret class   Closure
  -------------------------------- ---------------------- ---------------------------------------------- -------------- --------------
  `database_instance_capability`   host                   AVAILABLE/DEGRADED/UNAVAILABLE                 no             READY

  `database`                       hosting_account        PROVISIONING/ACTIVE/SUSPENDED/ERROR/DELETING   no             READY

  `database_principal`             hosting_account        PROVISIONING/ACTIVE/LOCKED/DELETING            SECRET ref     READY

  `database_grant`                 database+principal     ACTIVE/REVOKED                                 security       READY

  `database_backup_ref`            database               PENDING/VERIFIED/FAILED/EXPIRED                security       READY

  `db_governor_mapping`            account/principal      ACTIVE/ERROR                                   security       READY

  `cache_instance`                 hosting_account/site   PROVISIONING/ACTIVE/SUSPENDED/ERROR/DELETING   SECRET ref     READY

  `cache_access_policy`            cache_instance         ACTIVE                                         security       READY

  `db_admin_sso_ticket`            principal/session      ISSUED/CONSUMED/EXPIRED/REVOKED                SECRET         READY
  ------------------------------------------------------------------------------------------------------------------------------------

Database names/users are canonical resources, not raw SQL fragments.
Passwords, tokens and SSO tickets are SecretStore-backed references or
ephemeral values.
