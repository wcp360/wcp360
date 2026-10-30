# V4.0.21.20 --- Files / Access / Runtime Resource Registry

  --------------------------------------------------------------------------------------------------------------------------
  Resource               Owner               Lifecycle                                      Security          Closure
  ---------------------- ------------------- ---------------------------------------------- ----------------- --------------
  `file_object_ref`      hosting_account     PRESENT/MISSING/QUARANTINED                    path-sensitive    READY

  `managed_archive`      hosting_account     STAGED/READY/EXTRACTED/EXPIRED                 hostile-input     READY

  `access_identity`      account/principal   ACTIVE/LOCKED/REVOKED                          security          READY

  `ssh_public_key`       access_identity     ACTIVE/REVOKED                                 public+metadata   READY

  `access_policy`        account             ACTIVE                                         security          READY

  `git_repository`       account/site        PROVISIONING/ACTIVE/ERROR/DELETING             secret refs       READY

  `deployment`           site/app            QUEUED/RUNNING/SUCCEEDED/FAILED/ROLLED_BACK    security          READY

  `application`          site/account        PROVISIONING/ACTIVE/SUSPENDED/ERROR/DELETING   security          READY

  `runtime_assignment`   application/site    ACTIVE/UNAVAILABLE                             no                READY

  `dependency_job`       application         QUEUED/RUNNING/SUCCEEDED/FAILED                supply-chain      READY

  `app_secret`           application         ACTIVE/ROTATING/REVOKED                        SECRET            READY

  `scheduled_task`       account/app         ACTIVE/PAUSED/ERROR                            execution         READY

  `worker`               application         PROVISIONING/ACTIVE/STOPPED/ERROR              execution         READY

  `app_queue`            application         ACTIVE/DEGRADED                                data-sensitive    READY

  `wordpress_instance`   site                DISCOVERED/MANAGED/ACTIVE/ERROR                security          READY
  --------------------------------------------------------------------------------------------------------------------------

All paths are resolved relative to an authorized managed root unless an
internal privileged action owns the path. Secrets are references, never
ordinary config fields.
