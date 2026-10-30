# V4.0.21.17 --- Web/PHP/TLS/DNS Resource Registry

  -------------------------------------------------------------------------------------------------------------------------------
  Resource                   Parent/owner        Lifecycle                                      Desired/Observed   Closure
  -------------------------- ------------------- ---------------------------------------------- ------------------ --------------
  `domain`                   hosting_account     PENDING/ACTIVE/SUSPENDED/DELETING              yes                READY

  `site`                     domain/account      PROVISIONING/ACTIVE/SUSPENDED/ERROR/DELETING   yes                READY

  `document_root`            site/managed_root   ACTIVE/READONLY                                yes                READY

  `web_vhost`                site                PENDING/ACTIVE/ERROR/DELETING                  yes                READY

  `php_runtime_assignment`   site                ACTIVE/UNAVAILABLE                             yes                READY

  `php_fpm_pool`             site/account        PROVISIONING/ACTIVE/ERROR/DELETING             yes                READY

  `certificate`              domain/site         PENDING/VALID/EXPIRING/EXPIRED/REVOKED/ERROR   yes                READY

  `acme_order`               certificate         PENDING/CHALLENGING/ISSUED/FAILED              yes                READY

  `dns_zone`                 domain              PROVISIONING/ACTIVE/ERROR/DELETING             yes                READY

  `dns_record`               zone                ACTIVE/DELETING                                yes                READY

  `dnssec_state`             zone                DISABLED/PENDING/ACTIVE/ERROR                  yes                READY
  -------------------------------------------------------------------------------------------------------------------------------

Domain ownership never derives from DNS alone. Document roots must
remain inside the account managed-root policy. Certificates and DNS
records never store secret private material as ordinary resource fields.
