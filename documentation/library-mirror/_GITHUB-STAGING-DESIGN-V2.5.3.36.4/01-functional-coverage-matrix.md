# Functional Coverage Matrix

| Domain | Status | Evidence | Audit note |
|---|---|---|---|
| Foundations / Shell / Components | **READY** | V1.0–V2.5.3.14 | Visual style locked; interaction shell established. |
| Websites / Applications | **READY** | V1.5 + V2.5.3.28 | App-centric lifecycle, releases, deploys and health covered. |
| Domains / DNS | **READY** | V2.5.3.15 | Zones, records, DNSSEC, propagation and domain onboarding covered. |
| Databases | **READY** | V2.5.3.16 | Users, privileges, access, transfer, backups and diagnostics covered. |
| Email | **READY** | V2.5.3.17 | Domains, mailboxes, auth, antispam, logs and deliverability covered. |
| File Manager | **READY** | V2.5.3.18 | Files, editor, upload, permissions, trash and SFTP covered. |
| Backups / Restore | **READY** | V2.5.3.19 | Policy, restore points, tests, storage and critical restore flow covered. |
| Security / Access | **READY** | V2.5.3.20 | MFA, sessions, tokens, WAF, impersonation, privileged and break-glass covered. |
| Monitoring / Incidents | **READY** | V2.5.3.21 | Metrics, alerts, SLO, capacity, incidents and correlation covered. |
| Servers / Runtime / Services | **PARTIAL** | V2.5.3.22 | Server/services/runtime covered; process list/detail and service-specific config schemas remain. |
| Updates / OS Lifecycle | **READY** | V2.5.3.23 | Packages, kernel, reboot, windows, batch rollout and exclusions covered. |
| Clusters / HA | **READY** | V2.5.3.24 | Quorum, nodes, replication, failover, maintenance and failure scenarios covered. |
| Firewall / Network | **READY** | V2.5.3.25 | Interfaces, rules, NAT, routing, ports, DNS and diagnostics covered. |
| Users / Roles / Plans / Privileges | **READY** | V2.5.3.26 | Feature lists, demo, reseller delegation, quotas and effective rights covered. |
| Jobs / API / Webhooks / Automation | **READY** | V2.5.3.27 | Jobs, queues, API, webhooks, retries, cron and automation covered. |
| Application Lifecycle | **READY** | V2.5.3.28 | Environments, secrets, builds, releases, deploy, health and rollback covered. |
| SSL / TLS | **PARTIAL** | V1.5 + legacy visual | Spec is READY, but visual depth does not yet match DNS/DB/Email families. |
| Storage / Volumes | **PARTIAL** | V1.5 spec | Needs dedicated volumes/filesystems/mounts/expansion visual family. |
| Processes / Workers | **PARTIAL** | V1.5 spec | Service detail exists; process list/detail/kill safety remain visually shallow. |
| Notifications | **PARTIAL** | V1.5 spec | Needs notification center, preferences, routing, subscriptions and delivery failures. |
| Help / Support | **PARTIAL** | V1.5 spec | Needs contextual help, troubleshooting, support request and support-bundle consent flow. |
| Approvals | **PARTIAL** | V1.5 policy | Policy exists; request/approve/reject/expiry/invalidation UI remains insufficient. |
| Audit | **PARTIAL** | legacy + security audit | Needs cross-product audit explorer, filters, correlation and export policy. |
| Profile / Account Settings | **PARTIAL** | inventory only | Needs profile, organization, preferences and account-level settings depth. |
| System Settings | **PARTIAL** | inventory only | Needs WCP-ADMIN system settings structure and governance boundaries. |
| Error Remediation | **PARTIAL** | V1.4 taxonomy | Needs module-specific remediation mapping surfaced consistently. |
| Localization / i18n / RTL | **PARTIAL** | architecture defined | Needs visual validation for expansion, RTL and locale switching. |
| Responsive / Mobile | **PARTIAL** | policy defined | Needs measured page-by-page behavior; no simple hide-only strategy. |
| Accessibility WCAG 2.2 AA | **PARTIAL** | mandatory policy | Needs measured contrast, keyboard, focus, semantics, screen-reader and motion audit. |
| Billing UI | **OUT OF CORE** | V1.5 | Billing intentionally external; entitlements/quotas remain core. |