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


## V2.5.3.33 final closure update
- G-015 Saved Views / Filters: CLOSED
- G-016 Search / Command Center: CLOSED
- G-017 Lifecycle State Consistency: CLOSED
- G-018 Design CSS Architecture: DEFERRED TO IMPLEMENTATION PREP; visual style remains frozen.