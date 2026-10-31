# V4.0.21.4 --- Canonical CORE Resource Universe

## B1 contract

Every row below must end in `EXISTING_DICTIONARY` or receive a new
canonical logical field dictionary before B1 can PASS.

  ---------------------------------------------------------------------------------------------
  Resource                     Module            Family               B1 state
  ---------------------------- ----------------- -------------------- -------------------------
  `tenant`                     `identity`        Identity /           **EXISTING_DICTIONARY**
                                                 Authentication       

  `user`                       `identity`        Identity /           **EXISTING_DICTIONARY**
                                                 Authentication       

  `session`                    `identity`        Identity /           **DICTIONARY_REQUIRED**
                                                 Authentication       

  `api_token`                  `identity`        Identity /           **EXISTING_DICTIONARY**
                                                 Authentication       

  `mfa_factor`                 `identity`        Identity /           **DICTIONARY_REQUIRED**
                                                 Authentication       

  `role`                       `authorization`   RBAC / Scope /       **DICTIONARY_REQUIRED**
                                                 Impersonation        

  `role_binding`               `authorization`   RBAC / Scope /       **EXISTING_DICTIONARY**
                                                 Impersonation        

  `delegation`                 `authorization`   RBAC / Scope /       **DICTIONARY_REQUIRED**
                                                 Impersonation        

  `impersonation_session`      `authorization`   RBAC / Scope /       **DICTIONARY_REQUIRED**
                                                 Impersonation        

  `hosting_account`            `accounts`        Hosting Accounts     **EXISTING_DICTIONARY**

  `system_identity`            `accounts`        Hosting Accounts     **DICTIONARY_REQUIRED**

  `reseller`                   `reseller`        Reseller /           **EXISTING_DICTIONARY**
                                                 Multi-tenant         

  `reseller_ceiling`           `reseller`        Reseller /           **DICTIONARY_REQUIRED**
                                                 Multi-tenant         

  `hosting_plan`               `plans`           Plans / Packages /   **EXISTING_DICTIONARY**
                                                 Entitlements         

  `plan_assignment`            `plans`           Plans / Packages /   **EXISTING_DICTIONARY**
                                                 Entitlements         

  `entitlement`                `plans`           Plans / Packages /   **EXISTING_DICTIONARY**
                                                 Entitlements         

  `entitlement_override`       `plans`           Plans / Packages /   **EXISTING_DICTIONARY**
                                                 Entitlements         

  `domain`                     `web`             Websites / Domains / **EXISTING_DICTIONARY**
                                                 VHosts               

  `site`                       `web`             Websites / Domains / **EXISTING_DICTIONARY**
                                                 VHosts               

  `vhost_config`               `web`             Websites / Domains / **DICTIONARY_REQUIRED**
                                                 VHosts               

  `webserver_profile`          `webserver`       Nginx / Apache /     **DICTIONARY_REQUIRED**
                                                 Proxy                

  `webserver_override`         `webserver`       Nginx / Apache /     **DICTIONARY_REQUIRED**
                                                 Proxy                

  `runtime_profile`            `php`             PHP / PHP-FPM        **EXISTING_DICTIONARY**

  `php_pool`                   `php`             PHP / PHP-FPM        **DICTIONARY_REQUIRED**

  `php_extension_set`          `php`             PHP / PHP-FPM        **DICTIONARY_REQUIRED**

  `certificate`                `tls`             TLS / ACME           **EXISTING_DICTIONARY**

  `acme_order`                 `tls`             TLS / ACME           **DICTIONARY_REQUIRED**

  `tls_policy`                 `tls`             TLS / ACME           **EXISTING_DICTIONARY**

  `dns_zone`                   `dns`             DNS / DNSSEC         **EXISTING_DICTIONARY**

  `dns_record`                 `dns`             DNS / DNSSEC         **EXISTING_DICTIONARY**

  `dnssec_keyset`              `dns`             DNS / DNSSEC         **DICTIONARY_REQUIRED**

  `database`                   `database`        MariaDB / MySQL /    **EXISTING_DICTIONARY**
                                                 PostgreSQL           

  `database_user`              `database`        MariaDB / MySQL /    **EXISTING_DICTIONARY**
                                                 PostgreSQL           

  `database_grant`             `database`        MariaDB / MySQL /    **DICTIONARY_REQUIRED**
                                                 PostgreSQL           

  `cache_instance`             `cache`           Redis / Memcached    **DICTIONARY_REQUIRED**

  `cache_credential`           `cache`           Redis / Memcached    **DICTIONARY_REQUIRED**

  `mail_domain`                `mail`            Mail Domains /       **EXISTING_DICTIONARY**
                                                 Mailboxes / Aliases  

  `mailbox`                    `mail`            Mail Domains /       **EXISTING_DICTIONARY**
                                                 Mailboxes / Aliases  

  `mail_alias`                 `mail`            Mail Domains /       **EXISTING_DICTIONARY**
                                                 Mailboxes / Aliases  

  `mail_forwarder`             `mail`            Mail Domains /       **DICTIONARY_REQUIRED**
                                                 Mailboxes / Aliases  

  `autoresponder`              `mail`            Mail Domains /       **DICTIONARY_REQUIRED**
                                                 Mailboxes / Aliases  

  `spam_policy`                `mail_security`   Anti-spam /          **DICTIONARY_REQUIRED**
                                                 Antivirus /          
                                                 Deliverability       

  `mail_filter`                `mail_security`   Anti-spam /          **DICTIONARY_REQUIRED**
                                                 Antivirus /          
                                                 Deliverability       

  `dkim_key`                   `mail_security`   Anti-spam /          **DICTIONARY_REQUIRED**
                                                 Antivirus /          
                                                 Deliverability       

  `dmarc_policy`               `mail_security`   Anti-spam /          **DICTIONARY_REQUIRED**
                                                 Antivirus /          
                                                 Deliverability       

  `mta_sts_policy`             `mail_security`   Anti-spam /          **DICTIONARY_REQUIRED**
                                                 Antivirus /          
                                                 Deliverability       

  `tls_rpt_policy`             `mail_security`   Anti-spam /          **DICTIONARY_REQUIRED**
                                                 Antivirus /          
                                                 Deliverability       

  `webmail_sso_ticket`         `webmail`         Webmail / SSO        **DICTIONARY_REQUIRED**

  `file_operation`             `files`           File Manager /       **DICTIONARY_REQUIRED**
                                                 Storage              

  `archive_operation`          `files`           File Manager /       **DICTIONARY_REQUIRED**
                                                 Storage              

  `storage_quota`              `files`           File Manager /       **DICTIONARY_REQUIRED**
                                                 Storage              

  `access_principal`           `access`          SFTP / FTP / SSH     **DICTIONARY_REQUIRED**

  `ssh_key`                    `access`          SFTP / FTP / SSH     **DICTIONARY_REQUIRED**

  `sftp_account`               `access`          SFTP / FTP / SSH     **DICTIONARY_REQUIRED**

  `ftp_account`                `access`          SFTP / FTP / SSH     **DICTIONARY_REQUIRED**

  `git_repository`             `git`             Git / Deployments /  **DICTIONARY_REQUIRED**
                                                 Webhooks             

  `deployment`                 `git`             Git / Deployments /  **DICTIONARY_REQUIRED**
                                                 Webhooks             

  `deploy_key`                 `git`             Git / Deployments /  **DICTIONARY_REQUIRED**
                                                 Webhooks             

  `git_webhook`                `git`             Git / Deployments /  **DICTIONARY_REQUIRED**
                                                 Webhooks             

  `application_instance`       `apps`            Application          **DICTIONARY_REQUIRED**
                                                 Installer            

  `application_template`       `apps`            Application          **DICTIONARY_REQUIRED**
                                                 Installer            

  `wordpress_instance`         `wordpress`       WordPress Toolkit    **DICTIONARY_REQUIRED**

  `wordpress_staging`          `wordpress`       WordPress Toolkit    **DICTIONARY_REQUIRED**

  `wordpress_security_state`   `wordpress`       WordPress Toolkit    **DICTIONARY_REQUIRED**

  `node_app`                   `node`            Node.js Apps         **DICTIONARY_REQUIRED**

  `python_app`                 `python`          Python WSGI/ASGI     **DICTIONARY_REQUIRED**

  `ruby_app`                   `ruby`            Ruby / Passenger     **DICTIONARY_REQUIRED**

  `go_app`                     `go`              Go Applications      **DICTIONARY_REQUIRED**

  `cron_job`                   `cron`            Cron / Scheduled     **EXISTING_DICTIONARY**
                                                 Tasks                

  `worker_service`             `workers`         Workers / Supervisor **DICTIONARY_REQUIRED**
                                                 / systemd user       
                                                 services             

  `queue_binding`              `workers`         Workers / Supervisor **DICTIONARY_REQUIRED**
                                                 / systemd user       
                                                 services             

  `backup_policy`              `backup`          Backup / Restore     **EXISTING_DICTIONARY**

  `backup_run`                 `backup`          Backup / Restore     **EXISTING_DICTIONARY**

  `restore_run`                `backup`          Backup / Restore     **EXISTING_DICTIONARY**

  `backup_artifact`            `backup`          Backup / Restore     **DICTIONARY_REQUIRED**

  `security_scan`              `security`        Malware / Antivirus  **DICTIONARY_REQUIRED**
                                                 / Hardening          

  `security_finding`           `security`        Malware / Antivirus  **EXISTING_DICTIONARY**
                                                 / Hardening          

  `quarantine_item`            `security`        Malware / Antivirus  **DICTIONARY_REQUIRED**
                                                 / Hardening          

  `hardening_profile`          `security`        Malware / Antivirus  **DICTIONARY_REQUIRED**
                                                 / Hardening          

  `firewall_rule`              `firewall`        Firewall /           **DICTIONARY_REQUIRED**
                                                 Brute-force          
                                                 Protection           

  `ip_block`                   `firewall`        Firewall /           **DICTIONARY_REQUIRED**
                                                 Brute-force          
                                                 Protection           

  `rate_limit_policy`          `firewall`        Firewall /           **DICTIONARY_REQUIRED**
                                                 Brute-force          
                                                 Protection           

  `metric_policy`              `monitoring`      Monitoring / Metrics **DICTIONARY_REQUIRED**
                                                 / Health             

  `health_check`               `monitoring`      Monitoring / Metrics **DICTIONARY_REQUIRED**
                                                 / Health             

  `alert_rule`                 `monitoring`      Monitoring / Metrics **DICTIONARY_REQUIRED**
                                                 / Health             

  `audit_event`                `logs`            Logs / Audit /       **DICTIONARY_REQUIRED**
                                                 Events               

  `system_event`               `logs`            Logs / Audit /       **DICTIONARY_REQUIRED**
                                                 Events               

  `log_view`                   `logs`            Logs / Audit /       **DICTIONARY_REQUIRED**
                                                 Events               

  `export_job`                 `logs`            Logs / Audit /       **DICTIONARY_REQUIRED**
                                                 Events               

  `notification_route`         `notifications`   Notifications        **EXISTING_DICTIONARY**

  `notification_template`      `notifications`   Notifications        **DICTIONARY_REQUIRED**

  `job`                        `jobs`            Jobs / Task Queue    **EXISTING_DICTIONARY**

  `job_attempt`                `jobs`            Jobs / Task Queue    **DICTIONARY_REQUIRED**

  `demo_policy`                `demo`            Demo Mode            **EXISTING_DICTIONARY**

  `demo_account`               `demo`            Demo Mode            **DICTIONARY_REQUIRED**

  `demo_dataset`               `demo`            Demo Mode            **DICTIONARY_REQUIRED**

  `trial_policy`               `trial`           Trial Accounts       **EXISTING_DICTIONARY**

  `trial_account`              `trial`           Trial Accounts       **DICTIONARY_REQUIRED**

  `api_token`                  `api`             API Tokens /         **EXISTING_DICTIONARY**
                                                 Webhooks /           
                                                 Automation           

  `webhook_endpoint`           `api`             API Tokens /         **DICTIONARY_REQUIRED**
                                                 Webhooks /           
                                                 Automation           

  `automation_rule`            `api`             API Tokens /         **DICTIONARY_REQUIRED**
                                                 Webhooks /           
                                                 Automation           

  `billing_connector`          `billing`         Billing /            **DICTIONARY_REQUIRED**
                                                 Provisioning         
                                                 Integration          

  `provisioning_request`       `billing`         Billing /            **DICTIONARY_REQUIRED**
                                                 Provisioning         
                                                 Integration          

  `migration_source`           `migration`       Migration / Import   **DICTIONARY_REQUIRED**

  `migration_job`              `migration`       Migration / Import   **DICTIONARY_REQUIRED**

  `migration_report`           `migration`       Migration / Import   **DICTIONARY_REQUIRED**

  `managed_service`            `system`          Server / Services /  **DICTIONARY_REQUIRED**
                                                 Packages             

  `service_profile`            `system`          Server / Services /  **DICTIONARY_REQUIRED**
                                                 Packages             

  `package_state`              `system`          Server / Services /  **DICTIONARY_REQUIRED**
                                                 Packages             

  `node_state`                 `system`          Server / Services /  **DICTIONARY_REQUIRED**
                                                 Packages             

  `installation_state`         `installer`       Install / Bootstrap  **DICTIONARY_REQUIRED**
                                                 / Finalization       
                                                 Wizard               

  `bootstrap_check`            `installer`       Install / Bootstrap  **DICTIONARY_REQUIRED**
                                                 / Finalization       
                                                 Wizard               

  `finalization_profile`       `installer`       Install / Bootstrap  **DICTIONARY_REQUIRED**
                                                 / Finalization       
                                                 Wizard               

  `release`                    `updates`         Updates / Release /  **DICTIONARY_REQUIRED**
                                                 Maintenance          

  `update_plan`                `updates`         Updates / Release /  **DICTIONARY_REQUIRED**
                                                 Maintenance          

  `maintenance_window`         `updates`         Updates / Release /  **DICTIONARY_REQUIRED**
                                                 Maintenance          

  `secret_ref`                 `secrets`         Secret Store /       **EXISTING_DICTIONARY**
                                                 Credentials          

  `secret_rotation`            `secrets`         Secret Store /       **DICTIONARY_REQUIRED**
                                                 Credentials          

  `resource_quota`             `quotas`          Quotas / Resource    **DICTIONARY_REQUIRED**
                                                 Isolation            

  `isolation_profile`          `quotas`          Quotas / Resource    **DICTIONARY_REQUIRED**
                                                 Isolation            

  `usage_snapshot`             `quotas`          Quotas / Resource    **DICTIONARY_REQUIRED**
                                                 Isolation            

  `branding_profile`           `branding`        White-label / Theme  **DICTIONARY_REQUIRED**

  `search_index_entry`         `search`          Global Search /      **DICTIONARY_REQUIRED**
                                                 Quick Launcher       

  `module_exposure`            `demo_trial_ui`   WCP-USER/WCP-ADMIN   **DICTIONARY_REQUIRED**
                                                 feature exposure     
  ---------------------------------------------------------------------------------------------

## Required dictionary columns

`field_id`, type, required, nullable, mutability, default, uniqueness,
cardinality/reference, sensitivity class, writer authority,
validation/invariants, delete participation.

## Count

Canonical resource candidates: **124**. Rows currently lacking an
explicitly detected canonical dictionary: **91**.

This count is intentionally conservative: a detailed module document is
not treated as a field dictionary unless the canonical schema registries
expose the resource explicitly.

## V4.0.21.4.1 additions
Canonical resources required: `isolation_profile`, `isolation_assignment`, `isolation_capability`, `resource_limit`, `resource_usage_snapshot`, `resource_limit_event`, `command_invocation` (control-plane/audit metadata only; never arbitrary shell).

## V4.0.21.5 status
The seven isolation/command resources added in V4.0.21.4.1 now have canonical B1 dictionaries in `ISOLATION-V4021.5-01-RESOURCE-DICTIONARIES.md`.
