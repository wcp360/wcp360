# V4.0.21.3 --- Exhaustive PARTIAL / DECISION REQUIRED Triage

## Statut

**AUDIT / TRIAGE --- NO CODE --- REPOSITORY BLOCKED**

## Méthode

Tous les fichiers Markdown contenant littéralement `PARTIAL` ou
`DECISION REQUIRED` ont été analysés. La classification est fondée sur
le contexte local du marqueur, les formulations de réouverture/fermeture
et les supersessions explicites trouvées dans le corpus.

Cette classification n'efface ni ne réécrit les documents historiques.

## Résultat

-   Documents inspectés : **262**
-   `REAL_OPEN_GAP` : **24**
-   `HISTORICAL` : **231**
-   `SUPERSEDED` : **3**
-   `FALSE_POSITIVE` : **4**

## Inventaire

  ----------------------------------------------------------------------------------------------------------------------------------------------------
  Document                                                             Markers Classification       Supersession
  ------------------------------------------------------ --------------------- -------------------- --------------------------------------------------
  `100-system-overview.md`                                                   1 **HISTORICAL**       ---

  `101-system-identity-os-kernel.md`                                         1 **HISTORICAL**       ---

  `102-system-users-groups.md`                                               1 **HISTORICAL**       ---

  `103-system-packages-updates.md`                                           1 **HISTORICAL**       ---

  `104-system-processes.md`                                                  1 **HISTORICAL**       ---

  `105-system-services-systemd.md`                                           1 **HISTORICAL**       ---

  `106-system-storage-filesystems.md`                                        1 **HISTORICAL**       ---

  `107-system-cron-scheduler.md`                                             1 **HISTORICAL**       ---

  `108-system-logs-journal.md`                                               1 **HISTORICAL**       ---

  `109-system-reboot-shutdown-maintenance.md`                                1 **HISTORICAL**       ---

  `120-web-stack-overview.md`                                                1 **HISTORICAL**       ---

  `121-apache-overview.md`                                                   1 **HISTORICAL**       ---

  `122-apache-vhosts.md`                                                     1 **HISTORICAL**       ---

  `123-apache-modules-security.md`                                           1 **HISTORICAL**       ---

  `125-nginx-overview.md`                                                    1 **HISTORICAL**       ---

  `126-nginx-server-blocks.md`                                               1 **HISTORICAL**       ---

  `127-nginx-reverse-proxy-upstreams.md`                                     1 **HISTORICAL**       ---

  `128-nginx-cache-performance.md`                                           1 **HISTORICAL**       ---

  `130-php-overview.md`                                                      1 **HISTORICAL**       ---

  `131-php-versions.md`                                                      1 **HISTORICAL**       ---

  `132-php-fpm.md`                                                           1 **HISTORICAL**       ---

  `133-php-fpm-pools.md`                                                     1 **HISTORICAL**       ---

  `134-php-extensions.md`                                                    1 **HISTORICAL**       ---

  `135-php-ini-editor.md`                                                    1 **HISTORICAL**       ---

  `136-php-domain-runtime.md`                                                1 **HISTORICAL**       ---

  `140-database-stack.md`                                                    1 **HISTORICAL**       ---

  `141-mariadb-mysql.md`                                                     1 **HISTORICAL**       ---

  `142-postgresql.md`                                                        1 **HISTORICAL**       ---

  `143-database-users-privileges.md`                                         1 **HISTORICAL**       ---

  `144-database-remote-access.md`                                            1 **HISTORICAL**       ---

  `145-database-replication.md`                                              1 **HISTORICAL**       ---

  `150-mail-stack.md`                                                        1 **HISTORICAL**       ---

  `151-postfix.md`                                                           1 **HISTORICAL**       ---

  `152-exim.md`                                                              1 **HISTORICAL**       ---

  `153-dovecot.md`                                                           1 **HISTORICAL**       ---

  `154-mail-queue.md`                                                        1 **HISTORICAL**       ---

  `155-mail-deliverability-auth.md`                                          1 **HISTORICAL**       ---

  `156-mail-antispam-relay.md`                                               1 **HISTORICAL**       ---

  `170-dns-overview.md`                                                      1 **HISTORICAL**       ---

  `171-bind.md`                                                              1 **HISTORICAL**       ---

  `172-dns-zones-records.md`                                                 1 **HISTORICAL**       ---

  `173-dns-templates.md`                                                     1 **HISTORICAL**       ---

  `174-dnssec.md`                                                            1 **HISTORICAL**       ---

  `175-dns-cluster-sync.md`                                                  1 **HISTORICAL**       ---

  `176-reverse-dns-ptr.md`                                                   1 **HISTORICAL**       ---

  `180-network-overview.md`                                                  1 **HISTORICAL**       ---

  `181-network-interfaces-ip.md`                                             1 **HISTORICAL**       ---

  `182-routing-resolvers.md`                                                 1 **HISTORICAL**       ---

  `183-firewall.md`                                                          1 **HISTORICAL**       ---

  `184-fail2ban.md`                                                          1 **HISTORICAL**       ---

  `185-waf-modsecurity.md`                                                   1 **HISTORICAL**       ---

  `186-network-diagnostics.md`                                               1 **HISTORICAL**       ---

  `190-ssh-access.md`                                                        1 **HISTORICAL**       ---

  `191-sudo-policies.md`                                                     1 **HISTORICAL**       ---

  `194-ftp-sftp.md`                                                          1 **HISTORICAL**       ---

  `200-ssl-tls-overview.md`                                                  1 **HISTORICAL**       ---

  `201-certificates-acme.md`                                                 1 **HISTORICAL**       ---

  `202-csr-private-keys.md`                                                  1 **HISTORICAL**       ---

  `203-tls-policies.md`                                                      1 **HISTORICAL**       ---

  `210-backup-overview.md`                                                   1 **HISTORICAL**       ---

  `211-backup-profiles-scheduler.md`                                         1 **HISTORICAL**       ---

  `212-backup-destinations.md`                                               1 **HISTORICAL**       ---

  `213-restore-center.md`                                                    1 **HISTORICAL**       ---

  `214-snapshots-dr.md`                                                      1 **HISTORICAL**       ---

  `220-storage-zfs.md`                                                       1 **HISTORICAL**       ---

  `221-storage-ceph-distributed.md`                                          1 **HISTORICAL**       ---

  `230-cluster-manager.md`                                                   1 **HISTORICAL**       ---

  `231-node-manager-fencing.md`                                              1 **HISTORICAL**       ---

  `232-placement-migration-rolling.md`                                       1 **HISTORICAL**       ---

  `240-compute-kvm-qemu.md`                                                  1 **HISTORICAL**       ---

  `241-containers-lxc-docker.md`                                             1 **HISTORICAL**       ---

  `250-sdn-network-virtualization.md`                                        1 **HISTORICAL**       ---

  `251-load-balancers-vip.md`                                                1 **HISTORICAL**       ---

  `26-search-command-palette.md`                                             1 **HISTORICAL**       ---

  `260-monitoring-alerting.md`                                               1 **HISTORICAL**       ---

  `261-notification-integrations.md`                                         1 **HISTORICAL**       ---

  `27-charts-metrics-resource-usage.md`                                      1 **HISTORICAL**       ---

  `270-api-token-webhook-manager.md`                                         1 **HISTORICAL**       ---

  `280-plugin-extension-manager.md`                                          1 **HISTORICAL**       ---

  `29-file-manager-tree-upload.md`                                           1 **HISTORICAL**       ---

  `290-security-center.md`                                                   1 **HISTORICAL**       ---

  `291-antivirus-antimalware.md`                                             1 **HISTORICAL**       ---

  `292-quotas-isolation-lve-cagefs-chroot.md`                                1 **HISTORICAL**       ---

  `293-audit-traceability-login-history.md`                                  1 **HISTORICAL**       ---

  `294-mfa-2fa-access-security.md`                                           1 **HISTORICAL**       ---

  `300-runtime-platform-overview.md`                                         1 **HISTORICAL**       ---

  `301-nodejs-runtime.md`                                                    1 **HISTORICAL**       ---

  `302-python-runtime.md`                                                    1 **HISTORICAL**       ---

  `303-ruby-passenger-runtime.md`                                            1 **HISTORICAL**       ---

  `304-go-runtime.md`                                                        1 **HISTORICAL**       ---

  `305-dependency-managers.md`                                               1 **HISTORICAL**       ---

  `306-git-cicd-deployments.md`                                              1 **HISTORICAL**       ---

  `31-create-edit-wizards.md`                                                1 **HISTORICAL**       ---

  `310-litespeed-openlitespeed.md`                                           1 **HISTORICAL**       ---

  `320-redis.md`                                                             1 **HISTORICAL**       ---

  `321-memcached.md`                                                         1 **HISTORICAL**       ---

  `322-database-web-tools-sso.md`                                            1 **HISTORICAL**       ---

  `330-task-queue-workers.md`                                                1 **HISTORICAL**       ---

  `331-rabbitmq.md`                                                          1 **HISTORICAL**       ---

  `340-billing-provisioning-integrations.md`                                 1 **HISTORICAL**       ---

  `341-provisioning-api-account-lifecycle.md`                                1 **HISTORICAL**       ---

  `350-file-manager-complete.md`                                             1 **HISTORICAL**       ---

  `360-wordpress-toolkit.md`                                                 1 **HISTORICAL**       ---

  `361-application-auto-installer-catalog.md`                                1 **HISTORICAL**       ---

  `362-app-runtime-gui-contract.md`                                          1 **HISTORICAL**       ---

  `363-file-manager-editor-preview.md`                                       1 **HISTORICAL**       ---

  `364-db-tools-one-click-sso.md`                                            1 **HISTORICAL**       ---

  `365-access-hierarchy-multitenancy.md`                                     1 **HISTORICAL**       ---

  `366-package-plan-manager.md`                                              1 **HISTORICAL**       ---

  `367-impersonation-support-mode.md`                                        1 **HISTORICAL**       ---

  `368-api-first-rest-contract.md`                                           1 **HISTORICAL**       ---

  `369-graphql-optional-layer.md`                                            1 **HISTORICAL**       ---

  `370-billing-connectors-official.md`                                       1 **HISTORICAL**       ---

  `371-webhooks-event-automation.md`                                         1 **HISTORICAL**       ---

  `372-webmail-roundcube-snappymail.md`                                      1 **HISTORICAL**       ---

  `373-mail-autoconfig-autodiscover.md`                                      1 **HISTORICAL**       ---

  `374-mail-domain-bootstrap-dkim-spf-dmarc.md`                              1 **HISTORICAL**       ---

  `375-sieve-vacation-forwarding.md`                                         1 **HISTORICAL**       ---

  `376-reseller-white-label-branding.md`                                     1 **HISTORICAL**       ---

  `377-global-search-quick-launcher.md`                                      1 **HISTORICAL**       ---

  `378-theme-dark-light-system.md`                                           1 **HISTORICAL**       ---

  `380-domain-registrar-manager.md`                                          1 **HISTORICAL**       ---

  `381-domain-whois-rdap.md`                                                 1 **HISTORICAL**       ---

  `382-nameservers-glue-records.md`                                          1 **HISTORICAL**       ---

  `383-dns-propagation-diagnostics.md`                                       1 **HISTORICAL**       ---

  `390-migration-transfer-center.md`                                         1 **HISTORICAL**       ---

  `391-cpanel-importer.md`                                                   2 **HISTORICAL**       ---

  `392-plesk-importer.md`                                                    1 **HISTORICAL**       ---

  `393-directadmin-importer.md`                                              1 **HISTORICAL**       ---

  `394-hestia-importer.md`                                                   1 **HISTORICAL**       ---

  `395-wcp-to-wcp-migration.md`                                              1 **HISTORICAL**       ---

  `400-mail-alias-forward-catchall.md`                                       1 **HISTORICAL**       ---

  `401-mailing-lists.md`                                                     1 **HISTORICAL**       ---

  `402-rspamd-spamassassin.md`                                               1 **HISTORICAL**       ---

  `403-outbound-spam-reputation.md`                                          1 **HISTORICAL**       ---

  `404-mail-rate-limits.md`                                                  1 **HISTORICAL**       ---

  `410-database-import-export.md`                                            1 **HISTORICAL**       ---

  `411-database-performance-slow-query.md`                                   1 **HISTORICAL**       ---

  `412-postgresql-extensions.md`                                             1 **HISTORICAL**       ---

  `420-backup-integrity-verification.md`                                     1 **HISTORICAL**       ---

  `421-restore-testing.md`                                                   1 **HISTORICAL**       ---

  `422-backup-retention-immutability.md`                                     1 **HISTORICAL**       ---

  `430-ipam-ipv4-ipv6.md`                                                    1 **HISTORICAL**       ---

  `431-bandwidth-accounting.md`                                              1 **HISTORICAL**       ---

  `432-resource-metering.md`                                                 1 **HISTORICAL**       ---

  `433-usage-billing-export.md`                                              1 **HISTORICAL**       ---

  `440-host-security-manager.md`                                             1 **HISTORICAL**       ---

  `441-selinux-apparmor.md`                                                  1 **HISTORICAL**       ---

  `442-security-updates-cve.md`                                              1 **HISTORICAL**       ---

  `443-crowdsec-intrusion-prevention.md`                                     1 **HISTORICAL**       ---

  `450-raid-lvm-smart.md`                                                    1 **HISTORICAL**       ---

  `451-disk-health.md`                                                       1 **HISTORICAL**       ---

  `460-enterprise-sso.md`                                                    1 **HISTORICAL**       ---

  `461-oidc-oauth.md`                                                        1 **HISTORICAL**       ---

  `462-saml.md`                                                              1 **HISTORICAL**       ---

  `463-ldap-active-directory.md`                                             1 **HISTORICAL**       ---

  `470-support-ticketing.md`                                                 1 **HISTORICAL**       ---

  `471-knowledge-base.md`                                                    1 **HISTORICAL**       ---

  `472-status-announcements.md`                                              1 **HISTORICAL**       ---

  `473-notification-preferences.md`                                          1 **HISTORICAL**       ---

  `480-license-manager.md`                                                   1 **HISTORICAL**       ---

  `481-commercial-integrations-licenses.md`                                  1 **HISTORICAL**       ---

  `71-workflows-e2e.md`                                                      1 **HISTORICAL**       ---

  `76-page-spec-template.md`                                                 1 **HISTORICAL**       ---

  `853-migration-api-rbac-tests.md`                                          1 **HISTORICAL**       ---

  `930-licensing-resource-contract.md`                                       1 **REAL_OPEN_GAP**    ---

  `931-plugin-marketplace-security-gate.md`                                  1 **HISTORICAL**       ---

  `940-ha-cluster-readiness-gate.md`                                         2 **HISTORICAL**       ---

  `99-module-catalog.md`                                                     1 **HISTORICAL**       ---

  `ACCEPT-V4019-08-SECURITY-IDS.md`                                          1 **HISTORICAL**       ---

  `ACCEPT-V4019-09-E2E-IDS.md`                                               1 **HISTORICAL**       ---

  `API-409-07-API-PAGINATION-FILTERS-ERRORS.md`                              2 **HISTORICAL**       ---

  `API-409-19-PACKAGE-CHANGE-UPGRADE-DOWNGRADE.md`                           1 **HISTORICAL**       ---

  `API-409-27-INTEGRATION-CONTRACT-TESTING.md`                               1 **HISTORICAL**       ---

  `API-409-29-API-PROVISIONING-E2E.md`                                       2 **HISTORICAL**       ---

  `AUDIT-V401-00-EXECUTIVE-SUMMARY.md`                                       2 **REAL_OPEN_GAP**    ---

  `AUDIT-V401-03-OPEN-DECISIONS-PARTIALS.md`                               201 **REAL_OPEN_GAP**    ---

  `AUDIT-V401-20-CORPUS-INVENTORY-01.md`                                    56 **HISTORICAL**       ---

  `AUDIT-V401-20-CORPUS-INVENTORY-02.md`                                    98 **FALSE_POSITIVE**   ---

  `AUDIT-V401-20-CORPUS-INVENTORY-03.md`                                     9 **HISTORICAL**       ---

  `AUDIT-V401-20-CORPUS-INVENTORY-04.md`                                     4 **HISTORICAL**       ---

  `AUDIT-V401-20-CORPUS-INVENTORY-05.md`                                    11 **REAL_OPEN_GAP**    ---

  `AUDIT-V4012-LIFECYCLE-GOVERNANCE-CLOSURE.md`                              2 **REAL_OPEN_GAP**    ---

  `AUDIT-V4013-00-EXECUTIVE-CLOSURE.md`                                      6 **REAL_OPEN_GAP**    ---

  `AUDIT-V4013-01-MARKER-CLASSIFICATION.md`                                 12 **REAL_OPEN_GAP**    ---

  `AUDIT-V4013-07-E2E-COVERAGE.md`                                           1 **HISTORICAL**       ---

  `AUDIT-V4015-02-MARKER-RECHECK.md`                                         5 **REAL_OPEN_GAP**    ---

  `AUDIT-V4018-00-EXECUTIVE-VERDICT.md`                                      2 **HISTORICAL**       ---

  `AUDIT-V4018-01-RESOURCE-TRACEABILITY-MATRIX.md`                          21 **REAL_OPEN_GAP**    ---

  `AUDIT-V4018-02-TRACEABILITY-GAPS.md`                                      1 **HISTORICAL**       ---

  `AUDIT-V4018-03-API-COVERAGE.md`                                           3 **HISTORICAL**       ---

  `AUDIT-V4018-04-JOB-RPC-ADAPTER-COVERAGE.md`                               2 **HISTORICAL**       ---

  `AUDIT-V4018-05-RBAC-COVERAGE.md`                                          2 **HISTORICAL**       ---

  `AUDIT-V4018-06-DATABASE-COVERAGE.md`                                      2 **HISTORICAL**       ---

  `AUDIT-V4018-07-LIFECYCLE-COVERAGE.md`                                     2 **HISTORICAL**       ---

  `AUDIT-V4018-08-AUDIT-EVENT-COVERAGE.md`                                   2 **FALSE_POSITIVE**   ---

  `AUDIT-V4018-09-SECURITY-TEST-TRACEABILITY.md`                             2 **HISTORICAL**       ---

  `AUDIT-V4018-10-E2E-TRACEABILITY.md`                                       2 **HISTORICAL**       ---

  `AUDIT-V4018-11-PRIVILEGE-BOUNDARY.md`                                     3 **HISTORICAL**       ---

  `AUDIT-V4018-12-FAILURE-RECOVERY.md`                                       2 **HISTORICAL**       ---

  `AUDIT-V4018-13-UI-STATE-COVERAGE.md`                                      2 **HISTORICAL**       ---

  `AUDIT-V4018-14-NONFUNCTIONAL-READINESS.md`                                2 **FALSE_POSITIVE**   ---

  `AUDIT-V4018-15-MIGRATION-VERSIONING.md`                                   2 **HISTORICAL**       ---

  `AUDIT-V4018-16-CONTRADICTION-REVIEW.md`                                   1 **HISTORICAL**       ---

  `AUDIT-V4018-17-REPOSITORY-GATE-SCORECARD.md`                              9 **HISTORICAL**       ---

  `AUDIT-V4019-13-P0-CLOSURE.md`                                             1 **HISTORICAL**       ---

  `AUDIT-V4019-14-CONTRADICTION-RECHECK.md`                                  1 **HISTORICAL**       ---

  `AUDIT-V4020-02-P0-BLOCKERS.md`                                            1 **HISTORICAL**       ---

  `AUDIT-V4020-07-CROSS-RESOURCE-FAILURE-AUDIT.md`                           1 **HISTORICAL**       ---

  `AUDIT-V4021-13-P0-CLOSURE.md`                                             1 **SUPERSEDED**       → `V4.0.21.1 corrective status`

  `AUDIT-V4021.1-07-API-COMPLETENESS-CORRECTION.md`                          2 **REAL_OPEN_GAP**    ---

  `AUDIT-V4021.1-08-DATA-SCHEMA-CORRECTION.md`                               1 **REAL_OPEN_GAP**    ---

  `AUDIT-V4021.1-09-ACCEPTANCE-CORRECTION.md`                                1 **REAL_OPEN_GAP**    ---

  `AUDIT-V4021.1-10-CORRECTED-P0-STATUS.md`                                  8 **REAL_OPEN_GAP**    → `MASTER-V4021-DETERMINISTIC-CONTRACT-CLOSURE.md`

  `AUDIT-V4021.2-08-GLOBAL-CLOSURE-STATUS.md`                                3 **FALSE_POSITIVE**   → `MASTER-V4021.2-FULL-DETERMINISTIC-COVERAGE.md`

  `BOOT-4014-38-FIRST-RUN-API-AUTOMATION.md`                                 1 **HISTORICAL**       ---

  `CLUSTER-4011-02-NODE-ENROLLMENT-TRUST.md`                                 1 **HISTORICAL**       ---

  `CLUSTER-4011-14-NETWORK-PARTITION-SPLIT-BRAIN.md`                         1 **HISTORICAL**       ---

  `CLUSTER-4011-16-FAILOVER-ORCHESTRATION.md`                                1 **HISTORICAL**       ---

  `CLUSTER-4011-29-CLUSTER-E2E-FAILURE-MATRIX.md`                            1 **HISTORICAL**       ---

  `CLUSTER-4011-30-READY-GATE.md`                                            2 **REAL_OPEN_GAP**    ---

  `CONTRACT-V4021-08-CROSS-RESOURCE-ORCHESTRATION.md`                        1 **HISTORICAL**       ---

  `ERRATA-V4021.1-00-EXECUTIVE-CORRECTION.md`                                1 **HISTORICAL**       ---

  `FOUNDATION-402-25-FOUNDATION-SCENARIO-MATRIX.md`                          1 **HISTORICAL**       ---

  `GATE-V4019-15-REPOSITORY-ENTRY-PRECHECK.md`                               1 **HISTORICAL**       ---

  `GATE-V4021-14-NEXT-AUDIT.md`                                              1 **SUPERSEDED**       → `V4.0.21.1 corrective status`

  `IMPL-4017-25-E2E-ACCEPTANCE-MATRIX.md`                                    1 **HISTORICAL**       ---

  `LIFE-4012-30-READY-GATE.md`                                               2 **REAL_OPEN_GAP**    ---

  `MAIL-406-29-MAIL-FAILURE-ABUSE-MATRIX.md`                                 1 **HISTORICAL**       ---

  `MASTER-ADR-GOVERNANCE.md`                                                 2 **REAL_OPEN_GAP**    ---

  `MASTER-COMPLETENESS-CHECKLIST.md`                                         1 **HISTORICAL**       ---

  `MASTER-DOCUMENT-REGISTRY.md`                                             17 **HISTORICAL**       ---

  `MASTER-IMPLEMENTATION-READINESS.md`                                       5 **HISTORICAL**       ---

  `MASTER-NORMALIZATION-ROADMAP.md`                                          1 **HISTORICAL**       ---

  `MASTER-PACKAGE-REPOSITORY-STRATEGY.md`                                    1 **REAL_OPEN_GAP**    ---

  `MASTER-V38-READINESS-MATRIX.md`                                           5 **REAL_OPEN_GAP**    ---

  `MASTER-V401-DEEP-COMPLETENESS-AUDIT.md`                                   2 **REAL_OPEN_GAP**    ---

  `MASTER-V4012-LIFECYCLE-GOVERNANCE.md`                                     2 **REAL_OPEN_GAP**    ---

  `MASTER-V4018-IMPLEMENTATION-READINESS-AUDIT.md`                           2 **HISTORICAL**       ---

  `MASTER-V4019-TRACEABILITY-CLOSURE.md`                                     1 **HISTORICAL**       ---

  `MASTER-V4021-DETERMINISTIC-CONTRACT-CLOSURE.md`                           1 **SUPERSEDED**       → `V4.0.21.1 corrective status`

  `MASTER-V4021.1-CORRECTION.md`                                             1 **HISTORICAL**       ---

  `MASTER-VERSION-SUPPORT-POLICY.md`                                         1 **REAL_OPEN_GAP**    ---

  `OPS-4010-25-CONTROL-PLANE-SLO.md`                                         1 **HISTORICAL**       ---

  `PLAN-V4018-18-CLOSURE-BACKLOG.md`                                         2 **HISTORICAL**       ---

  `README.md`                                                                2 **HISTORICAL**       ---

  `REGISTRY-V4019-01-API-ACTIONS.md`                                         1 **HISTORICAL**       ---

  `REGISTRY-V4019-02-CAPABILITIES.md`                                        1 **HISTORICAL**       ---

  `REGISTRY-V4019-03-LIFECYCLE-ASSIGNMENT.md`                                1 **HISTORICAL**       ---

  `REGISTRY-V4019-04-LOGICAL-DATA.md`                                        1 **HISTORICAL**       ---

  `REGISTRY-V4019-05-JOB-EXECUTION.md`                                       1 **HISTORICAL**       ---

  `REGISTRY-V4019-06-RPC-AUTH-TARGETS.md`                                    1 **REAL_OPEN_GAP**    ---

  `REGISTRY-V4019-07-AUDIT-EVENTS.md`                                        1 **HISTORICAL**       ---

  `REGISTRY-V4019-10-COMPATIBILITY.md`                                       1 **HISTORICAL**       ---

  `REGISTRY-V4019-11-UI-STATES.md`                                           1 **HISTORICAL**       ---

  `SECOPS-407-06-ARCHIVE-CREATE-EXTRACT.md`                                  1 **HISTORICAL**       ---

  `SECOPS-407-20-BACKUP-JOBS-FAILURES.md`                                    1 **HISTORICAL**       ---

  `SYSTEM-403-01-SUPPORTED-OS-CERTIFICATION.md`                              1 **REAL_OPEN_GAP**    ---

  `SYSTEM-403-06-PACKAGE-REPOSITORIES-TRANSACTIONS.md`                       1 **HISTORICAL**       ---

  `TRACE-V4019-12-CORE-V1-MATRIX.md`                                         2 **HISTORICAL**       ---

  `V35-DEEP-NORMALIZATION-REPORT.md`                                         1 **REAL_OPEN_GAP**    ---

  `V36-SYSTEMATIC-FAMILY-AUDIT.md`                                           6 **HISTORICAL**       ---
  ----------------------------------------------------------------------------------------------------------------------------------------------------

Le fichier `AUDIT-V4021.3-PARTIAL-TRIAGE.csv` conserve aussi le premier
contexte et les scores de classification pour contrôle manuel.

## V4.0.21.3.1 correction
The numeric heuristic classification in this document is historical. `AUDIT-V4021.3.1-01-MANUAL-TRIAGE-CORRECTION.md` is authoritative for current blocker interpretation.
