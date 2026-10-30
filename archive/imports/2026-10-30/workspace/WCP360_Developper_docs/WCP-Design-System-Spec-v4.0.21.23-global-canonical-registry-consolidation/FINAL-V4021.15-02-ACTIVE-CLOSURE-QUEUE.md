# V4.0.21.15 --- Active Closure Queue

## Fichiers nécessitant une revue contractuelle prioritaire

-   `MASTER-DOCUMENT-REGISTRY.md` --- 17
-   `UNIVERSE-V4021.4-01-CANONICAL-V1-MODULES.md` --- 13
-   `V36-SYSTEMATIC-FAMILY-AUDIT.md` --- 6
-   `MASTER-V38-READINESS-MATRIX.md` --- 5
-   `MASTER-IMPLEMENTATION-READINESS.md` --- 5
-   `MASTER-V4012-LIFECYCLE-GOVERNANCE.md` --- 4
-   `LIFE-4012-30-READY-GATE.md` --- 3
-   `V35-DEEP-NORMALIZATION-REPORT.md` --- 2
-   `PLAN-V4018-18-CLOSURE-BACKLOG.md` --- 2
-   `CLUSTER-4011-30-READY-GATE.md` --- 2
-   `API-409-07-API-PAGINATION-FILTERS-ERRORS.md` --- 2
-   `391-cpanel-importer.md` --- 2
-   `README.md` --- 2
-   `MASTER-ADR-GOVERNANCE.md` --- 2
-   `TRACE-V4019-12-CORE-V1-MATRIX.md` --- 2
-   `MASTER-V401-DEEP-COMPLETENESS-AUDIT.md` --- 2
-   `MASTER-V4021.11-OPS-SECURITY-INSTALLER-DEEP-CLOSURE.md` --- 2
-   `INDEX.md` --- 2
-   `MASTER-V4018-IMPLEMENTATION-READINESS-AUDIT.md` --- 2
-   `462-saml.md` --- 1
-   `128-nginx-cache-performance.md` --- 1
-   `232-placement-migration-rolling.md` --- 1
-   `410-database-import-export.md` --- 1
-   `172-dns-zones-records.md` --- 1
-   `270-api-token-webhook-manager.md` --- 1
-   `171-bind.md` --- 1
-   `140-database-stack.md` --- 1
-   `152-exim.md` --- 1
-   `368-api-first-rest-contract.md` --- 1
-   `303-ruby-passenger-runtime.md` --- 1
-   `214-snapshots-dr.md` --- 1
-   `853-migration-api-rbac-tests.md` --- 1
-   `REGISTRY-V4019-04-LOGICAL-DATA.md` --- 1
-   `BOOT-4014-38-FIRST-RUN-API-AUTOMATION.md` --- 1
-   `API-409-29-API-PROVISIONING-E2E.md` --- 1
-   `940-ha-cluster-readiness-gate.md` --- 1
-   `280-plugin-extension-manager.md` --- 1
-   `374-mail-domain-bootstrap-dkim-spf-dmarc.md` --- 1
-   `MASTER-COMPLETENESS-CHECKLIST.md` --- 1
-   `CLUSTER-4011-14-NETWORK-PARTITION-SPLIT-BRAIN.md` --- 1
-   `108-system-logs-journal.md` --- 1
-   `REGISTRY-V4019-03-LIFECYCLE-ASSIGNMENT.md` --- 1
-   `ACCEPT-V4019-08-SECURITY-IDS.md` --- 1
-   `MASTER-NORMALIZATION-ROADMAP.md` --- 1
-   `220-storage-zfs.md` --- 1
-   `461-oidc-oauth.md` --- 1
-   `134-php-extensions.md` --- 1
-   `360-wordpress-toolkit.md` --- 1
-   `MASTER-V4019-TRACEABILITY-CLOSURE.md` --- 1
-   `463-ldap-active-directory.md` --- 1
-   `480-license-manager.md` --- 1
-   `302-python-runtime.md` --- 1
-   `377-global-search-quick-launcher.md` --- 1
-   `143-database-users-privileges.md` --- 1
-   `250-sdn-network-virtualization.md` --- 1
-   `109-system-reboot-shutdown-maintenance.md` --- 1
-   `291-antivirus-antimalware.md` --- 1
-   `REGISTRY-V4019-01-API-ACTIONS.md` --- 1
-   `MASTER-VERSION-SUPPORT-POLICY.md` --- 1
-   `212-backup-destinations.md` --- 1
-   `411-database-performance-slow-query.md` --- 1
-   `472-status-announcements.md` --- 1
-   `31-create-edit-wizards.md` --- 1
-   `451-disk-health.md` --- 1
-   `182-routing-resolvers.md` --- 1
-   `371-webhooks-event-automation.md` --- 1
-   `154-mail-queue.md` --- 1
-   `REGISTRY-V4019-05-JOB-EXECUTION.md` --- 1
-   `126-nginx-server-blocks.md` --- 1
-   `ACCEPT-V4019-09-E2E-IDS.md` --- 1
-   `CLUSTER-4011-16-FAILOVER-ORCHESTRATION.md` --- 1
-   `364-db-tools-one-click-sso.md` --- 1
-   `132-php-fpm.md` --- 1
-   `184-fail2ban.md` --- 1
-   `341-provisioning-api-account-lifecycle.md` --- 1
-   `127-nginx-reverse-proxy-upstreams.md` --- 1
-   `183-firewall.md` --- 1
-   `378-theme-dark-light-system.md` --- 1
-   `144-database-remote-access.md` --- 1
-   `173-dns-templates.md` --- 1
-   `150-mail-stack.md` --- 1
-   `186-network-diagnostics.md` --- 1
-   `442-security-updates-cve.md` --- 1
-   `395-wcp-to-wcp-migration.md` --- 1
-   `MASTER-V4021.1-CORRECTION.md` --- 1
-   `380-domain-registrar-manager.md` --- 1
-   `422-backup-retention-immutability.md` --- 1
-   `931-plugin-marketplace-security-gate.md` --- 1
-   `230-cluster-manager.md` --- 1
-   `221-storage-ceph-distributed.md` --- 1
-   `331-rabbitmq.md` --- 1
-   `185-waf-modsecurity.md` --- 1
-   `102-system-users-groups.md` --- 1
-   `330-task-queue-workers.md` --- 1
-   `401-mailing-lists.md` --- 1
-   `CONTRACT-V4021-08-CROSS-RESOURCE-ORCHESTRATION.md` --- 1
-   `COMMANDS-V4021.5-08-CLI-OPTION-CONTRACT.md` --- 1
-   `460-enterprise-sso.md` --- 1
-   `393-directadmin-importer.md` --- 1
-   `141-mariadb-mysql.md` --- 1

## Règle de traitement

Chaque occurrence active doit être reliée à un code `MC-*`, à une
décision produit explicite, ou à un document canonique qui la supersède.
Une occurrence dans un ancien audit n'est pas un blocker
d'implémentation si le registre de supersession prouve qu'elle n'est
plus normative.
