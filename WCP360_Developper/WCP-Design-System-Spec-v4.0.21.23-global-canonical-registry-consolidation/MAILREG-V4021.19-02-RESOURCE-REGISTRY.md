# V4.0.21.19 --- Mail Resource Registry

  -----------------------------------------------------------------------------------------------------------------------------------
  Resource                Owner                    Lifecycle                                       Security            Closure
  ----------------------- ------------------------ ----------------------------------------------- ------------------- --------------
  `mail_domain`           hosting_account/domain   PROVISIONING/ACTIVE/SUSPENDED/ERROR/DELETING    scoped              READY

  `mailbox`               mail_domain              PROVISIONING/ACTIVE/LOCKED/OVERQUOTA/DELETING   SECRET ref          READY

  `mail_alias`            mail_domain              ACTIVE/DISABLED                                 scoped              READY

  `mail_forward`          mailbox/domain           ACTIVE/DISABLED                                 abuse-sensitive     READY

  `mail_autoresponder`    mailbox                  ACTIVE/DISABLED                                 content-sensitive   READY

  `sieve_policy`          mailbox/domain           ACTIVE/ERROR                                    content-sensitive   READY

  `mail_quota_policy`     mailbox/account          ACTIVE                                          security            READY

  `dkim_key`              mail_domain              PENDING/ACTIVE/ROTATING/RETIRED/ERROR           PRIVATE KEY         READY

  `mail_auth_policy`      domain                   ACTIVE/DEGRADED                                 security            READY

  `spam_policy`           account/domain/mailbox   ACTIVE                                          security            READY

  `mail_queue_item`       host/domain scope        QUEUED/DEFERRED/FROZEN/DELIVERED/BOUNCED        privacy             READY

  `mail_trace`            message metadata         ACTIVE/EXPIRED                                  privacy             READY

  `webmail_sso_ticket`    session/mailbox          ISSUED/CONSUMED/EXPIRED/REVOKED                 SECRET              READY

  `mail_archive_policy`   domain/mailbox           ACTIVE/DISABLED                                 privacy             READY
  -----------------------------------------------------------------------------------------------------------------------------------

Message bodies are not normal control-plane resources. Their access is
minimized, permissioned and never included in ordinary audit/event
payloads.
