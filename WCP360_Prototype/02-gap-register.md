# Gap Register

Priority: P0 = design-freeze blocker; P1 = major closure; P2 = hardening/consistency.

| ID | Priority | Area | Required closure |
|---|---|---|---|
| G-001 | **P0** | Notifications | Create full Notification Center family: inbox, preferences, routing, subscriptions, delivery failures, grouping/dedup/cooldown/digest/escalation. |
| G-002 | **P0** | Approvals | Create Approval Center and request detail; distinguish requester/approver, expiry, invalidation after scope change, execution re-check. |
| G-003 | **P0** | Storage / Volumes | Create Volumes, Filesystems, Mounts, Usage, Alerts, Expansion with resize states and destructive safeguards. |
| G-004 | **P0** | Processes / Workers | Create process list/detail, terminate/kill workflow, worker pools, impacted resources, privileged kill handling. |
| G-005 | **P0** | Accessibility | Run measurable WCAG 2.2 AA audit across canonical pages and all critical workflows; fix focus, names, contrast, keyboard and reduced-motion. |
| G-006 | **P0** | Responsive / Mobile | Validate each critical family against mobile capability policy; replace hide-only behavior with summaries/drawers/safe actions. |
| G-007 | **P1** | SSL / TLS | Deepen certificate inventory, issuance, renewal, chain, ACME challenge, HSTS/OCSP policy, errors, revocation and deployment. |
| G-008 | **P1** | Help / Support | Create contextual help, troubleshooting, support request and privacy-aware support bundle flow. |
| G-009 | **P1** | Audit Explorer | Unify security/job/config/operator audit into searchable cross-family explorer with correlation IDs and export controls. |
| G-010 | **P1** | Error Remediation | Map canonical errors to module-specific actions, retryability, support escalation and correlation context. |
| G-011 | **P1** | Account / Profile Settings | Deepen user profile, organization, preferences, notification defaults, locale/theme/security entry points. |
| G-012 | **P1** | System Settings | Define WCP-ADMIN settings boundaries: global policies, defaults, providers, retention, security, maintenance, feature flags. |
| G-013 | **P1** | Localization / RTL | Test long labels, number/date formatting, locale selection and RTL navigation/table/form behavior. |
| G-014 | **P1** | Service-specific Config | Define schemas for Nginx, PHP-FPM, Redis, mail services, database services and daemon-managed configs. |
| G-015 | **P2** | Saved Views / Filters | Standardize saved-view ownership, sharing, defaults and permissions across tables and operator workspaces. |
| G-016 | **P2** | Search / Command Center | Complete command taxonomy, discoverability, permission-filtered results and no-result/error states across all modules. |
| G-017 | **P2** | Lifecycle State Consistency | Normalize state machines for resource provisioning, suspension, deletion, restore, migration, failover and jobs. |
| G-018 | **P2** | Design CSS Architecture | Refactor accumulated prototype CSS into modular layers without changing V2.5.3.14 locked appearance. |


## V2.5.3.30 closure update
- G-001 Notifications: CLOSED
- G-002 Approvals: CLOSED
- G-003 Storage / Volumes: CLOSED
- G-004 Processes / Workers: CLOSED
- G-005 Accessibility: OPEN
- G-006 Responsive / Mobile: OPEN


## V2.5.3.31 validation update
- G-005 Accessibility: CLOSED at design/specification level; production conformance testing remains mandatory.
- G-006 Responsive / Mobile: CLOSED at design/specification level; production device/browser validation remains mandatory.


## V2.5.3.32 P1 closure update
- G-007 SSL / TLS: CLOSED
- G-008 Help / Support: CLOSED
- G-009 Audit Explorer: CLOSED
- G-010 Error Remediation: CLOSED
- G-011 Account / Profile Settings: CLOSED
- G-012 System Settings: CLOSED
- G-014 Service-specific Config: CLOSED

## Competitive hosting parity closure — 2026-10-29

- Classic USER hosting utilities: CLOSED at prototype level.
- Advanced email rules, routing, trace and retention: CLOSED at prototype level.
- Account lifecycle operations: CLOSED at prototype level.
- Dedicated RESELLER workspace and operation-level ACLs: CLOSED at prototype level.
- Server profiles, stack builder, DNS cluster and mail service configuration: CLOSED at prototype level.
- Competitor migration importer and extension ecosystem: CLOSED at prototype level.
- PHP, Node.js, Python and Ruby lifecycle, assignments and package toolchains: CLOSED at prototype level.
- Runtime modules and PHP extensions (imagick, ionCube, redis, bcmath): CLOSED at prototype level.
- Bulk account restore with mapping, dry run, waves and rollback: CLOSED at prototype level.
- Resource metering, billing exports and suspension automation: CLOSED at prototype level.
- Real-time process, database, disk and inode diagnostics: CLOSED at prototype level.
- Account provisioning blueprints, skeleton ownership and lifecycle hooks: CLOSED at prototype level.
- ROOT scheduled tasks, systemd timers and tenant cron governance: CLOSED at prototype level.
- ROOT SFTP, FTPES and WebDAV service governance: CLOSED at prototype level.
- DNS zone templates and explicit live-zone ownership: CLOSED at prototype level.
- Account/platform lifecycle hooks with closed-operation execution: CLOSED at prototype level.
- ROOT service configuration schemas, provenance and drift handling: CLOSED at prototype level.
- ROOT Approval Center and approval request evidence workflow: CLOSED at prototype level.
- Notification routing, acknowledgment, escalation and delivery health: CLOSED at prototype level.
- Immutable Audit Explorer, correlation and controlled export policy: CLOSED at prototype level.
- USER / RESELLER / ROOT navigation separation: CLOSED and enforced by runtime audit.
- Lightweight sidebar consolidation: CLOSED with searchable single-open groups across canonical and legacy shells.
- RESELLER cross-scope routes: CLOSED; all delegated workflows now remain inside the canonical Reseller Center.
- Privileged credential workflows: CLOSED at prototype level with WCP-ADMIN password change, vaulted Linux root rotation, dual approval and session revocation.
- Hosting account identity and recovery: CLOSED at prototype level with dependency-aware rename/reset, terminated-account retention, restore, archive verification and typed purge confirmation.
- DNS and network quick tools: CLOSED at prototype level with WHOIS, record lookups, delegation/glue, propagation, ping and traceroute diagnostics.
- PHP security profiles: CLOSED at prototype level with open_basedir, disable_functions, FPM isolation, inheritance floors and compatibility audit.
- Database completion: CLOSED at prototype level with clone workflow, optional SQLite adapter and existing phpMyAdmin/pgAdmin/Adminer SSO governance.
- Named provider adapters: CLOSED at prototype level for Cloudflare, Dynadot, Spaceship, ResellerClub and Brevo, including domain automation exclusions and native fallback.
- Virtualization parity: CLOSED at prototype level as a disabled-by-default extension covering Proxmox/libvirt, images, VPS plans and virtual machine inventory.
- Legacy application parity: CLOSED at catalog-data level; named CMS, forum, wiki, commerce, gallery, CRM and support applications no longer require hard-coded pages.
- Advanced HA/DR orchestration: CLOSED at prototype level with floating IP and VRRP inventory, fencing gates, cross-datacenter replication, RPO/RTO policy and controlled recovery drills.
- Privacy and data governance: CLOSED at prototype level with subject requests, discovery and erasure approval, legal-hold conflict, data residency, geo-fencing evidence and signed receipts.
- Developer delivery governance: CLOSED at prototype level with Git provider connections, signed webhook registry, isolated runner pools, staging clones and ROOT deployment guardrails.
- Production implementation remains OPEN: privileged agent, control plane, durable jobs, reconciliation, system adapters, installer, upgrade/rollback, security assurance and recovery evidence.
