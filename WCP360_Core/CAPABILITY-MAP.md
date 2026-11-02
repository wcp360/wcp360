# README capability and folder coverage

This navigation audit maps **233 capabilities in 21 README domains to 13 Core module owners**. It also checks the **149 catalog entries in 21 extension families**. An owner/path means there is an identifiable implementation location; it does not mean that the capability, adapter, test or security control is implemented.

Source: [README at ee85186](https://github.com/yyogas/wcp360/blob/ee85186c2ac369a311062a63e858c07738612f8a/README.md), Git blob 570033415f61d6516b25aba3720007ddb26947a4. Capability labels/counts are retained from that source. The mapping is design-level navigation subordinate to accepted API/DB/permission/IPC contracts and current gates; it does not create public identifiers or authorize runtime code.

## Findings and resulting structure

| Finding | Result |
| :--- | :--- |
| Instance migration had no explicit coordinating module; migrations/ only owns database-schema changes. | Add [Transfers](src/Module/Transfers/README.md), reusing existing resource owners and Operations. |
| The shared UI map omitted charts even though the README UI library requires them. | Add [components/charts](templates/components/charts/README.md), using authorized domain data and accessible alternatives. |
| Shared HTTP presentation/contracts plumbing had no named source anchor. | Add [Shared/Http](src/Shared/Http/README.md); keep domain endpoints in their own UI layer and the public SDK outside Core. |
| Shared context/secret-reference/redaction primitives had no named source anchor. | Add [Shared/Security](src/Shared/Security/README.md); preserve authoritative domain policy and accepted system boundaries. |
| The Core/root README diagrams omitted directories that already exist. | Update the diagrams and link this owner map from the navigation indexes. |


The [ROOT hosting account inventory](src/Module/IdentityAccess/README.md#root-hosting-account-inventory) adds bounded USER login identity reads and selected-user navigation into existing access blocks. This is not the complete customer/reseller/Unix-account lifecycle; capability counts and completion estimates remain unchanged.

The [other-session revocation action](src/Module/IdentityAccess/README.md#disconnecting-other-sessions) retains the current USER/ROOT browser, rechecks current credentials/session/factor policy and atomically audits an account-scoped bulk update. Session management and Revocation and role changes remain Partial; existing counts and the approximately 20% estimate remain unchanged.

The [ROOT runtime-settings workflow](src/Module/Platform/README.md#root-global-runtime-settings) adds a dedicated form and protected browser API over the same revisioned default, with conflict review and correlated audit. Hierarchical configuration and Configuration validation remain Partial; counts and estimates are unchanged.

The [hosting stack catalogue](src/Module/Hosting/README.md#web-stack-profiles-and-development-direction) separates Classic, standalone Modern, Apache Legacy and Advanced. Current Worker plans remain Advanced. Modern has a separate [inert Go driver and ROOT preview](src/Module/Hosting/README.md#standalone-modern-preview) for a synthetic single-site loopback configuration; durable Modern selection and activation remain unavailable. Apache Legacy, complete multi-PHP and stack migration remain unimplemented. Catalogue and preview integration do not change capability status, counts or the approximately 20% estimate. [Persisted site profiles](src/Module/Hosting/README.md#persisted-site-runtime-profiles) now preserve Classic/Advanced topology explicitly and fence observed changes at worker publication. This migration prerequisite does not implement stack switching, durable Modern selection or immutable per-job topology history; the same statuses and estimates remain.

[Reusable application profiles](src/Module/Hosting/APPLICATION-PROFILES.md) now generate seven server/runtime combinations across three routing profiles, with WordPress as a reference. These are protected configuration previews with optional bounded syntax probes and exact artifact hashes; native availability and unexecuted stages remain explicit. Live application installation, native recipe qualification, cache policy and measured performance remain outstanding. The Nginx → Apache → FPM target preview has its own version-2 document, with all content proxied to private Apache; its native Apache/FPM/HTTP acceptance is still open. No capability status/count or completion estimate is promoted.

The [candidate application catalogue bridge](src/Module/Hosting/APPLICATION-CATALOGUE.md) reads the preserved App Deploy CSV as bounded editorial metadata. Draft recipe schemas and WordPress/Discourse authoring examples prepare future lifecycle work; they do not install applications, admit providers or enable deployment. Existing capability statuses, counts and the completion estimate remain unchanged.

## Delivery snapshot

**Initial assessed runtime: 61adf46e3e29a666c3c0fc462591b7fbd639a85a, 2026-11-01.** See the [Core roadmap and weighted estimate](ROADMAP.md) for scope, dependencies and acceptance. Later live capacity/port/path inspection extends the prerequisite-check row. Offline operator-pinned artifact verification moves Update verification from Planned to Partial; update admission and official publisher trust remain absent. These later sources and current CI outcomes are tracked in [draft PR #116](https://github.com/yyogas/wcp360/pull/116) and distinguished from the initial checkpoint in the roadmap. The candidate-status column below records inspected behavior; it does not amend canonical contracts or certify production readiness.

| Candidate status | Rows | Meaning |
| :--- | ---: | :--- |
| Demonstrated | 5 | The stated bounded candidate path executes and has retained test evidence; the complete production capability is not thereby accepted. |
| Partial | 71 | Implemented candidate code, helper or probe covers only part of this capability. |
| Planned | 157 | No implemented Core product workflow was demonstrated for this capability; ownership/documentation or unrelated infrastructure alone is insufficient. |
| **Total** | **233** | **One status per existing capability; all production acceptance remains open.** |

These row counts are not the overall completion percentage. The weighted roadmap estimate is approximately 20% complete / 80% remaining (15–25% completion range). The five-OS userspace matrix and the Debian 13 privileged VM have different qualification boundaries.

The [administrator MFA candidate](src/Module/IdentityAccess/README.md#administrator-mfa-candidate) moves Multifactor authentication, Access recovery and Sensitive-action reauthentication from Planned to Partial. It supplies optional per-account TOTP, second-factor backup codes, voluntary setup/disablement and recent password/MFA mutation gates. Candidate identity confirmation is available; password recovery and action-bound canonical production integration remain absent. Current-source executed outcomes belong to draft PR #116, separately from the initial assessment.

The [shared candidate authorization boundary](src/Module/IdentityAccess/README.md#candidate-authorization-and-access-blocks) now applies one current-database rule to visibility and execution, with audited administrator access blocks for the single existing permission. Action-level permissions, Effective permissions and Revocation and role changes remain Partial; the counts above do not change. Canonical policy composition, the 82 canonical permissions and production identity separation remain unfinished. Current-source CI for this slice must be recorded separately from the earlier checkpoints.

The [candidate session registry](src/Module/IdentityAccess/README.md#candidate-session-inventory-and-revocation) adds bounded own-account inventory and password-confirmed single/all-session revocation in the current USER/ADMIN context, with durable cookie bindings, admission checks and audited logout. Session management and Revocation and role changes remain Partial; counts stay unchanged. Native Symfony session payloads remain in server-side files; canonical production session storage/grants, other channels and general in-flight effect fencing remain unfinished. Older checkouts require migration `Version20261101000300` and a new login. Exact-source CI for this slice belongs to draft PR #116, not the historical checkpoint.

The [runtime identity diagnostic](installer/preflight/README.md#runtime-identity-inspection) extends read-only prerequisite observation with fixed canonical profiles, actual current-namespace credentials and conservative process checks. It does not deploy identities or establish production Unix/filesystem/PostgreSQL separation; no capability status or count changes. Exact-source results remain in draft PR #116.

The [shared HTTP candidate](src/Shared/Http/README.md) now implements request references, safe exception presentation and a bounded error envelope for existing workbench endpoints. It reuses canonical error-code names without claiming complete API contracts or production integration. Hosting-plan submission now persists its originating request reference across the job and its lifecycle audit occurrences; cross-resource and IPC correlation remain unfinished. Capability statuses and the **5 demonstrated / 71 partial / 157 planned** counts stay unchanged; current-source execution is recorded in draft PR #116.

## Domain summary

The README domains describe capabilities; Core modules describe cohesive ownership. Several README domains can share a module, and a cross-resource capability has one primary coordinator with explicit collaborators.

| README domain | Capabilities | Primary owner(s) within the domain |
| :--- | :--- | :--- |
| [Authentication and access protection](#authentication-and-access-protection) | 10 | [IdentityAccess](src/Module/IdentityAccess/README.md) |
| [Permissions, delegation, and account separation](#permissions-delegation-and-account-separation) | 10 | [IdentityAccess](src/Module/IdentityAccess/README.md) |
| [Customers, resellers, and account lifecycle](#customers-resellers-and-account-lifecycle) | 10 | [Tenancy](src/Module/Tenancy/README.md) |
| [Hosting plans, quotas, and usage metering](#hosting-plans-quotas-and-usage-metering) | 10 | [Tenancy](src/Module/Tenancy/README.md), [Observability](src/Module/Observability/README.md) |
| [Websites, virtual hosts, and HTTP routing](#websites-virtual-hosts-and-http-routing) | 12 | [Hosting](src/Module/Hosting/README.md), [Observability](src/Module/Observability/README.md) |
| [Domains, authoritative DNS, and resolution](#domains-authoritative-dns-and-resolution) | 13 | [Hosting](src/Module/Hosting/README.md) |
| [Certificates, ACME, and TLS policies](#certificates-acme-and-tls-policies) | 10 | [Hosting](src/Module/Hosting/README.md) |
| [PHP, PHP-FPM, and site dependencies](#php-php-fpm-and-site-dependencies) | 11 | [Hosting](src/Module/Hosting/README.md) |
| [Databases and engine access](#databases-and-engine-access) | 13 | [Data](src/Module/Data/README.md), [BackupRestore](src/Module/BackupRestore/README.md) |
| [Mail services and deliverability](#mail-services-and-deliverability) | 18 | [Mail](src/Module/Mail/README.md) |
| [File management and web transfers](#file-management-and-web-transfers) | 13 | [Files](src/Module/Files/README.md) |
| [SSH, SFTP, and technical access](#ssh-sftp-and-technical-access) | 9 | [Platform](src/Module/Platform/README.md), [IdentityAccess](src/Module/IdentityAccess/README.md), [Tenancy](src/Module/Tenancy/README.md), [Observability](src/Module/Observability/README.md) |
| [Local backups, restoration, and retention](#local-backups-restoration-and-retention) | 12 | [BackupRestore](src/Module/BackupRestore/README.md) |
| [Migration and transfers between instances](#migration-and-transfers-between-instances) | 10 | [Transfers](src/Module/Transfers/README.md) |
| [System, services, packages, and local storage](#system-services-packages-and-local-storage) | 12 | [Platform](src/Module/Platform/README.md), [Operations](src/Module/Operations/README.md), [Observability](src/Module/Observability/README.md) |
| [Networking, IP addresses, and firewall](#networking-ip-addresses-and-firewall) | 10 | [Platform](src/Module/Platform/README.md) |
| [Security, secrets, and baseline compliance](#security-secrets-and-baseline-compliance) | 10 | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md), [IdentityAccess](src/Module/IdentityAccess/README.md) |
| [Metrics, alerts, incidents, and diagnostics](#metrics-alerts-incidents-and-diagnostics) | 10 | [Observability](src/Module/Observability/README.md) |
| [Jobs, reconciliation, and long-running operations](#jobs-reconciliation-and-long-running-operations) | 10 | [Operations](src/Module/Operations/README.md) |
| [Configuration, APIs, and application exchanges](#configuration-apis-and-application-exchanges) | 10 | [Platform](src/Module/Platform/README.md), [Operations](src/Module/Operations/README.md), [IdentityAccess](src/Module/IdentityAccess/README.md) |
| [Installation, updates, and panel autonomy](#installation-updates-and-panel-autonomy) | 10 | [Setup](src/Module/Setup/README.md), [Tenancy](src/Module/Tenancy/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| **Total** | **233** | **13 Core modules** |

## Shared service locations

| README service/component | Source owner or location |
| :--- | :--- |
| Tables, pagination, filtering, saved views, bulk/export controls | [components/tables](templates/components/tables/README.md); domain use cases enforce authorized queries and per-resource effects |
| Form Builder, fields and validation feedback | [components/forms](templates/components/forms/README.md); domain UI/Form and application/domain validation |
| Dialogs, confirmations, notifications and interface states | [components/feedback](templates/components/feedback/README.md); authorization is server-side |
| Menus, tabs, command palette and breadcrumbs | [components/navigation](templates/components/navigation/README.md); approved scoped registry |
| Charts, usage/history and indicators | [components/charts](templates/components/charts/README.md); Observability and the resource owner |
| Themes, responsive presentation and localization | [assets/styles](assets/styles/README.md), [translations](translations/README.md), [browser tests](tests/Browser/README.md) |
| HTTP envelopes, error presentation and correlation plumbing | [Shared/Http](src/Shared/Http/README.md); accepted API definitions remain authoritative |
| Identity/token/permission lifecycle | [IdentityAccess](src/Module/IdentityAccess/README.md); reusable primitives in [Shared/Security](src/Shared/Security/README.md) |
| Repositories, transactions and migrations | Owning module Infrastructure plus [migrations](migrations/README.md); hosted databases remain owned by Data |
| Jobs, outbox, scheduling, webhook delivery/replay/retry | [Operations](src/Module/Operations/README.md); resource payloads and permissions stay with the owning module |
| Effective settings and service capability discovery | [Platform](src/Module/Platform/README.md) with the resource owner and [service resources](resources/services/README.md) |
| Audit, local notifications, alerts, incidents and diagnostics | [Observability](src/Module/Observability/README.md), [diagnostics](diagnostics/README.md), shared redaction primitives |
| Package lifecycle and SDK conformance | [ExtensionManagement](src/Module/ExtensionManagement/README.md), [public SDK](../WCP360_SDK/README.md), [contract tests](tests/Contract/README.md) |

## Paths that are not missing

- The 142 optional catalog entries and 7 Core engine-adapter entries are design candidates, not 149 missing Core directories. Optional packages belong in WCP360_Plugins when their source is implemented. Core adapters keep the existing domain/service ownership and require selected-engine qualification.
- Domains, authoritative DNS, TLS and PHP hosting are cohesive Hosting responsibilities; they do not require separate top-level modules just to match README headings. Tenancy groups account/plan/quota ownership; Observability groups metrics, local alerts and incidents.
- Module Domain/Application/Infrastructure/UI code directories are created with actual accepted source. This audit supplies ownership documents, not empty classes or fictional controllers/commands.
- vendor/, node_modules/, var/, caches, real secrets, live /etc files and build output are generated/runtime data, not missing source folders. Real composer.json/lock and other toolchain manifests accompany the actual bootstrap.
- Existing tests, installer, asset and Twig category directories already have source maps. No extra parallel api/, functions/, commands/, secrets/ or duplicate configuration root is needed.
- WCP360 Internal Management and Official Website are intentionally outside current main; the README does not require restoring them into this repository. The full optional-plugin catalog remains in [PLUGIN_CATALOG.md](../PLUGIN_CATALOG.md).

## Capability-by-capability ownership

Primary owner is coordination and authoritative business responsibility. Collaborators provide their existing capabilities; this is not permission to bypass them or duplicate their inventories. A generic API/import/export row delegates resource-specific validation to its concrete domain; a shared helper does not own every resource.

### Authentication and access protection

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Sign-in and sign-out | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Demonstrated — Candidate sign-in/out forms, CSRF and real HTTP sessions. | [M1](ROADMAP.md#m1) |
| Multifactor authentication | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Partial — Optional per-account administrator TOTP enrollment/challenge, encrypted seeds and durable replay/attempt controls; enabled factors remain enforced and production integration remains open. | [M1](ROADMAP.md#m1) |
| Passkeys / WebAuthn | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Planned | [M1](ROADMAP.md#m1) |
| Access recovery | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Partial — Ten hashed single-use second-factor codes and password-confirmed factor reset; password recovery and independent emergency access absent. | [M1](ROADMAP.md#m1) |
| Session management | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Partial — Separate candidate USER/ADMIN registries, bound rotated cookies, bounded own-account inventory and password-confirmed revocation/logout; canonical session storage/grants and full production administration absent. | [M1](ROADMAP.md#m1) |
| Sensitive-action reauthentication | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Partial — Administrator mutations require recent password authentication or enabled MFA; candidate reconfirmation is available, action-bound production integration absent. | [M1](ROADMAP.md#m1) |
| Abuse protection | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Partial — Login throttling, durable MFA limits and per-session password-confirmation budgets for revocation; wider abuse policy absent. | [M1](ROADMAP.md#m1) |
| Passwords | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Partial — Argon2id enrollment/login; complete reset/rotation lifecycle absent. | [M1](ROADMAP.md#m1) |
| Sign-in history | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Planned | [M1](ROADMAP.md#m1) |
| Local recovery access | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Planned | [M1](ROADMAP.md#m1) |

### Permissions, delegation, and account separation

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| ROOT / RESELLER / USER roles | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Partial — ADMIN/USER candidate contexts; ROOT/RESELLER policy absent. | [M1](ROADMAP.md#m1) |
| Action-level permissions | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Partial — One fixed candidate.hosting.plan permission; shared current active-USER/membership/block checks, no canonical registry. | [M1](ROADMAP.md#m1) |
| Ownership checks | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Partial — Candidate tenant-plan and job ownership checks. | [M1](ROADMAP.md#m1) |
| Reseller delegation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Policy inheritance | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Effective permissions | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Partial — Shared database evaluation and read-only CLI for one candidate permission; canonical role/override/delegation composition absent. | [M4](ROADMAP.md#m4) |
| Data isolation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Partial — Candidate tenant/context checks; production database grants absent. | [M1](ROADMAP.md#m1) |
| Support mode / impersonation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Planned | [M1](ROADMAP.md#m1) |
| Service accounts | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Planned | [M1](ROADMAP.md#m1) |
| Revocation and role changes | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Partial — Audited tenant access blocks, current-authority worker checks, MFA-revision invalidation and durable own-account browser-session revocation; general role changes and in-flight effect/channel fencing absent. | [M1](ROADMAP.md#m1) |

### Customers, resellers, and account lifecycle

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Account creation | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Partial — CLI enrollment creates distinct account and tenant identities. | [M4](ROADMAP.md#m4) |
| Onboarding | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Interactive enrollment only; full customer onboarding absent. | [M4](ROADMAP.md#m4) |
| Customer profile | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Resource inventory | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Tenant-scoped prepared-site and job inventories with filters/cursors and shared list UI; general resource lifecycle/inventory absent. | [M4](ROADMAP.md#m4) |
| Targeted suspension | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Reactivation | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Closure and deletion | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Ownership transfer | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Demo accounts | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Trial accounts | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M4](ROADMAP.md#m4) |

### Hosting plans, quotas, and usage metering

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Plan catalog | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Resource limits | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Storage and inodes | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Bandwidth | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |
| CPU, RAM, and processes | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |
| I/O and connections | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Upgrades and exceptions | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Limit violations | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Usage history | [Observability](src/Module/Observability/README.md) | [Tenancy](src/Module/Tenancy/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Usage export | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M4](ROADMAP.md#m4) |

### Websites, virtual hosts, and HTTP routing

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Site provisioning | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Synthetic .test plans and guarded disposable hosting activation. | [M5](ROADMAP.md#m5) |
| Document roots | [Hosting](src/Module/Hosting/README.md) | [Files](src/Module/Files/README.md) | Partial — Fixed candidate tenant paths; general document-root lifecycle absent. | [M5](ROADMAP.md#m5) |
| Static and PHP sites | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Embedded PHP demo and isolated HTTP probes; arbitrary apps absent. | [M5](ROADMAP.md#m5) |
| Nginx configuration | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Fixed Go templates, bounded authorized plan preview/downloads and disposable Nginx/FPM execution; general configuration lifecycle unfinished. | [M5](ROADMAP.md#m5) |
| Web subdomains and aliases | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Redirects | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Reverse proxy | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Fixed FastCGI/Worker upstreams; general proxy administration absent. | [M5](ROADMAP.md#m5) |
| Per-site rules | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Directory access | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Hidden-file and missing-script refusals; directory policy UI absent. | [M5](ROADMAP.md#m5) |
| Caching and compression | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| HTTP logs | [Observability](src/Module/Observability/README.md) | [Hosting](src/Module/Hosting/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Configuration apply and recovery | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Candidate apply/recovery; privileged VM path is Debian 13-specific. | [M5](ROADMAP.md#m5) |

### Domains, authoritative DNS, and resolution

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Domain inventory | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Name validation | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Strict ASCII .test validation; public/IDN domain workflow absent. | [M5](ROADMAP.md#m5) |
| Zones and records | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| SOA, TTL, and serial numbers | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Zone templates | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Zone import and export | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| DNSSEC | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Nameservers and glue | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Primary and secondary DNS | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Resolution diagnostics | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Reverse DNS | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| CAA, TLSA, and DANE | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| DNS configuration drift | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |

### Certificates, ACME, and TLS policies

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Certificate inventory | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| ACME accounts and authorities | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Orders and challenges | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Wildcard certificates | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Renewal | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Import and CSR | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Deployment across services | [Hosting](src/Module/Hosting/README.md) | [Mail](src/Module/Mail/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M5](ROADMAP.md#m5) |
| TLS profiles | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Expiration and rotation | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Post-application verification | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M5](ROADMAP.md#m5) |

### PHP, PHP-FPM, and site dependencies

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| PHP versions | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Partial — Fixed candidate runtime profiles; hosted version inventory absent. | [M5](ROADMAP.md#m5) |
| Per-site assignment | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Partial — FPM/Worker assignment; hosted PHP-version assignment absent. | [M5](ROADMAP.md#m5) |
| PHP-FPM pools | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Partial — Fixed rendered pools and non-root runtime probes. | [M5](ROADMAP.md#m5) |
| PHP settings | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Partial — Fixed generated directives; general per-site settings absent. | [M5](ROADMAP.md#m5) |
| Effective configuration | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Partial — One revisioned default/inheritance policy; full effective settings absent. | [M5](ROADMAP.md#m5) |
| PHP extensions | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Planned | [M5](ROADMAP.md#m5) |
| OPcache and diagnostics | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Command-line PHP | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Composer | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Planned | [M5](ROADMAP.md#m5) |
| Startup and health | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Partial — Candidate process/service probes; general pool health management absent. | [M5](ROADMAP.md#m5) |
| Version changes | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) | Planned | [M5](ROADMAP.md#m5) |

### Databases and engine access

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Engine inventory | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Database creation | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Users and credentials | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Privileges | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Remote access | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Data import | [Data](src/Module/Data/README.md) | [Files](src/Module/Files/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Data export | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Backup and restore | [BackupRestore](src/Module/BackupRestore/README.md) | [Data](src/Module/Data/README.md) | Partial — Candidate control-plane dump/restore; tenant backup service absent. | [M7](ROADMAP.md#m7) |
| Connections and locks | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Slow queries | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| PostgreSQL extensions | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Deletion and dependencies | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Panel data separation | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |

### Mail services and deliverability

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Mail domains | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Mailboxes | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Quotas and sending limits | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Aliases and recipient groups | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Forwarders and catch-all | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Vacation replies | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Sieve filters | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| SMTP, IMAP, and authentication | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Autoconfig and Autodiscover | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Webmail access | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Mail queue | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| SPF, DKIM, and DMARC | [Mail](src/Module/Mail/README.md) | [Hosting](src/Module/Hosting/README.md) | Planned | [M8](ROADMAP.md#m8) |
| DKIM key rotation | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Baseline mail protection | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Outbound abuse and reputation | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| MTA-STS and TLS-RPT | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Deliverability diagnostics | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |
| Mail logs | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M8](ROADMAP.md#m8) |

### File management and web transfers

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| File explorer | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Creation and renaming | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Copy and move | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Uploads | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Downloads | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Text editor | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Edit conflicts | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Archives | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| File permissions | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Deletion and trash | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Search and size calculation | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Path protection | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |
| Operation history | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M6](ROADMAP.md#m6) |

### SSH, SFTP, and technical access

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Server SSH configuration | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Tenancy](src/Module/Tenancy/README.md) | Planned | [M9](ROADMAP.md#m9) |
| User keys | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| SFTP accounts | [Tenancy](src/Module/Tenancy/README.md) | [Files](src/Module/Files/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Delegated shell access | [Tenancy](src/Module/Tenancy/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Access restrictions | [Tenancy](src/Module/Tenancy/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Administrator lockout prevention | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Tenancy](src/Module/Tenancy/README.md) | Planned | [M9](ROADMAP.md#m9) |
| SSH host keys | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Tenancy](src/Module/Tenancy/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Sessions and logs | [Observability](src/Module/Observability/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Credential rotation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |

### Local backups, restoration, and retention

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Backup policies | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M7](ROADMAP.md#m7) |
| Per-resource backups | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Selected candidate PostgreSQL data only; files/mail/config coverage absent. | [M7](ROADMAP.md#m7) |
| Recovery points | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M7](ROADMAP.md#m7) |
| Execution and progress | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Partial — CLI helper execution; scheduled product backup jobs absent. | [M7](ROADMAP.md#m7) |
| Data consistency | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Selected logical database snapshots; cross-resource consistency absent. | [M7](ROADMAP.md#m7) |
| Encryption | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M7](ROADMAP.md#m7) |
| Verification | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Digest and logical-restore comparisons; full recovery verification absent. | [M7](ROADMAP.md#m7) |
| Retention and pruning | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M7](ROADMAP.md#m7) |
| Targeted restoration | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Restore into guarded empty test database; tenant restore workflow absent. | [M7](ROADMAP.md#m7) |
| Restore preparation | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Backup path/digest and empty test-database admission guards. | [M7](ROADMAP.md#m7) |
| Restore results | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Selected database-state comparison; full application restore reporting absent. | [M7](ROADMAP.md#m7) |
| Independent recovery access | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M7](ROADMAP.md#m7) |

### Migration and transfers between instances

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Source inventory | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| Compatibility checks | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| Resource mapping | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| Destination capacity | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| Tracked transfers | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| Final synchronization | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| Cutover preparation | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| Post-transfer validation | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| Recovery and cleanup | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |
| External formats | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M11](ROADMAP.md#m11) |

### System, services, packages, and local storage

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Server identity | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Strict target, boot-bound fixture identity and current-ROOT OS/architecture/memory/storage observations; full node inventory absent. | [M9](ROADMAP.md#m9) |
| System users and groups | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Fixed disposable identities and UID checks; production lifecycle absent. | [M9](ROADMAP.md#m9) |
| Services | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Candidate systemd units and fixed processes only. | [M9](ROADMAP.md#m9) |
| Service dependencies | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Fixed candidate startup ordering; general service dependency policy absent. | [M9](ROADMAP.md#m9) |
| Processes | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Candidate child supervision; general process controls absent. | [M9](ROADMAP.md#m9) |
| Packages and repositories | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Security updates | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| System scheduling | [Operations](src/Module/Operations/README.md) | [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Volumes and mounts | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Disk capacity and health | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| System logs | [Observability](src/Module/Observability/README.md) | [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Maintenance and reboot | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Explicit Debian 13 VM reboot/re-arm; operator maintenance workflow absent. | [M9](ROADMAP.md#m9) |

### Networking, IP addresses, and firewall

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Interface inventory | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| IPv4 and IPv6 | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| IP pools and reservations | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Routes and gateways | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Resolvers and local naming | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Ports and sockets | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Fixed candidate Unix sockets; host port/socket inventory absent. | [M9](ROADMAP.md#m9) |
| Firewall rules | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Blocks and exceptions | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Network diagnostics | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Sensitive network changes | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |

### Security, secrets, and baseline compliance

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Security center | [Observability](src/Module/Observability/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Baseline hardening | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md) | Partial — Candidate identity/allowlist/path guards; complete hardening policy absent. | [M2](ROADMAP.md#m2) |
| Sign-in attack prevention | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Platform](src/Module/Platform/README.md), [Observability](src/Module/Observability/README.md) | Partial — Login throttling, optional per-account administrator MFA and durable challenge/password-confirmation limits; broader protection lifecycle absent. | [M1](ROADMAP.md#m1) |
| Installed protection integrations | [Platform](src/Module/Platform/README.md) | [Mail](src/Module/Mail/README.md), [Observability](src/Module/Observability/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Application secrets | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Platform](src/Module/Platform/README.md), [Observability](src/Module/Observability/README.md) | Partial — Independent private application/MFA keys and encrypted factor seeds; managed secret rotation/store absent. | [M1](ROADMAP.md#m1) |
| Sensitive-data redaction | [Observability](src/Module/Observability/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md) | Partial — Selected readiness/installer redaction; system-wide coverage absent. | [M9](ROADMAP.md#m9) |
| Internal keys and certificates | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Request validation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Platform](src/Module/Platform/README.md), [Observability](src/Module/Observability/README.md) | Partial — Selected CSRF, schema and bounded-input checks. | [M1](ROADMAP.md#m1) |
| Change auditing | [Observability](src/Module/Observability/README.md) | [Operations](src/Module/Operations/README.md) | Partial — Append-only candidate audit and bounded current-ADMIN metadata queries with explicit restricted-role opt-in; production audit policy, general logs and retention absent. | [M2](ROADMAP.md#m2) |
| Evidence retention | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Partial — Source-bound CI and private journal; product evidence retention absent. | [M9](ROADMAP.md#m9) |

### Metrics, alerts, incidents, and diagnostics

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Server health | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Partial — Database SELECT 1 endpoint; full server health inventory absent. | [M9](ROADMAP.md#m9) |
| System metrics | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |
| Per-account metrics | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |
| History and trends | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |
| Service checks | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Partial — Fixed candidate runtime probes; operator service-check catalog absent. | [M9](ROADMAP.md#m9) |
| Alert rules | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |
| Notification center | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |
| Local alert fallback | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |
| Incident management | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |
| Diagnostic bundles | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |

### Jobs, reconciliation, and long-running operations

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Task center | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Partial — Tenant-scoped plan/job dashboard and filtered bidirectional job inventory; general scheduling, cancellation and production contracts remain open. | [M2](ROADMAP.md#m2) |
| Job persistence | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Demonstrated — Durable candidate job state and worker processing. | [M2](ROADMAP.md#m2) |
| Idempotency | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Demonstrated — Candidate request fingerprint, uniqueness and retry tests. | [M2](ROADMAP.md#m2) |
| Locks and concurrency | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Partial — Locked/CAS plan transitions, overlapping PostgreSQL transaction checks and install lock; general leases/fencing absent. | [M2](ROADMAP.md#m2) |
| Preconditions | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Partial — Candidate authorization/input/revision checks; general contract absent. | [M2](ROADMAP.md#m2) |
| Steps and checkpoints | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Partial — Installer/broker checkpoints; generic operation steps absent. | [M2](ROADMAP.md#m2) |
| Recovery and retries | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Partial — Conservative uncertain outcomes; general retry policy absent. | [M2](ROADMAP.md#m2) |
| Cancellation and compensation | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Planned | [M2](ROADMAP.md#m2) |
| Timeouts and resource budgets | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Partial — Selected child/IPC bounds; global task budgets absent. | [M2](ROADMAP.md#m2) |
| Reconciliation | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts | Partial — Bounded audited UNKNOWN_OUTCOME batches and stale publication refusal; broker recovery; generic flow absent. | [M2](ROADMAP.md#m2) |

### Configuration, APIs, and application exchanges

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Hierarchical configuration | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) | Partial — One global default/site override, dedicated ROOT form/API, revision conflicts and correlated audit; general hierarchy absent. | [M9](ROADMAP.md#m9) |
| Configuration validation | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) | Partial — Selected forms/profiles; general configuration model absent. | [M9](ROADMAP.md#m9) |
| Diff and apply | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) | Partial — Fixed plans and guarded apply; general diff/apply workflow absent. | [M9](ROADMAP.md#m9) |
| REST API | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) | Partial — Experimental workbench API; canonical production API absent. | [M9](ROADMAP.md#m9) |
| Identifiers and correlation | [Operations](src/Module/Operations/README.md) | [Platform](src/Module/Platform/README.md) | Partial — Candidate UUID identities, HTTP references and durable hosting-plan request/audit correlation; cross-resource/IPC tracing unfinished. | [M9](ROADMAP.md#m9) |
| Tokens and restrictions | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts | Planned | [M9](ROADMAP.md#m9) |
| Outbound webhooks | [Operations](src/Module/Operations/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Inbound webhooks | [Operations](src/Module/Operations/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Structured import and export | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) | Planned | [M9](ROADMAP.md#m9) |
| Capability detection | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) | Partial — Strict OS/runtime detection; full service capability discovery absent. | [M9](ROADMAP.md#m9) |

### Installation, updates, and panel autonomy

| README capability | Primary owner | Collaborators | Candidate delivery status | Next milestone |
| :--- | :--- | :--- | :--- | :--- |
| Prerequisite checks | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) | Demonstrated — Read-only OS/PHP/PostgreSQL checks and live capacity/port/path inspection; unknowns retained, native application blocked. | [M3](ROADMAP.md#m3) |
| Installation status | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) | Demonstrated — Private journal with read-only status and explicit resume. | [M3](ROADMAP.md#m3) |
| Initial configuration | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) | Partial — Private local config and CLI enrollment; graphical wizard absent. | [M3](ROADMAP.md#m3) |
| Update verification | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) | Partial — Offline operator-pinned candidate signature/identity/size/digest checks; trusted update admission, freshness and compatibility absent. | [M7](ROADMAP.md#m7) |
| Panel migrations | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) | Partial — Candidate migrations and uncertainty guards; supported release upgrade absent. | [M7](ROADMAP.md#m7) |
| Controlled maintenance | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) | Planned | [M7](ROADMAP.md#m7) |
| Version rollback | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) | Planned | [M7](ROADMAP.md#m7) |
| License entitlement status | [Tenancy](src/Module/Tenancy/README.md) | [ExtensionManagement](src/Module/ExtensionManagement/README.md) | Planned | [M4](ROADMAP.md#m4) |
| Extension independence | [ExtensionManagement](src/Module/ExtensionManagement/README.md) | [Setup](src/Module/Setup/README.md), [Hosting](src/Module/Hosting/README.md), [Mail](src/Module/Mail/README.md), [Data](src/Module/Data/README.md), [Files](src/Module/Files/README.md), [BackupRestore](src/Module/BackupRestore/README.md) | Partial — Reference plugin removal preserves candidate Core operations. | [M10](ROADMAP.md#m10) |
| Private-service availability | [ExtensionManagement](src/Module/ExtensionManagement/README.md) | [Setup](src/Module/Setup/README.md) | Partial — Local reference path has no vendor dependency; full offline-Core proof pending. | [M10](ROADMAP.md#m10) |

## Verification scope

The inventory parser counted 21 capability sections/233 unique domain-and-label pairs and 21 extension families/149 entries, matching the README totals. All mapped module names must exist and relative source links must resolve in the planned repository tree. These structural checks do not execute product workflows or establish production readiness.
