# Final Functional Coverage Matrix

| Domain | Status | Evidence | Freeze note |
|---|---|---|---|
| Foundations / Shell / Components | FROZEN | V1.0–V2.5.3.14 | Visual baseline locked; no global redesign allowed. |
| Applications / Websites | FROZEN | V2.5.3.28 | Lifecycle, environments, runtime, deploy, health and rollback covered. |
| Domains / DNS | FROZEN | V2.5.3.15 | Domain/DNS lifecycle covered. |
| Databases | FROZEN | V2.5.3.16 | DB lifecycle, users, access, backups and diagnostics covered. |
| Email | FROZEN | V2.5.3.17 | Mailbox, auth, deliverability, antispam and logs covered. |
| Files | FROZEN | V2.5.3.18 | Manager, editor, transfers, search, trash and permissions covered. |
| Backups / Restore | FROZEN | V2.5.3.19 | Policy, restore points, tests and critical restore workflows covered. |
| Security / Access | FROZEN | V2.5.3.20 | Auth, sessions, keys/tokens, WAF, impersonation and break-glass covered. |
| Monitoring / Incidents | FROZEN | V2.5.3.21 | Metrics, alerts, SLO, capacity, incidents and correlation covered. |
| Servers / Services / Processes | FROZEN | V2.5.3.22 + .30 + .32 | Runtime, services, processes, workers and config schemas covered. |
| Updates / OS Lifecycle | FROZEN | V2.5.3.23 | Package, kernel, batch rollout and exclusions covered. |
| Clusters / HA | FROZEN | V2.5.3.24 | Quorum, replication, failover and failure scenarios covered. |
| Firewall / Network | FROZEN | V2.5.3.25 | Interfaces, rules, NAT, routes, ports and diagnostics covered. |
| Users / Roles / Plans / Privileges | FROZEN | V2.5.3.26 | RBAC, plans, feature lists, demo, reseller and quotas covered. |
| Jobs / API / Webhooks / Automation | FROZEN | V2.5.3.27 | Execution engine, scopes, webhooks, cron and automation covered. |
| Notifications | FROZEN | V2.5.3.30 | Inbox, preferences, routing and delivery covered. |
| Approvals | FROZEN | V2.5.3.30 | Approval lifecycle, expiry and execution revalidation covered. |
| Storage / Volumes | FROZEN | V2.5.3.30 | Volumes/filesystems/capacity/expansion covered. |
| SSL / TLS | FROZEN | V2.5.3.32 | Certificate lifecycle and deployment depth covered. |
| Help / Support | FROZEN | V2.5.3.32 | Contextual help and privacy-aware support bundle covered. |
| Audit Explorer | FROZEN | V2.5.3.32 | Cross-family traceability covered. |
| Error Remediation | FROZEN | V2.5.3.32 | Canonical retry/remediation/escalation model covered. |
| Account / System Settings | FROZEN | V2.5.3.32 | User/account and WCP-ADMIN global settings separated. |
| Accessibility / Responsive / RTL | DESIGN-FROZEN | V2.5.3.31 | Design requirements closed; production conformance testing still mandatory. |
| Billing UI | OUT OF CORE | V1.5 decision | External billing; quotas/entitlements stay core. |