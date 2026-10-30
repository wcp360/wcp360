# WCP360 — Scope and route ownership

Date: 2026-10-29

## Product boundaries

| Scope | Owns | Consumes from parent | Must never expose |
|---|---|---|---|
| **INTERNAL / vendor control plane** | Workforce IAM/JIT, license authority, marketplace publishing, build factory, releases, downloads, update manifests, consented fleet registry and commercial operations | Signed public source archives and versioned contracts | Private source, customers, commercial records, signing infrastructure or universal Linux access to a public installation |
| **ROOT / WCP-ADMIN** | Fleet, servers, clusters, services, network, DNS infrastructure, mail/database platforms, storage, security policy, licenses, global plans, feature ceilings, reseller envelopes and audit | Physical/cloud provider capabilities | Customer file, mailbox or database workspaces as if ROOT were the tenant |
| **RESELLER** | Owned customer accounts, commercial packages, allocated capacity, delegated services, reseller operators/ACLs, branding, customer notices, transfers and delegated audit | ROOT plans, maximum resource envelope, service allowlist, regions, IP pools and policy decisions | Server selection, daemon configuration, global DNS/mail/database settings, other resellers or unrestricted ROOT actions |
| **USER** | Websites, domains, mailboxes/webmail launchers, databases, files, backups, cron, SSL, applications, deployments and account security | One hosting package and account policy assigned by ROOT or RESELLER | Reseller portfolio, packages of other customers, server topology, global services or ROOT governance |

## Operational relationship

0. INTERNAL publishes signed releases, update manifests, entitlements and application catalogs through minimal public clients; it is not the parent tenant of customer WCP-ADMIN.
1. ROOT publishes a plan, feature ceiling and resource/service policy.
2. ROOT delegates a bounded envelope to a reseller, or assigns a plan directly to a hosting account.
3. RESELLER may narrow the envelope into customer packages; it can never widen it.
4. RESELLER creates and operates only customer accounts it owns.
5. USER operates resources inside one account and cannot see placement or parent capacity.
6. Every effective decision is the intersection of parent policy, role, ownership, package, quota and current security conditions.

## One capability, one write owner

- System Settings defines defaults and mandatory floors; it does not duplicate operational inventories.
- A canonical ROOT center owns infrastructure inventory, mutations, jobs and audit context for its capability.
- RESELLER narrows the ROOT envelope for owned customers and can never widen it.
- USER operates only account-owned resources through the effective policy.
- Provider-specific behavior belongs to an isolated adapter; optional workflows belong to extensions.
- Dashboards and compatibility pages are read-only summaries or deep links, never additional write owners.
- The complete 26-capability registry is maintained in the **Capability ownership** tab of `software-manager.html`.

## Canonical centers

| Canonical route | Ownership | Consolidated content | Compatibility routes |
|---|---|---|---|
| `../WCP360 Internal/index.html` | INTERNAL | Dedicated vendor login, private build/release factory, downloads/updates, license authority, marketplace and extension publishers, organizations/subscriptions, consented fleet and supply-chain security | No public route; physically separate private repository and deployment |
| `../WCP360 Extensions/wcp360-insights-connector/index.html` | ROOT extension | Optional local audit, normalized findings, consented reports and reviewed bug submissions | Separate signed artifact; disabled by default; no shell, hosted-content access or core dependency |
| `users-roles.html` | ROOT | Users, roles, plans, feature lists, effective permissions, demo accounts, reseller envelopes and quotas | `roles.html`, `plans.html`, `feature-lists.html`, `effective-permissions.html`, `demo-accounts.html`, `resellers-access.html`, `quotas.html` |
| `system-settings.html` | ROOT | Global server and platform settings | `server-configuration.html` |
| `web-routing.html` | ROOT | Global domain, subdomain, alias, redirect, canonical-host, reverse-proxy and document-root policy floors | `websites.html`, `domains.html` and `web-compatibility.html` consume the effective policy in USER scope |
| `hosting-accounts.html` | ROOT | Account inventory, lifecycle, placement and capacity | `account-operations.html` |
| `web-runtime.html` | ROOT | Web engines, PHP/FPM, Node.js, Python, Ruby, modules, stack profiles, assignments, lifecycle and recovery history | `language-runtimes.html`, `runtime-modules.html`, `web-stack-builder.html` |
| `admin-mail.html` | ROOT | Mail nodes/profiles, SMTP, IMAP/storage, security, reputation, external webmail/SSO, queue and forensic history | `mail-server-config.html`, `webmail-services.html`, `mail-queue.html` |
| `certificates.html` | ROOT | Fleet AutoSSL coverage, certificate inventory, service TLS, authorities, DCV/DNS, policies, renewals and revocation evidence | — |
| `application-catalog.html` | ROOT | Trusted applications, signed sources, install templates, update rings, global policies, fleet inventory and catalog entitlements | `app-installer.html` consumes the effective catalog in USER scope |
| `file-transfer-services.html` | ROOT | SFTP/FTPES/WebDAV daemons, endpoints, profiles, network paths, hard ceilings, live sessions and security policy | `file-access-center.html` consumes the effective protocol and limit policy in USER scope |
| `admin-dns.html` | ROOT | Authoritative zones, immutable templates, multi-region cluster, nameserver/glue sets, DNSSEC keys, provider adapters and global policy floor | `dns-zone-templates.html` → `#templates`; `dns-cluster.html` → `#cluster`; `canonical-dns.html` and `dns-record-add.html` consume effective policy in USER scope |
| `reseller.html` | RESELLER | Overview, customers, packages, capacity, services, ACLs, brand, transfers, notifications and audit/support | `reseller-governance.html` |

`system-settings.html` is deliberately not the operational owner of DNS zones, mail queues, databases, files, backups, runtimes, notifications or extensions. It publishes their global defaults and guardrails; each row above or the ownership registry points to the center that performs authoritative writes.

## Sidebar contract

- Each scope renders only routes owned by that scope.
- INTERNAL is absent from every public USER, RESELLER and ROOT sidebar. The public Software & Extensions catalog may discover the separately signed Insights Connector, but it never exposes a route or credential for the INTERNAL workforce console.
- Sections use a single-open accordion so the sidebar remains short.
- Search indexes every item, including links in collapsed sections.
- The active section opens automatically and the current route is unique.
- Related subfunctions live as tabs or categories inside a canonical center instead of duplicating sidebar entries.
- Portal switching is allowed only in the profile menu of this prototype and is explicitly marked as a demo feature.
- Production transitions between scopes require a separately authorized session; customer support access additionally requires consent, expiry and audit.

## Cross-scope workflows

Before these hosting workflows, the vendor INTERNAL plane may publish a signed public artifact, entitlement or catalog. Customer ROOT verifies and consumes that contract locally; it cannot mutate the private authority. A private-service outage must not block existing hosting, login, export or recovery.

| Workflow | ROOT role | RESELLER role | USER role |
|---|---|---|---|
| Account creation | Defines plans, placement policy and hard limits; provisions the account | Selects owned customer, allowed package, region preference and brand | Activates owner identity and starts onboarding |
| Package change | Publishes maximum feature/resource envelope | Chooses a package within that envelope | Receives impact notice; cannot self-expand limits |
| Capacity exhaustion | Supplies infrastructure and approves envelope changes | Monitors allocation/consumption and requests more capacity | Sees account quota and remediation guidance |
| Customer support | Handles infrastructure and escalated incidents | Opens consent-based, time-bound support session | Grants/revokes consent and sees the audit record |
| Suspension/termination | Enforces platform policy and recovery guarantees | Operates only owned accounts when ACL permits | Receives notice, status/default page and recovery information |
| Transfer/migration | Runs durable jobs and selects compatible placement | Chooses owned customer, source and allowed package | Validates application and data after cutover |
| Runtime publication | Installs signed runtimes/modules, builds profiles, stages rollout and owns rollback | Narrows the versions and profiles offered in its packages | Selects an allowed stack for an owned application; cannot install platform binaries |
| Mail service | Operates SMTP/IMAP nodes, filtering, reputation, webmail providers and queue | Narrows mailbox features, limits and webmail visibility inside its envelope | Manages owned mailboxes/routes and opens only ROOT-enabled external webmail clients |
| Certificate lifecycle | Configures CAs/DCV, service TLS, AutoSSL floors, renewal and revocation | Narrows certificate features available in packages | Requests and inspects certificates only for domains owned by its account |
| Application publication | Approves signed sources, versions, templates, security gates, update rings and rollback policy | Narrows the ROOT catalog by package; cannot add software or weaken a gate | Chooses only an effective application, owned domain/path and permitted update preference |
| File transfer access | Operates encrypted protocols, listeners, crypto, NAT/passive ranges, isolation and hard ceilings | Narrows protocols and limits inside its envelope | Manages only account-scoped identities, keys, paths, quotas and sessions using effective protocols |
| DNS publication | Operates authoritative engines, cluster, templates, nameserver sets, DNSSEC custody, providers and hard policy floors | Selects assigned templates/brands and may narrow record types inside its envelope | Manages records only inside an assigned zone and only for effective record types; cannot access keys, nodes or provider credentials |
| Web routing | Publishes reserved labels, subdomain depth/wildcard ceilings, redirect safety, proxy allowlists, canonical HTTPS and document-root boundaries | Narrows subdomain, alias and redirect features inside assigned packages | Creates subdomains, aliases and redirects only below owned domains and within the effective policy |
| Server notifications | Owns in-app and email fallback, sender identity, verified ROOT recipients, event taxonomy, severity and escalation floors | Receives scoped operational events and may add customer-facing recipients | Manages only account notification preferences; cannot suppress mandatory platform or security alerts |
