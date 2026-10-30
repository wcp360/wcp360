# V4.0.21.4 --- Canonical V1 Standalone Module Universe

## Statut

**NORMATIVE SCOPE FREEZE CANDIDATE --- NO CODE**

This inventory consolidates the V1 Standalone Hosting capabilities
already described across the corpus into one canonical module universe.
It does not add HA/Ceph/KVM/SDN/Kubernetes/Enterprise KMS as V1
blockers.

  -------------------------------------------------------------------------------------------------------------------------------------
  Module ID         Functional family    Tier       B1 resource  Existing explicit      Resources still requiring  B2 exact API status
                                                    status       resources              canonical dictionary       
  ----------------- -------------------- ---------- ------------ ---------------------- -------------------------- --------------------
  `identity`        Identity /           `CORE`     `PARTIAL`    tenant, user,          session, mfa_factor        `MISSING/UNPROVEN`
                    Authentication                               api_token                                         

  `authorization`   RBAC / Scope /       `CORE`     `PARTIAL`    role_binding           role, delegation,          `MISSING/UNPROVEN`
                    Impersonation                                                       impersonation_session      

  `accounts`        Hosting Accounts     `CORE`     `PARTIAL`    hosting_account        system_identity            `PARTIAL`

  `reseller`        Reseller /           `CORE`     `PARTIAL`    reseller               reseller_ceiling           `MISSING/UNPROVEN`
                    Multi-tenant                                                                                   

  `plans`           Plans / Packages /   `CORE`     `COMPLETE`   hosting_plan,          ---                        `PARTIAL`
                    Entitlements                                 plan_assignment,                                  
                                                                 entitlement,                                      
                                                                 entitlement_override                              

  `web`             Websites / Domains / `CORE`     `PARTIAL`    domain, site           vhost_config               `PARTIAL`
                    VHosts                                                                                         

  `webserver`       Nginx / Apache /     `CORE`     `MISSING`    ---                    webserver_profile,         `MISSING/UNPROVEN`
                    Proxy                                                               webserver_override         

  `php`             PHP / PHP-FPM        `CORE`     `PARTIAL`    runtime_profile        php_pool,                  `PARTIAL`
                                                                                        php_extension_set          

  `tls`             TLS / ACME           `CORE`     `PARTIAL`    certificate,           acme_order                 `PARTIAL`
                                                                 tls_policy                                        

  `dns`             DNS / DNSSEC         `CORE`     `PARTIAL`    dns_zone, dns_record   dnssec_keyset              `PARTIAL`

  `database`        MariaDB / MySQL /    `CORE`     `PARTIAL`    database,              database_grant             `PARTIAL`
                    PostgreSQL                                   database_user                                     

  `cache`           Redis / Memcached    `CORE`     `MISSING`    ---                    cache_instance,            `MISSING/UNPROVEN`
                                                                                        cache_credential           

  `mail`            Mail Domains /       `CORE`     `PARTIAL`    mail_domain, mailbox,  mail_forwarder,            `PARTIAL`
                    Mailboxes / Aliases                          mail_alias             autoresponder              

  `mail_security`   Anti-spam /          `CORE`     `MISSING`    ---                    spam_policy, mail_filter,  `MISSING/UNPROVEN`
                    Antivirus /                                                         dkim_key, dmarc_policy,    
                    Deliverability                                                      mta_sts_policy,            
                                                                                        tls_rpt_policy             

  `webmail`         Webmail / SSO        `CORE`     `MISSING`    ---                    webmail_sso_ticket         `MISSING/UNPROVEN`

  `files`           File Manager /       `CORE`     `MISSING`    ---                    file_operation,            `MISSING/UNPROVEN`
                    Storage                                                             archive_operation,         
                                                                                        storage_quota              

  `access`          SFTP / FTP / SSH     `CORE`     `MISSING`    ---                    access_principal, ssh_key, `MISSING/UNPROVEN`
                                                                                        sftp_account, ftp_account  

  `git`             Git / Deployments /  `CORE`     `MISSING`    ---                    git_repository,            `MISSING/UNPROVEN`
                    Webhooks                                                            deployment, deploy_key,    
                                                                                        git_webhook                

  `apps`            Application          `CORE`     `MISSING`    ---                    application_instance,      `MISSING/UNPROVEN`
                    Installer                                                           application_template       

  `wordpress`       WordPress Toolkit    `CORE`     `MISSING`    ---                    wordpress_instance,        `MISSING/UNPROVEN`
                                                                                        wordpress_staging,         
                                                                                        wordpress_security_state   

  `node`            Node.js Apps         `CORE`     `MISSING`    ---                    node_app                   `MISSING/UNPROVEN`

  `python`          Python WSGI/ASGI     `CORE`     `MISSING`    ---                    python_app                 `MISSING/UNPROVEN`

  `ruby`            Ruby / Passenger     `CORE`     `MISSING`    ---                    ruby_app                   `MISSING/UNPROVEN`

  `go`              Go Applications      `CORE`     `MISSING`    ---                    go_app                     `MISSING/UNPROVEN`

  `cron`            Cron / Scheduled     `CORE`     `COMPLETE`   cron_job               ---                        `MISSING/UNPROVEN`
                    Tasks                                                                                          

  `workers`         Workers / Supervisor `CORE`     `MISSING`    ---                    worker_service,            `MISSING/UNPROVEN`
                    / systemd user                                                      queue_binding              
                    services                                                                                       

  `backup`          Backup / Restore     `CORE`     `PARTIAL`    backup_policy,         backup_artifact            `PARTIAL`
                                                                 backup_run,                                       
                                                                 restore_run                                       

  `security`        Malware / Antivirus  `CORE`     `PARTIAL`    security_finding       security_scan,             `PARTIAL`
                    / Hardening                                                         quarantine_item,           
                                                                                        hardening_profile          

  `firewall`        Firewall /           `CORE`     `MISSING`    ---                    firewall_rule, ip_block,   `MISSING/UNPROVEN`
                    Brute-force                                                         rate_limit_policy          
                    Protection                                                                                     

  `monitoring`      Monitoring / Metrics `CORE`     `MISSING`    ---                    metric_policy,             `MISSING/UNPROVEN`
                    / Health                                                            health_check, alert_rule   

  `logs`            Logs / Audit /       `CORE`     `MISSING`    ---                    audit_event, system_event, `MISSING/UNPROVEN`
                    Events                                                              log_view, export_job       

  `notifications`   Notifications        `CORE`     `PARTIAL`    notification_route     notification_template      `PARTIAL`

  `jobs`            Jobs / Task Queue    `CORE`     `PARTIAL`    job                    job_attempt                `PARTIAL`

  `demo`            Demo Mode            `CORE`     `PARTIAL`    demo_policy            demo_account, demo_dataset `PARTIAL`

  `trial`           Trial Accounts       `CORE`     `PARTIAL`    trial_policy           trial_account              `PARTIAL`

  `api`             API Tokens /         `CORE`     `PARTIAL`    api_token              webhook_endpoint,          `MISSING/UNPROVEN`
                    Webhooks /                                                          automation_rule            
                    Automation                                                                                     

  `billing`         Billing /            `CORE`     `MISSING`    ---                    billing_connector,         `MISSING/UNPROVEN`
                    Provisioning                                                        provisioning_request       
                    Integration                                                                                    

  `migration`       Migration / Import   `CORE`     `MISSING`    ---                    migration_source,          `MISSING/UNPROVEN`
                                                                                        migration_job,             
                                                                                        migration_report           

  `system`          Server / Services /  `CORE`     `MISSING`    ---                    managed_service,           `PARTIAL`
                    Packages                                                            service_profile,           
                                                                                        package_state, node_state  

  `installer`       Install / Bootstrap  `CORE`     `MISSING`    ---                    installation_state,        `MISSING/UNPROVEN`
                    / Finalization                                                      bootstrap_check,           
                    Wizard                                                              finalization_profile       

  `updates`         Updates / Release /  `CORE`     `MISSING`    ---                    release, update_plan,      `MISSING/UNPROVEN`
                    Maintenance                                                         maintenance_window         

  `secrets`         Secret Store /       `CORE`     `PARTIAL`    secret_ref             secret_rotation            `MISSING/UNPROVEN`
                    Credentials                                                                                    

  `quotas`          Quotas / Resource    `CORE`     `MISSING`    ---                    resource_quota,            `MISSING/UNPROVEN`
                    Isolation                                                           isolation_profile,         
                                                                                        usage_snapshot             

  `branding`        White-label / Theme  `CORE`     `MISSING`    ---                    branding_profile           `MISSING/UNPROVEN`

  `search`          Global Search /      `CORE`     `MISSING`    ---                    search_index_entry         `MISSING/UNPROVEN`
                    Quick Launcher                                                                                 

  `demo_trial_ui`   WCP-USER/WCP-ADMIN   `CORE`     `MISSING`    ---                    module_exposure            `MISSING/UNPROVEN`
                    feature exposure                                                                               
  -------------------------------------------------------------------------------------------------------------------------------------

## Scope rule

A module marked `CORE` is not implementation-ready merely because a deep
functional document exists. It must pass B1--B5.

## Current finding

The existing corpus has strong deep specifications, but the canonical
B1/B2 registries do not yet prove complete coverage for all modules
above. Missing rows are documentation-contract gaps, not proof that the
feature itself was never discussed.
