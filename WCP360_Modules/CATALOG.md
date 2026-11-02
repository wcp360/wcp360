# WCP360 premium business-module catalogue

<!-- Generated from CATALOG.json. Update the authoritative JSON and regenerate this view and package exports. -->

**43 design proposals across 16 categories. All are PRO, non-installable and source_model: undecided.** No package implementation, provider admission, pricing or premium license is supplied.

This catalogue adds optional advanced workflows. All 233 essential capabilities and the seven Core engine adapters remain in the free Core scope. PRO rights never replace package trust, compatibility or current authorization. See [boundaries and authoring](README.md), the [machine catalogue](CATALOG.json), [planning schema](schemas/package-proposal.schema.json), [149-entry source](../PLUGIN_CATALOG.md) and [editions strategy](../documentation/development/EDITIONS-AND-PREMIUM.md).

## applications

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [AppInstall Pro](app-install-pro/README.md)<br><code>module.app-install-pro</code> | A curated application installation product with complete versioned recipes: requirements, trusted upstream artifacts, compatible runtime/database combinations, scoped configuration, install, update, backup, restore and dependency-aware removal. | Hosting, Data, Files, Operations, BackupRestore, Tenancy | `apps.installer`, `apps.cms-recipes`, `apps.business-recipes`, `apps.verified-updates` |
| [WordPress Management Pro](wordpress-management-pro/README.md)<br><code>module.wordpress-management-pro</code> | Advanced WordPress portfolio management covering instance discovery, core/plugin/theme inventory, compatible update campaigns, maintenance windows, hardening recommendations and source-attributed vulnerability tracking. | Hosting, Data, Files, Operations, BackupRestore | `apps.wordpress-toolkit`, `apps.verified-updates`, `security.advisories` |
| [Staging Workflows Pro](staging-workflows-pro/README.md)<br><code>module.staging-workflows-pro</code> | Managed staging environments with controlled file/database cloning, URL remapping, sanitization, isolated credentials, comparison and selective promotion. | Hosting, Data, Files, Operations, BackupRestore, Tenancy | `apps.staging-clones`, `delivery.previews`, `data.anonymization` |
| [Application Fleet Pro](application-fleet-pro/README.md)<br><code>module.application-fleet-pro</code> | Cross-application portfolio views with version/dependency drift, qualified campaign targeting, bounded rollout batches and per-instance reporting. | Hosting, Tenancy, Operations, Observability | `apps.fleet-management`, `apps.verified-updates`, `workspace.dashboards` |
| [Application Testing Pro](application-testing-pro/README.md)<br><code>module.application-testing-pro</code> | Advanced customer-application test campaigns using isolated preview environments, version matrices, synthetic user journeys and comparison before promotion. | Hosting, Data, Files, Operations, BackupRestore, Observability | `apps.verified-updates`, `delivery.previews`, `backup.restore-tests`, `observe.synthetics` |

## delivery

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Git Delivery Pro](git-delivery-pro/README.md)<br><code>module.git-delivery-pro</code> | Repository-driven delivery workflows with versioned branch/tag policy, isolated builds, immutable artifact references, environment binding and deployment history. | Hosting, Files, Operations, IdentityAccess | `delivery.git`, `delivery.pipelines`, `delivery.runners`, `delivery.dependency-analysis` |
| [Deployment Orchestration Pro](deployment-orchestration-pro/README.md)<br><code>module.deployment-orchestration-pro</code> | Advanced release orchestration with stage gates, canary or rolling strategies, traffic checks, drain coordination and supported recovery plans. | Hosting, Operations, Platform, BackupRestore, Observability | `delivery.pipelines`, `compute.rolling-operations`, `network.service-ha` |

## data

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Database Operations Pro](database-operations-pro/README.md)<br><code>module.database-operations-pro</code> | Advanced database topology workflows covering replication planning, replica initialization, lag review, controlled promotion and qualified extension campaigns. | Data, Operations, BackupRestore, Platform | `data.replication`, `data.postgres-extensions`, `data.external-databases` |
| [Database Performance Pro](database-performance-pro/README.md)<br><code>module.database-performance-pro</code> | Longitudinal database workload analysis with redacted query fingerprints, execution-plan comparison, capacity trends and explainable tuning proposals. | Data, Observability, Operations | `data.sql-analysis`, `data.anonymization`, `observe.grafana` |

## mail

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Mail Operations Pro](mail-operations-pro/README.md)<br><code>module.mail-operations-pro</code> | Advanced mail portfolio workflows for provider-assisted mailbox moves, coordinated domain cutovers, optional collaboration-suite integration and large-group policy campaigns. | Mail, Hosting, Transfers, Operations, BackupRestore | `mail.mailbox-migration`, `mail.mailing-lists`, `mail.sogo` |
| [Mail Deliverability Pro](mail-deliverability-pro/README.md)<br><code>module.mail-deliverability-pro</code> | Advanced multi-domain deliverability reporting with DMARC aggregate analysis, qualified relay comparisons, reputation evidence and reviewed BIMI-related readiness checks. | Mail, Hosting, Observability, Operations | `mail.dmarc`, `mail.bimi`, `mail.smtp-relays`, `mail.mailguard` |

## recovery

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Remote Backup Pro](remote-backup-pro/README.md)<br><code>module.remote-backup-pro</code> | Remote backup policy orchestration across qualified destinations and engines, with scoped repository bindings, encryption-key references, transfer receipts, retention review and verified restore references. | BackupRestore, Operations, Files, IdentityAccess | `backup.sftp`, `backup.s3`, `backup.nfs-destination`, `backup.restic`, `backup.borg`, `backup.immutable` |
| [Disaster Recovery Pro](disaster-recovery-pro/README.md)<br><code>module.disaster-recovery-pro</code> | Advanced multi-resource recovery plans, dependency ordering, isolated exercises and measured recovery objectives. | BackupRestore, Transfers, Operations, Platform, Hosting, Data, Mail | `backup.disaster-recovery`, `backup.restore-tests`, `compute.node-groups` |

## security

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Cybersecurity Posture Pro](security-posture-pro/README.md)<br><code>module.security-posture-pro</code> | Advanced cybersecurity posture review across identities, secrets, exposed services, TLS, software advisories, configuration drift and selected protection integrations. | Platform, IdentityAccess, Observability, Operations | `security.advisories`, `security.waf`, `security.file-integrity` |
| [Threat Response Pro](threat-response-pro/README.md)<br><code>module.threat-response-pro</code> | Advanced response playbooks that correlate selected security signals and propose scoped containment sequences. | IdentityAccess, Platform, Observability, Operations, Hosting | `security.siem`, `security.crowdsec`, `security.threat-intel`, `automation.event-rules` |
| [Malware Investigation Pro](malware-investigation-pro/README.md)<br><code>module.malware-investigation-pro</code> | Advanced investigation cases combining scoped scanner findings, file-change evidence, comparison and reviewed quarantine proposals. | Files, Hosting, Observability, Operations, BackupRestore | `security.clamav`, `security.maldet`, `security.forensics`, `security.file-integrity` |
| [DDoS Defense Pro](ddos-defense-pro/README.md)<br><code>module.ddos-defense-pro</code> | Advanced DDoS observation and response coordination across qualified upstream scrubbing/edge providers and admitted local L7 controls. | Platform, Hosting, IdentityAccess, Operations, Observability | `security.crowdsec`, `security.waf`, `security.threat-intel`, `domains.cdn-edge`, `network.haproxy`, `network.service-ha` |

## observability

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Observability Pro](observability-pro/README.md)<br><code>module.observability-pro</code> | Advanced service-objective dashboards, cross-resource correlation and portfolio analysis using qualified telemetry sources. | Observability, Platform, Tenancy, Operations | `observe.prometheus`, `observe.opentelemetry`, `observe.grafana`, `observe.synthetics` |
| [Log Analytics Pro](log-analytics-pro/README.md)<br><code>module.log-analytics-pro</code> | Advanced authorized log exploration with correlation, saved investigations, bounded aggregation and export cases. | Observability, IdentityAccess, Operations | `observe.loki`, `observe.syslog`, `security.siem`, `governance.audit-archive` |
| [Incident Operations Pro](incident-operations-pro/README.md)<br><code>module.incident-operations-pro</code> | Advanced incident cases, response coordination, on-call workflow integration and reviewed public-status drafting. | Observability, Operations, IdentityAccess | `notify.oncall`, `notify.team-messaging`, `support.status-page`, `support.reports` |

## identity

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Enterprise Identity Pro](enterprise-identity-pro/README.md)<br><code>module.enterprise-identity-pro</code> | Advanced federation and provisioning orchestration with reviewed organizational mappings, synchronization plans and identity-source change tracking. | IdentityAccess, Tenancy, Operations, ExtensionManagement | `identity.oidc`, `identity.saml`, `identity.ldap`, `identity.scim` |
| [Privileged Access Pro](privileged-access-pro/README.md)<br><code>module.privileged-access-pro</code> | Advanced just-in-time access requests, reviewed delegation windows and evidence around explicitly approved administrative sessions or secret consumers. | IdentityAccess, Operations, Observability, Platform | `identity.vaults-kms`, `workspace.web-terminal`, `automation.playbooks` |

## commercial

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Agency Workspace Pro](agency-workspace-pro/README.md)<br><code>module.agency-workspace-pro</code> | Advanced agency portfolio workspaces with scoped branding, client-facing views, resource links and coordinated handoffs. | Tenancy, IdentityAccess, Hosting, Observability | `workspace.white-label`, `workspace.dashboards`, `support.helpdesk` |
| [Billing Orchestration Pro](billing-orchestration-pro/README.md)<br><code>module.billing-orchestration-pro</code> | Advanced commercial reconciliation between measured usage, external customer/product mappings and reviewed provisioning requests. | Tenancy, Operations, IdentityAccess, Observability | `commerce.usage`, `commerce.whmcs`, `commerce.blesta`, `commerce.fossbilling`, `commerce.hostbill` |
| [Capacity Planning Pro](capacity-planning-pro/README.md)<br><code>module.capacity-planning-pro</code> | Advanced capacity forecasting, scenario comparison and explained placement recommendations from qualified usage observations. | Tenancy, Platform, Observability, Operations | `commerce.usage`, `compute.node-groups`, `automation.energy`, `hardware.sensors` |

## governance

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Compliance Evidence Pro](compliance-evidence-pro/README.md)<br><code>module.compliance-evidence-pro</code> | Advanced evidence cases, control mappings, review assignments and reproducible exports for internal or external assessments. | Observability, IdentityAccess, Platform, Operations | `governance.audit-archive`, `support.reports`, `security.advisories` |
| [Data Governance Pro](data-governance-pro/README.md)<br><code>module.data-governance-pro</code> | Advanced multi-resource privacy-request cases with discovery scope, approvals, redacted exports, retention holds and coordinated deletion proposals. | IdentityAccess, Tenancy, Files, Data, Mail, Observability, Operations | `governance.data-requests`, `governance.erasure`, `governance.retention-holds`, `governance.residency` |

## infrastructure

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Storage Orchestration Pro](storage-orchestration-pro/README.md)<br><code>module.storage-orchestration-pro</code> | Advanced storage lifecycle orchestration covering performance/resilience tiers, pools, volumes, measured capacity, encryption-key references, snapshots, replication and controlled migration across qualified local or distributed providers. | Platform, Files, Tenancy, BackupRestore, Operations | `storage.zfs`, `storage.ceph`, `storage.glusterfs`, `storage.object-storage`, `storage.raid-lvm` |
| [High Availability Pro](high-availability-pro/README.md)<br><code>module.high-availability-pro</code> | Advanced service-continuity coordination, failover rehearsals, leader/fence evidence and rolling-maintenance workflows on already admitted nodes/providers. | Platform, Hosting, Data, Operations, Observability, BackupRestore | `network.vrrp-vip`, `network.service-ha`, `compute.node-groups`, `compute.rolling-operations` |
| [Multi-server Hosting Pro](multi-server-hosting-pro/README.md)<br><code>module.multi-server-hosting-pro</code> | Advanced hosting orchestration across admitted servers: enrollment workflows, trust review, tenant/resource placement, scoped node routing, capacity groups, maintenance/drain waves and fleet care. | Platform, Hosting, Tenancy, IdentityAccess, Operations, Observability, BackupRestore, Data, Mail, Transfers | `compute.node-groups`, `compute.rolling-operations`, `runtime.workers`, `network.service-ha`, `automation.playbooks` |
| [Docker Management Pro](docker-management-pro/README.md)<br><code>module.docker-management-pro</code> | Advanced Docker/OCI workload lifecycle management: reviewed images/build provenance, scoped containers and application groups, private networks, ports, volumes, secret references, effective quotas, logs/health, coordinated updates and verified backups. | Platform, Hosting, Tenancy, Files, Data, Operations, Observability, BackupRestore | `compute.containers`, `compute.images`, `compute.rolling-operations`, `backup.restore-tests` |
| [Kubernetes Management Pro](kubernetes-management-pro/README.md)<br><code>module.kubernetes-management-pro</code> | Advanced Kubernetes and multi-cluster application orchestration: admitted cluster contexts, namespaces/RBAC, workloads, services/ingress/TLS, persistent volumes, quotas, observations, controlled upgrades and restore. | Platform, Hosting, IdentityAccess, Tenancy, Operations, Observability, Files, Data, BackupRestore | `compute.kubernetes`, `compute.images`, `compute.node-groups`, `compute.rolling-operations`, `network.service-ha` |
| [VPS / VM Management Pro](vps-vm-management-pro/README.md)<br><code>module.vps-vm-management-pro</code> | Advanced VPS and virtual-machine lifecycle orchestration across qualified hypervisors/providers: reviewed templates and cloud-init inputs, guest identities, virtual disks, IP/vNIC policy, effective quotas, snapshots, backups, maintenance and explicitly supported migrations. | Platform, Tenancy, IdentityAccess, Hosting, Files, Operations, Observability, BackupRestore | `compute.proxmox`, `compute.libvirt`, `compute.lxc`, `compute.images`, `network.interfaces`, `storage.raid-lvm` |

## networking

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Network Traffic Pro](network-traffic-pro/README.md)<br><code>module.network-traffic-pro</code> | Advanced load-balancer, global-routing and overlay-network orchestration with reviewed listener/upstream policies, measured checks and staged changes across qualified providers. | Platform, Hosting, Operations, Observability | `network.haproxy`, `network.traefik`, `domains.global-routing`, `domains.cdn-edge`, `network.wireguard` |
| [CDN Management Pro](cdn-management-pro/README.md)<br><code>module.cdn-management-pro</code> | Advanced multi-site CDN/edge orchestration with provider-qualified delivery profiles, origin/DNS binding, cache rules, purge workflows, TLS review and source-attributed cache/availability observations. | Hosting, Platform, IdentityAccess, Operations, Observability | `domains.cdn-edge`, `domains.global-routing`, `domains.cloudflare`, `domains.dns-providers`, `observe.synthetics` |

## automation

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Infrastructure Automation Pro](infrastructure-automation-pro/README.md)<br><code>module.infrastructure-automation-pro</code> | Advanced versioned playbooks, reusable workflow parameters and reviewed bulk infrastructure plans. | Operations, Platform, IdentityAccess, Observability | `automation.playbooks`, `automation.event-rules`, `automation.terraform-opentofu`, `automation.ansible` |
| [Diagnostic Toolbox Pro](diagnostic-toolbox-pro/README.md)<br><code>module.diagnostic-toolbox-pro</code> | Advanced guided diagnostic cases combining named bounded checks, evidence comparison and reviewed repair proposals across service owners. | Observability, Platform, Hosting, Data, Mail, Operations | `automation.playbooks`, `support.reports`, `security.forensics`, `observe.synthetics` |
| [AI Assistant Pro](ai-assistant-pro/README.md)<br><code>module.ai-assistant-pro</code> | Optional evidence-linked AI assistance for authorized diagnostics, followed only by reviewed action proposals where underlying operations are qualified. | Platform, IdentityAccess, Operations, Observability, ExtensionManagement | `automation.ai-readonly`, `automation.ai-missions`, `automation.chatops` |
| [API Automation Pro](api-automation-pro/README.md)<br><code>module.api-automation-pro</code> | Advanced workflow composition over existing public API/events, with reusable scoped parameters, delivery traces and approved cross-resource automations. | Operations, IdentityAccess, Platform, Observability | `automation.event-rules`, `automation.playbooks`, `automation.chatops` |

## workspace

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Developer Productivity Pro](developer-productivity-pro/README.md)<br><code>module.developer-productivity-pro</code> | Advanced project workspaces with scoped shortcuts, dependency reports, environment comparisons and qualified optional development sessions. | Hosting, Files, IdentityAccess, Operations, Observability | `workspace.dashboards`, `delivery.dependency-analysis`, `delivery.previews`, `workspace.web-terminal` |
| [Collaboration Approvals Pro](collaboration-approvals-pro/README.md)<br><code>module.collaboration-approvals-pro</code> | Advanced team review queues, separation-of-duties proposals, change annotations and approval evidence for optional operational workflows. | IdentityAccess, Operations, Tenancy, Observability | `automation.playbooks`, `support.reports`, `workspace.dashboards` |

## transfers

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Portfolio Migration Pro](portfolio-migration-pro/README.md)<br><code>module.portfolio-migration-pro</code> | Advanced coordinated migrations for many accounts or application portfolios, with provider-specific import plans, staged synchronization, dependency waves and cutover rehearsals. | Transfers, Tenancy, Hosting, Files, Data, Mail, Operations, BackupRestore | `apps.fleet-management`, `mail.mailbox-migration`, `backup.transfers`, `automation.playbooks` |

## performance

| Module / planning ID | Proposed optional scope | Core owners | Existing catalogue keys |
| :--- | :--- | :--- | :--- |
| [Performance Booster Pro](performance-booster-pro/README.md)<br><code>module.performance-booster-pro</code> | Advanced application-aware performance profiles coordinating compatible caches, compression, static/edge delivery, runtime budgets and measured tuning proposals. | Hosting, Platform, Data, Tenancy, Operations, Observability, BackupRestore | `runtime.redis`, `runtime.memcached`, `domains.cdn-edge`, `data.sql-analysis`, `observe.synthetics` |

## Reading a package

Each linked folder contains its exact planning proposal and package-specific acceptance document. Empty dependency lists mean no inter-package requirement has been selected; implemented Core contracts and admitted providers are still prerequisites. Package terms remain undecided, including any compatibility and upstream licensing review. The 410 App Deploy references are editorial input, not an installation guarantee.
