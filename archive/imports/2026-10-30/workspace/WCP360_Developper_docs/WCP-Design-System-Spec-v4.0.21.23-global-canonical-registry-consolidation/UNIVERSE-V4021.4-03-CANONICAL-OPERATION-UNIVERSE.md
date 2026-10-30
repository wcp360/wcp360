# V4.0.21.4 --- Canonical CORE Operation Universe

## B2 contract

This is the action universe that must be normalized into exact
API/internal Operation IDs. Candidate IDs below are inventory
identifiers until promoted into the authoritative API registry.

  -------------------------------------------------------------------------------------------------------------------------------------------------------------
  Candidate Operation ID                                 Module            Action                                                 B2 state
  ------------------------------------------------------ ----------------- ------------------------------------------------------ -----------------------------
  `IDENTITY-LOGIN-LOGOUT`                                `identity`        login/logout                                           **REQUIRES_EXACT_ROW**

  `IDENTITY-SESSION-REVOKE`                              `identity`        session revoke                                         **REQUIRES_EXACT_ROW**

  `IDENTITY-TOKEN-CREATE-REVOKE`                         `identity`        token create/revoke                                    **REQUIRES_EXACT_ROW**

  `IDENTITY-MFA-ENROLL-DISABLE-RECOVERY`                 `identity`        MFA enroll/disable/recovery                            **REQUIRES_EXACT_ROW**

  `AUTHORIZATION-BIND-UNBIND-ROLE`                       `authorization`   bind/unbind role                                       **REQUIRES_EXACT_ROW**

  `AUTHORIZATION-DELEGATE`                               `authorization`   delegate                                               **REQUIRES_EXACT_ROW**

  `AUTHORIZATION-IMPERSONATE-START-STOP`                 `authorization`   impersonate start/stop                                 **REQUIRES_EXACT_ROW**

  `ACCOUNTS-CREATE`                                      `accounts`        create                                                 **REQUIRES_EXACT_ROW**

  `ACCOUNTS-UPDATE`                                      `accounts`        update                                                 **REQUIRES_EXACT_ROW**

  `ACCOUNTS-SUSPEND`                                     `accounts`        suspend                                                **REQUIRES_EXACT_ROW**

  `ACCOUNTS-RESUME`                                      `accounts`        resume                                                 **REQUIRES_EXACT_ROW**

  `ACCOUNTS-DELETE`                                      `accounts`        delete                                                 **REQUIRES_EXACT_ROW**

  `ACCOUNTS-RECONCILE`                                   `accounts`        reconcile                                              **REQUIRES_EXACT_ROW**

  `RESELLER-CREATE-UPDATE`                               `reseller`        create/update                                          **PARTIAL_EXISTING_FAMILY**

  `RESELLER-CEILING-UPDATE`                              `reseller`        ceiling update                                         **PARTIAL_EXISTING_FAMILY**

  `RESELLER-SUSPEND-RESUME`                              `reseller`        suspend/resume                                         **PARTIAL_EXISTING_FAMILY**

  `RESELLER-DELETE`                                      `reseller`        delete                                                 **PARTIAL_EXISTING_FAMILY**

  `PLANS-CREATE-UPDATE-CLONE-ARCHIVE`                    `plans`           create/update/clone/archive                            **REQUIRES_EXACT_ROW**

  `PLANS-ASSIGN`                                         `plans`           assign                                                 **REQUIRES_EXACT_ROW**

  `PLANS-OVERRIDE-SET-RESET`                             `plans`           override set/reset                                     **REQUIRES_EXACT_ROW**

  `WEB-CREATE-UPDATE-DELETE`                             `web`             create/update/delete                                   **REQUIRES_EXACT_ROW**

  `WEB-ATTACH-DETACH-DOMAIN`                             `web`             attach/detach domain                                   **REQUIRES_EXACT_ROW**

  `WEB-RECONCILE`                                        `web`             reconcile                                              **REQUIRES_EXACT_ROW**

  `WEBSERVER-PROFILE-SELECT-UPDATE`                      `webserver`       profile select/update                                  **REQUIRES_EXACT_ROW**

  `WEBSERVER-CUSTOM-DIRECTIVES-VALIDATE-APPLY-RESET`     `webserver`       custom directives validate/apply/reset                 **REQUIRES_EXACT_ROW**

  `WEBSERVER-RELOAD-RECONCILE`                           `webserver`       reload/reconcile                                       **REQUIRES_EXACT_ROW**

  `PHP-VERSION-SELECT`                                   `php`             version select                                         **REQUIRES_EXACT_ROW**

  `PHP-LIMITS-UPDATE`                                    `php`             limits update                                          **REQUIRES_EXACT_ROW**

  `PHP-EXTENSION-SET`                                    `php`             extension set                                          **REQUIRES_EXACT_ROW**

  `PHP-POOL-RESTART-RECONCILE`                           `php`             pool restart/reconcile                                 **REQUIRES_EXACT_ROW**

  `TLS-ISSUE-RENEW-REVOKE-VERIFY`                        `tls`             issue/renew/revoke/verify                              **REQUIRES_EXACT_ROW**

  `TLS-POLICY-UPDATE`                                    `tls`             policy update                                          **REQUIRES_EXACT_ROW**

  `DNS-ZONE-CRUD-RECONCILE`                              `dns`             zone CRUD/reconcile                                    **PARTIAL_EXISTING_FAMILY**

  `DNS-RECORD-CRUD`                                      `dns`             record CRUD                                            **PARTIAL_EXISTING_FAMILY**

  `DNS-DNSSEC-ENABLE-DISABLE-ROTATE`                     `dns`             DNSSEC enable/disable/rotate                           **PARTIAL_EXISTING_FAMILY**

  `DATABASE-DATABASE-CREATE-DELETE-BACKUP-RESTORE`       `database`        database create/delete/backup/restore                  **REQUIRES_EXACT_ROW**

  `DATABASE-USER-CREATE-ROTATE-DELETE`                   `database`        user create/rotate/delete                              **REQUIRES_EXACT_ROW**

  `DATABASE-GRANT-UPDATE`                                `database`        grant update                                           **REQUIRES_EXACT_ROW**

  `CACHE-CREATE-UPDATE-RESTART-DELETE`                   `cache`           create/update/restart/delete                           **REQUIRES_EXACT_ROW**

  `CACHE-CREDENTIAL-ROTATE`                              `cache`           credential rotate                                      **REQUIRES_EXACT_ROW**

  `MAIL-DOMAIN-MAILBOX-CRUD`                             `mail`            domain/mailbox CRUD                                    **REQUIRES_EXACT_ROW**

  `MAIL-ALIAS-FORWARDER-CRUD`                            `mail`            alias/forwarder CRUD                                   **REQUIRES_EXACT_ROW**

  `MAIL-AUTORESPONDER-UPDATE`                            `mail`            autoresponder update                                   **REQUIRES_EXACT_ROW**

  `MAIL_SECURITY-SPAM-POLICY-UPDATE`                     `mail_security`   spam policy update                                     **REQUIRES_EXACT_ROW**

  `MAIL_SECURITY-FILTER-CRUD`                            `mail_security`   filter CRUD                                            **REQUIRES_EXACT_ROW**

  `MAIL_SECURITY-DKIM-ROTATE`                            `mail_security`   DKIM rotate                                            **REQUIRES_EXACT_ROW**

  `MAIL_SECURITY-DMARC-MTA-STS-TLS-RPT-UPDATE`           `mail_security`   DMARC/MTA-STS/TLS-RPT update                           **REQUIRES_EXACT_ROW**

  `WEBMAIL-ISSUE-ONE-TIME-SSO-TICKET`                    `webmail`         issue one-time SSO ticket                              **REQUIRES_EXACT_ROW**

  `WEBMAIL-REVOKE-SESSION`                               `webmail`         revoke session                                         **REQUIRES_EXACT_ROW**

  `FILES-LIST-READ-WRITE-MOVE-COPY-DELETE`               `files`           list/read/write/move/copy/delete                       **REQUIRES_EXACT_ROW**

  `FILES-ARCHIVE-EXTRACT`                                `files`           archive/extract                                        **REQUIRES_EXACT_ROW**

  `FILES-QUOTA-UPDATE`                                   `files`           quota update                                           **REQUIRES_EXACT_ROW**

  `ACCESS-PRINCIPAL-CRUD`                                `access`          principal CRUD                                         **REQUIRES_EXACT_ROW**

  `ACCESS-KEY-CRUD`                                      `access`          key CRUD                                               **REQUIRES_EXACT_ROW**

  `ACCESS-ENABLE-DISABLE`                                `access`          enable/disable                                         **REQUIRES_EXACT_ROW**

  `ACCESS-PASSWORD-KEY-ROTATE`                           `access`          password/key rotate                                    **REQUIRES_EXACT_ROW**

  `GIT-REPO-CRUD`                                        `git`             repo CRUD                                              **REQUIRES_EXACT_ROW**

  `GIT-DEPLOY`                                           `git`             deploy                                                 **REQUIRES_EXACT_ROW**

  `GIT-KEY-ROTATE`                                       `git`             key rotate                                             **REQUIRES_EXACT_ROW**

  `GIT-WEBHOOK-CRUD`                                     `git`             webhook CRUD                                           **REQUIRES_EXACT_ROW**

  `APPS-CATALOG-READ`                                    `apps`            catalog/read                                           **REQUIRES_EXACT_ROW**

  `APPS-INSTALL-UPDATE-CLONE-DELETE`                     `apps`            install/update/clone/delete                            **REQUIRES_EXACT_ROW**

  `WORDPRESS-INSTALL-IMPORT`                             `wordpress`       install/import                                         **REQUIRES_EXACT_ROW**

  `WORDPRESS-UPDATE`                                     `wordpress`       update                                                 **REQUIRES_EXACT_ROW**

  `WORDPRESS-CLONE-STAGE-SYNC`                           `wordpress`       clone/stage/sync                                       **REQUIRES_EXACT_ROW**

  `WORDPRESS-HARDEN`                                     `wordpress`       harden                                                 **REQUIRES_EXACT_ROW**

  `WORDPRESS-PLUGIN-THEME-OPERATIONS`                    `wordpress`       plugin/theme operations                                **REQUIRES_EXACT_ROW**

  `NODE-CREATE-UPDATE-RESTART-DELETE`                    `node`            create/update/restart/delete                           **PARTIAL_EXISTING_FAMILY**

  `NODE-VERSION-ENV-UPDATE`                              `node`            version/env update                                     **PARTIAL_EXISTING_FAMILY**

  `PYTHON-CREATE-UPDATE-RESTART-DELETE`                  `python`          create/update/restart/delete                           **REQUIRES_EXACT_ROW**

  `PYTHON-VENV-ENV-UPDATE`                               `python`          venv/env update                                        **REQUIRES_EXACT_ROW**

  `RUBY-CREATE-UPDATE-RESTART-DELETE`                    `ruby`            create/update/restart/delete                           **REQUIRES_EXACT_ROW**

  `RUBY-VERSION-ENV-UPDATE`                              `ruby`            version/env update                                     **REQUIRES_EXACT_ROW**

  `GO-CREATE-UPDATE-RESTART-DELETE`                      `go`              create/update/restart/delete                           **REQUIRES_EXACT_ROW**

  `GO-ENV-UPDATE`                                        `go`              env update                                             **REQUIRES_EXACT_ROW**

  `CRON-CRUD`                                            `cron`            CRUD                                                   **REQUIRES_EXACT_ROW**

  `CRON-ENABLE-DISABLE`                                  `cron`            enable/disable                                         **REQUIRES_EXACT_ROW**

  `CRON-RUN-NOW`                                         `cron`            run-now                                                **REQUIRES_EXACT_ROW**

  `WORKERS-CRUD`                                         `workers`         CRUD                                                   **REQUIRES_EXACT_ROW**

  `WORKERS-START-STOP-RESTART`                           `workers`         start/stop/restart                                     **REQUIRES_EXACT_ROW**

  `WORKERS-LOGS`                                         `workers`         logs                                                   **REQUIRES_EXACT_ROW**

  `BACKUP-POLICY-CRUD`                                   `backup`          policy CRUD                                            **PARTIAL_EXISTING_FAMILY**

  `BACKUP-RUN`                                           `backup`          run                                                    **PARTIAL_EXISTING_FAMILY**

  `BACKUP-RESTORE`                                       `backup`          restore                                                **PARTIAL_EXISTING_FAMILY**

  `BACKUP-VERIFY`                                        `backup`          verify                                                 **PARTIAL_EXISTING_FAMILY**

  `BACKUP-DELETE-ARTIFACT`                               `backup`          delete artifact                                        **PARTIAL_EXISTING_FAMILY**

  `SECURITY-SCAN`                                        `security`        scan                                                   **REQUIRES_EXACT_ROW**

  `SECURITY-REPAIR`                                      `security`        repair                                                 **REQUIRES_EXACT_ROW**

  `SECURITY-QUARANTINE-RELEASE`                          `security`        quarantine/release                                     **REQUIRES_EXACT_ROW**

  `SECURITY-HARDENING-APPLY`                             `security`        hardening apply                                        **REQUIRES_EXACT_ROW**

  `FIREWALL-RULE-CRUD`                                   `firewall`        rule CRUD                                              **REQUIRES_EXACT_ROW**

  `FIREWALL-BLOCK-UNBLOCK`                               `firewall`        block/unblock                                          **REQUIRES_EXACT_ROW**

  `FIREWALL-POLICY-UPDATE`                               `firewall`        policy update                                          **REQUIRES_EXACT_ROW**

  `MONITORING-READ-METRICS`                              `monitoring`      read metrics                                           **REQUIRES_EXACT_ROW**

  `MONITORING-HEALTH-EVALUATE`                           `monitoring`      health evaluate                                        **REQUIRES_EXACT_ROW**

  `MONITORING-ALERT-RULE-CRUD`                           `monitoring`      alert rule CRUD                                        **REQUIRES_EXACT_ROW**

  `LOGS-SEARCH-READ`                                     `logs`            search/read                                            **REQUIRES_EXACT_ROW**

  `LOGS-EXPORT`                                          `logs`            export                                                 **REQUIRES_EXACT_ROW**

  `LOGS-RETENTION-POLICY`                                `logs`            retention policy                                       **REQUIRES_EXACT_ROW**

  `NOTIFICATIONS-ROUTE-CRUD-TEST`                        `notifications`   route CRUD/test                                        **REQUIRES_EXACT_ROW**

  `NOTIFICATIONS-TEMPLATE-UPDATE`                        `notifications`   template update                                        **REQUIRES_EXACT_ROW**

  `JOBS-READ-LIST`                                       `jobs`            read/list                                              **REQUIRES_EXACT_ROW**

  `JOBS-RETRY`                                           `jobs`            retry                                                  **REQUIRES_EXACT_ROW**

  `JOBS-CANCEL`                                          `jobs`            cancel                                                 **REQUIRES_EXACT_ROW**

  `DEMO-POLICY-CRUD`                                     `demo`            policy CRUD                                            **PARTIAL_EXISTING_FAMILY**

  `DEMO-ACCOUNT-CREATE-RESET-DELETE`                     `demo`            account create/reset/delete                            **PARTIAL_EXISTING_FAMILY**

  `DEMO-SIMULATED-ACTIONS`                               `demo`            simulated actions                                      **PARTIAL_EXISTING_FAMILY**

  `TRIAL-CREATE`                                         `trial`           create                                                 **PARTIAL_EXISTING_FAMILY**

  `TRIAL-EXTEND`                                         `trial`           extend                                                 **PARTIAL_EXISTING_FAMILY**

  `TRIAL-CONVERT`                                        `trial`           convert                                                **PARTIAL_EXISTING_FAMILY**

  `TRIAL-EXPIRE-SUSPEND`                                 `trial`           expire/suspend                                         **PARTIAL_EXISTING_FAMILY**

  `API-TOKEN-CRUD`                                       `api`             token CRUD                                             **REQUIRES_EXACT_ROW**

  `API-WEBHOOK-CRUD-TEST`                                `api`             webhook CRUD/test                                      **REQUIRES_EXACT_ROW**

  `API-AUTOMATION-CRUD`                                  `api`             automation CRUD                                        **REQUIRES_EXACT_ROW**

  `BILLING-CONNECTOR-CONFIGURE-TEST`                     `billing`         connector configure/test                               **REQUIRES_EXACT_ROW**

  `BILLING-PROVISION-SUSPEND-UNSUSPEND-TERMINATE-CHAN`   `billing`         provision/suspend/unsuspend/terminate/change-package   **REQUIRES_EXACT_ROW**

  `MIGRATION-SOURCE-VALIDATE`                            `migration`       source validate                                        **REQUIRES_EXACT_ROW**

  `MIGRATION-DISCOVER`                                   `migration`       discover                                               **REQUIRES_EXACT_ROW**

  `MIGRATION-MIGRATE`                                    `migration`       migrate                                                **REQUIRES_EXACT_ROW**

  `MIGRATION-RETRY`                                      `migration`       retry                                                  **REQUIRES_EXACT_ROW**

  `MIGRATION-REPORT`                                     `migration`       report                                                 **REQUIRES_EXACT_ROW**

  `SYSTEM-STATUS`                                        `system`          status                                                 **REQUIRES_EXACT_ROW**

  `SYSTEM-START-STOP-RESTART-RELOAD`                     `system`          start/stop/restart/reload                              **REQUIRES_EXACT_ROW**

  `SYSTEM-RECONCILE`                                     `system`          reconcile                                              **REQUIRES_EXACT_ROW**

  `SYSTEM-PACKAGE-UPDATE-OPERATIONS`                     `system`          package/update operations                              **REQUIRES_EXACT_ROW**

  `INSTALLER-PREFLIGHT`                                  `installer`       preflight                                              **REQUIRES_EXACT_ROW**

  `INSTALLER-INSTALL-PHASE`                              `installer`       install phase                                          **REQUIRES_EXACT_ROW**

  `INSTALLER-FINALIZE`                                   `installer`       finalize                                               **REQUIRES_EXACT_ROW**

  `INSTALLER-VERIFY`                                     `installer`       verify                                                 **REQUIRES_EXACT_ROW**

  `INSTALLER-RESUME-ROLLBACK`                            `installer`       resume/rollback                                        **REQUIRES_EXACT_ROW**

  `UPDATES-CHECK`                                        `updates`         check                                                  **REQUIRES_EXACT_ROW**

  `UPDATES-PLAN`                                         `updates`         plan                                                   **REQUIRES_EXACT_ROW**

  `UPDATES-APPLY`                                        `updates`         apply                                                  **REQUIRES_EXACT_ROW**

  `UPDATES-ROLLBACK`                                     `updates`         rollback                                               **REQUIRES_EXACT_ROW**

  `UPDATES-CANARY`                                       `updates`         canary                                                 **REQUIRES_EXACT_ROW**

  `UPDATES-SCHEDULE`                                     `updates`         schedule                                               **REQUIRES_EXACT_ROW**

  `SECRETS-CREATE-REFERENCE`                             `secrets`         create reference                                       **REQUIRES_EXACT_ROW**

  `SECRETS-ROTATE`                                       `secrets`         rotate                                                 **REQUIRES_EXACT_ROW**

  `SECRETS-REVOKE`                                       `secrets`         revoke                                                 **REQUIRES_EXACT_ROW**

  `SECRETS-METADATA-READ`                                `secrets`         metadata read                                          **REQUIRES_EXACT_ROW**

  `QUOTAS-QUOTA-UPDATE`                                  `quotas`          quota update                                           **REQUIRES_EXACT_ROW**

  `QUOTAS-USAGE-READ`                                    `quotas`          usage read                                             **REQUIRES_EXACT_ROW**

  `QUOTAS-ISOLATION-PROFILE-APPLY`                       `quotas`          isolation profile apply                                **REQUIRES_EXACT_ROW**

  `BRANDING-READ-UPDATE-RESET`                           `branding`        read/update/reset                                      **REQUIRES_EXACT_ROW**

  `SEARCH-SEARCH-READ-ONLY`                              `search`          search/read only                                       **REQUIRES_EXACT_ROW**

  `DEMO_TRIAL_UI-EFFECTIVE-MODULE-STATE-READ`            `demo_trial_ui`   effective module state read                            **REQUIRES_EXACT_ROW**

  `DEMO_TRIAL_UI-ADMIN-EXPOSURE-UPDATE`                  `demo_trial_ui`   admin exposure update                                  **REQUIRES_EXACT_ROW**
  -------------------------------------------------------------------------------------------------------------------------------------------------------------

## Promotion requirements

Before an action becomes an authoritative Operation ID it must have:
method/path or internal action identifier; request/response resource;
capability; scope; entitlement; step-up; idempotency/concurrency;
execution class; audit action; error contract.

## Rule

UI buttons, CLI actions, billing callbacks and automation triggers must
resolve to the same canonical operation contract where they represent
the same business action.

## V4.0.21.4.1 isolation additions
See `ISOLATION-V4021.4.1-07-COMMANDS-OPERATIONS.md`; these IDs must be normalized during B2 closure.

## V4.0.21.5 status
Isolation/quota candidate actions are normalized into exact B2 rows in `ISOLATION-V4021.5-06-EXACT-API-OPERATIONS.md`.
