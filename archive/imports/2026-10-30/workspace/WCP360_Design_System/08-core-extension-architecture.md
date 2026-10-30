# WCP360 — Core and extension architecture

Date: 2026-10-29

## Architectural decision

WCP360 is a stable hosting core with optional, isolated extensions. The core must boot, authenticate, authorize, provision accounts, serve existing workloads, operate baseline services, restore local recovery points, audit actions and update itself when every extension is disabled or unavailable.

An extension may add routes, jobs, events, provider adapters and owned data. It must never be imported into the core process, write core tables directly, execute unrestricted ROOT shell, replace authorization or become a synchronous dependency of core startup.

## INTERNAL is not an extension

The vendor license authority, marketplace publisher, build factory, release manager, download/update origin, consented fleet registry and commercial operations are **private control-plane services**, not WCP360 plugins. They are developed and deployed from a separate private repository and are never installable on a customer server.

The public product contains only small, auditable clients:

| Private INTERNAL service | Public component | Failure behavior |
|---|---|---|
| License authority | Signature-verifying entitlement client | Uses bounded cached grace; hosting, login, export and recovery continue |
| Marketplace publisher | Signed catalog client | Cached catalog remains readable; new discovery may pause |
| Release/download control | Signed update client | Current version keeps operating; local rollback remains available |
| Consented fleet registry | Registration and diagnostic client | Registration is optional; customer WCP-ADMIN remains autonomous |

These clients follow the same deny-by-default, timeout, signature, privacy and circuit-breaker rules as provider adapters, but the private services themselves are outside the distributed extension platform. The complete release boundary is specified in `09-internal-public-distribution-architecture.md`.

The WCP360 Insights Connector does not turn INTERNAL into an extension. It is a detachable customer-side client that can submit consented audits, normalized failures and reviewed bug reports to the private Fleet service. It is packaged separately, disabled by default and has no remote-execution capability. Removing it leaves the public core and existing workloads unchanged.

Build and update isolation is structural: the public-product lane and detached-extension lane have separate worker inputs, output registries, manifests, compatibility checks and revocation records. A detached build is not eligible for promotion to Alpha, Beta, RC or Stable core channels.

Customer WCP-ADMIN also enforces a stateful local lifecycle. **Available**, **installed**, **running**, **stopped**, **quarantined** and **uninstalled** are distinct states. Install verifies signature, checksum, SBOM, provenance, compatibility, dependencies and the exact capability set. Activation uses a canary and health gates; disable withdraws routes and drains jobs while preserving owned data; quarantine additionally blocks outbound access; rollback changes only the extension slot; uninstall is blocked while enabled and requires an explicit data disposition. None of these transitions may reduce core availability.

## Classification test: core, adapter or extension

Every proposed module is classified with the following questions, in order:

1. **Does basic shared hosting stop working without it?** Authentication, account lifecycle, websites, domains, authoritative DNS, SSL, mail, databases, files, local backup, service control and updates are core.
2. **Does it enforce a security or tenancy boundary?** Authorization, quotas, document-root isolation, redirect/proxy safety, audit, secrets and signed jobs remain core even if an external product can enhance them.
3. **Is the capability generic but the implementation provider-specific?** The generic contract and safe fallback are core; Cloudflare, Route 53, registrar, S3, SMTP relay, SMS and incident-platform drivers are adapters/extensions.
4. **Can it be disabled without making existing sites, mailboxes or databases unavailable?** If yes, and it adds a workflow rather than a safety boundary, it is normally an extension.
5. **Does licensing expire?** Loss of an add-on license may stop new premium actions, never core login, export, recovery or existing workload service.
6. **Does it need privileged execution?** The extension requests a versioned core job. It never receives arbitrary ROOT shell or direct writes to core configuration.

| Capability | Classification | Reason and extension boundary |
|---|---|---|
| Nameserver sets, glue, authoritative nodes and delegation checks | **Core** | Required to publish and diagnose hosted domains. Registrar-specific automation is an adapter. |
| In-app alerts and verified ROOT email fallback | **Core** | The server must report service, security, backup and capacity failure without an add-on. SMS, chat and incident providers are connectors. |
| Subdomains, aliases, redirects and canonical HTTPS | **Core** | Fundamental virtual-host behavior and security validation. Edge/CDN execution and advanced traffic steering are extensions. |
| Webmail launcher and mailbox lifecycle | **Core contract** | Mailboxes and an external-login handoff remain available. Individual Roundcube, SnappyMail or SOGo adapters may be replaceable bundled extensions. |
| External DNS, object storage, identity and observability providers | **Adapter extensions** | Provider credentials, APIs and release cycles must remain detachable from the generic core capability. |
| WCP360 Insights Connector | **Detached optional extension** | Customer-controlled diagnostics and support evidence; separate artifact, explicit consent, outbound-only mTLS and no remote administration path. |
| App Installer, CI/CD, advanced HA/DR, privacy orchestration and forensic lab | **Bundled extensions** | Valuable workflows that can withdraw their routes and jobs while the hosting baseline continues. |
| Billing, commercial security gateways and third-party integrations | **External extensions** | Independent licensing, trust, upgrade and failure domains. |

## Single write owner rule

Every capability has exactly one authoritative write owner. The same state may be summarized in dashboards, onboarding or System Settings, but those surfaces must deep-link to the owner instead of creating another editable copy.

| Layer | Owns | Must not own |
|---|---|---|
| **System Settings** | Global defaults, mandatory policy floors, provider credentials, feature flags and platform behavior | Resource inventories, day-to-day actions, duplicate service forms or tenant resources |
| **Canonical ROOT center** | Authoritative inventory, validation, operational jobs, audit context and mutations for one infrastructure capability | USER resources presented as if ROOT were the tenant, or provider-specific code embedded in core |
| **RESELLER center** | A narrower commercial and operational envelope for owned child accounts | Server topology, daemon configuration, provider secrets or any capability above the ROOT ceiling |
| **USER center** | Resources owned by one hosting account under the effective policy | Fleet state, parent capacity, other tenants or global service configuration |
| **Extension / adapter** | Its isolated data, provider protocol and detachable workflow | Core tables, core authorization, a second canonical inventory or a synchronous core dependency |

The effective configuration is resolved in one direction:

`ROOT floor + plan ceiling -> RESELLER narrowing -> USER choice -> runtime/security condition`

Compatibility pages never become owners. They redirect or deep-link to a tab in the canonical center. A disabled or unhealthy extension withdraws its routes and jobs, then the declared core fallback becomes effective.

The interactive ownership registry in `software-manager.html#ownership` records 27 platform capabilities with their canonical route, ROOT/RESELLER/USER responsibility, System Settings boundary, extension boundary and deterministic fallback. It explicitly includes certificates, network/IPAM, storage, services/processes, migrations, HA/DR, privacy governance and developer delivery.

## Required core baseline

| Core domain | Minimum responsibility |
|---|---|
| Control plane and UI shell | API entry point, routing, configuration state and health |
| Identity and access | ROOT/RESELLER/USER tenancy, MFA, sessions and server-side authorization |
| Job and reconciliation engine | Durable jobs, locks, idempotency, retries, checkpoints and rollback |
| Privileged node agent | Versioned closed operations over mTLS; no arbitrary extension shell |
| Audit, events and notifications | Immutable attribution, in-app events and verified ROOT email fallback independent of extensions |
| Accounts, plans and quotas | Hosting lifecycle, ownership and effective capability resolution |
| Websites, domains, DNS and SSL | Virtual hosts, subdomains, aliases, redirects, authoritative publication and certificate lifecycle |
| Mail | Domains, mailboxes, SMTP/IMAP lifecycle and safe defaults |
| Databases | Databases, identities, grants, engine adapter and recovery baseline |
| Files and secure transfer | File manager, SFTP baseline, isolation and quota enforcement |
| Backup and restore | Local recovery points, verification and restore without remote connectors |
| System operations | Services, firewall, network baseline, packages and signed updates |

This target is intentionally comparable to a strong HestiaCP baseline. Advanced automation, commercial integrations and multi-site orchestration remain detachable.

## Extension families

| Extension | Current prototype coverage | Default posture | Core behavior when unavailable |
|---|---|---|---|
| Application Installer | Covered: catalog, templates, USER installer, updates and rollback | Bundled, enabled | Existing sites keep serving; installer routes disappear |
| HA & Disaster Recovery | Covered: floating IP/VRRP, fencing, replication, RPO/RTO, failover and drills | Bundled, optional | Single-node/service-pool operation remains available |
| Virtualization Control | Proxmox/libvirt adapters, images, VPS plans and VM inventory | Bundled, disabled by default | Websites, mail, DNS, databases, files and backups continue normally |
| Privacy & Data Governance | Covered: subject requests, erasure/anonymization, holds, residency and evidence | Bundled, disabled by default | Requests pause; data is never silently removed |
| Developer Pipelines | Covered: providers, webhooks, runners, policy, deployments and staging clone | Bundled, optional | Existing releases keep serving; deploy routes disappear |
| Green Operations | Covered in System Settings | Bundled, optional | Normal SLO-first scheduling resumes |
| Threat Intelligence & Forensics Lab | Covered: WORM, FIM, rootkit signals and quarantine sandbox | Bundled/security add-on | Baseline firewall/auth/audit remain; advanced scans pause |
| Remote Backup Connectors | Covered: S3/SFTP/immutable repositories | Bundled, optional | Local backup and restore remain available |
| Provider Adapter Pack | Cloudflare, Dynadot, Spaceship, ResellerClub and Brevo with scoped credentials and exclusions | Bundled adapters | Native DNS/mail and manual registrar workflows remain available |
| SQLite Account Databases | Account-scoped database files with WAL, permissions, quota and backup policy | Bundled, disabled by default | MariaDB and PostgreSQL remain available |
| DNS & Registrar Connectors | Provider adapters above core authoritative DNS and manual glue workflow | Bundled adapters | Native DNS, zone export and manual registrar evidence remain available |
| Notification Connectors | SMS, chat, on-call and incident-platform delivery | Bundled/external adapters | In-app and verified ROOT email delivery remain available |
| External Identity Connectors | OIDC, SAML, LDAP and directory sync | Bundled, disabled until configured | Local ROOT identities, MFA and recovery access remain available |
| Webmail Client Connectors | Roundcube, SnappyMail and SOGo launch/SSO adapters | Bundled adapters | SMTP/IMAP and mailbox management remain available |
| Edge & Advanced Traffic Routing | CDN, global steering and edge execution | Bundled, optional | Origin virtual hosts, subdomains and redirects continue locally |
| MailGuard, Billing, Observability | Provider-specific integrations | External verified | Core mail, accounts and local monitoring remain available |

## Minimum extension manifest

```yaml
id: wcp360.app-installer
type: bundled-extension
publisher: WCP360
version: 1.0.0
core: ">=4.8 <5.0"
permissions:
  - apps.catalog.read
  - jobs.application.install
routes:
  - root.catalog
  - user.installer
events:
  consumes: [account.terminated, runtime.withdrawn]
  emits: [application.installed, application.updated]
dependencies: [core.jobs, core.web, core.database]
data_namespace: ext_app_installer
failure_mode: hide-routes-and-retry-jobs
uninstall_policy: export-or-retain-owned-data
```

## Isolation contract

1. **Process boundary:** extension workers run out of process with a dedicated identity, CPU/RAM/time limits and network policy.
2. **Permission boundary:** deny by default; extensions receive scoped capability tokens and closed job operations only.
3. **Data boundary:** an extension owns its schema and migrations. Core resources are read through versioned APIs and changed through commands.
4. **UI boundary:** routes and sidebar contributions are published atomically only while the extension is enabled and healthy.
5. **Failure boundary:** timeouts and error budgets open a circuit breaker. Core workers are never held by an extension failure.
6. **Upgrade boundary:** compatibility, permission diff, SBOM, migration dry run, canary and rollback slot are mandatory.
7. **Uninstall boundary:** dependency scan, active-job drain, data export/retention choice and audit evidence precede removal.

## Lifecycle

`discover → verify → dependency plan → migrate owned schema → disposable test → canary → enable → observe → disable/drain → uninstall or rollback`

Disablement preserves owned data and historical evidence. New jobs are rejected, active jobs checkpoint or drain, UI routes are withdrawn and core fallbacks become effective.

## Product ownership

- **Software & Extensions** owns installation, enable/disable state, trust, compatibility, health, licensing and lifecycle.
- **Extension Platform** owns the developer contract: APIs, SDKs, manifests, hooks and conformance tests.
- Each extension owns its operational console and data; it does not duplicate global policy or core inventories.
- **System Settings** owns defaults and guardrails only. It may summarize effective health or expose global policy for an installed extension, but it must link to the canonical operational owner and show an unavailable state when that extension is absent.

## Next implementation slices

1. Implement the production adapters behind HA/DR, privacy and delivery contracts without widening extension privileges.
2. Apply the extension guard contract to every optional route and System Settings category.
3. Build a conformance kit that kills an extension during requests, jobs, migrations and upgrades and proves that core remains healthy.
4. Persist adapter receipts, reconciliation state and recovery evidence in the production control plane.
