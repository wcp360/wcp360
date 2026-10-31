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

## Domain summary

The README domains describe capabilities; Core modules describe cohesive ownership. Several README domains can share a module, and a cross-resource capability has one primary coordinator with explicit collaborators.

| README domain | Capabilities | Primary owner(s) within the domain |
| :--- | :--- | :--- |
| [Authentication and access protection](../README.md#authentication-and-access-protection) | 10 | [IdentityAccess](src/Module/IdentityAccess/README.md) |
| [Permissions, delegation, and account separation](../README.md#permissions-delegation-and-account-separation) | 10 | [IdentityAccess](src/Module/IdentityAccess/README.md) |
| [Customers, resellers, and account lifecycle](../README.md#customers-resellers-and-account-lifecycle) | 10 | [Tenancy](src/Module/Tenancy/README.md) |
| [Hosting plans, quotas, and usage metering](../README.md#hosting-plans-quotas-and-usage-metering) | 10 | [Tenancy](src/Module/Tenancy/README.md), [Observability](src/Module/Observability/README.md) |
| [Websites, virtual hosts, and HTTP routing](../README.md#websites-virtual-hosts-and-http-routing) | 12 | [Hosting](src/Module/Hosting/README.md), [Observability](src/Module/Observability/README.md) |
| [Domains, authoritative DNS, and resolution](../README.md#domains-authoritative-dns-and-resolution) | 13 | [Hosting](src/Module/Hosting/README.md) |
| [Certificates, ACME, and TLS policies](../README.md#certificates-acme-and-tls-policies) | 10 | [Hosting](src/Module/Hosting/README.md) |
| [PHP, PHP-FPM, and site dependencies](../README.md#php-php-fpm-and-site-dependencies) | 11 | [Hosting](src/Module/Hosting/README.md) |
| [Databases and engine access](../README.md#databases-and-engine-access) | 13 | [Data](src/Module/Data/README.md), [BackupRestore](src/Module/BackupRestore/README.md) |
| [Mail services and deliverability](../README.md#mail-services-and-deliverability) | 18 | [Mail](src/Module/Mail/README.md) |
| [File management and web transfers](../README.md#file-management-and-web-transfers) | 13 | [Files](src/Module/Files/README.md) |
| [SSH, SFTP, and technical access](../README.md#ssh-sftp-and-technical-access) | 9 | [Platform](src/Module/Platform/README.md), [IdentityAccess](src/Module/IdentityAccess/README.md), [Tenancy](src/Module/Tenancy/README.md), [Observability](src/Module/Observability/README.md) |
| [Local backups, restoration, and retention](../README.md#local-backups-restoration-and-retention) | 12 | [BackupRestore](src/Module/BackupRestore/README.md) |
| [Migration and transfers between instances](../README.md#migration-and-transfers-between-instances) | 10 | [Transfers](src/Module/Transfers/README.md) |
| [System, services, packages, and local storage](../README.md#system-services-packages-and-local-storage) | 12 | [Platform](src/Module/Platform/README.md), [Operations](src/Module/Operations/README.md), [Observability](src/Module/Observability/README.md) |
| [Networking, IP addresses, and firewall](../README.md#networking-ip-addresses-and-firewall) | 10 | [Platform](src/Module/Platform/README.md) |
| [Security, secrets, and baseline compliance](../README.md#security-secrets-and-baseline-compliance) | 10 | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md), [IdentityAccess](src/Module/IdentityAccess/README.md) |
| [Metrics, alerts, incidents, and diagnostics](../README.md#metrics-alerts-incidents-and-diagnostics) | 10 | [Observability](src/Module/Observability/README.md) |
| [Jobs, reconciliation, and long-running operations](../README.md#jobs-reconciliation-and-long-running-operations) | 10 | [Operations](src/Module/Operations/README.md) |
| [Configuration, APIs, and application exchanges](../README.md#configuration-apis-and-application-exchanges) | 10 | [Platform](src/Module/Platform/README.md), [Operations](src/Module/Operations/README.md), [IdentityAccess](src/Module/IdentityAccess/README.md) |
| [Installation, updates, and panel autonomy](../README.md#installation-updates-and-panel-autonomy) | 10 | [Setup](src/Module/Setup/README.md), [Tenancy](src/Module/Tenancy/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
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

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Sign-in and sign-out | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Multifactor authentication | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Passkeys / WebAuthn | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Access recovery | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Session management | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Sensitive-action reauthentication | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Abuse protection | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Passwords | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Sign-in history | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Local recovery access | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |

### Permissions, delegation, and account separation

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| ROOT / RESELLER / USER roles | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Action-level permissions | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Ownership checks | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Reseller delegation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Policy inheritance | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Effective permissions | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Data isolation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Support mode / impersonation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Service accounts | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Revocation and role changes | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md) |

### Customers, resellers, and account lifecycle

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Account creation | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Onboarding | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Customer profile | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Resource inventory | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Targeted suspension | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Reactivation | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Closure and deletion | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Ownership transfer | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Demo accounts | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |
| Trial accounts | [Tenancy](src/Module/Tenancy/README.md) | [Operations](src/Module/Operations/README.md) |

### Hosting plans, quotas, and usage metering

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Plan catalog | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |
| Resource limits | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |
| Storage and inodes | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |
| Bandwidth | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |
| CPU, RAM, and processes | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |
| I/O and connections | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |
| Upgrades and exceptions | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |
| Limit violations | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |
| Usage history | [Observability](src/Module/Observability/README.md) | [Tenancy](src/Module/Tenancy/README.md) |
| Usage export | [Tenancy](src/Module/Tenancy/README.md) | [Observability](src/Module/Observability/README.md), [Platform](src/Module/Platform/README.md) |

### Websites, virtual hosts, and HTTP routing

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Site provisioning | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Document roots | [Hosting](src/Module/Hosting/README.md) | [Files](src/Module/Files/README.md) |
| Static and PHP sites | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Nginx configuration | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Web subdomains and aliases | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Redirects | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Reverse proxy | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Per-site rules | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Directory access | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Caching and compression | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| HTTP logs | [Observability](src/Module/Observability/README.md) | [Hosting](src/Module/Hosting/README.md) |
| Configuration apply and recovery | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |

### Domains, authoritative DNS, and resolution

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Domain inventory | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Name validation | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Zones and records | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| SOA, TTL, and serial numbers | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Zone templates | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Zone import and export | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| DNSSEC | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Nameservers and glue | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Primary and secondary DNS | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Resolution diagnostics | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Reverse DNS | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| CAA, TLSA, and DANE | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| DNS configuration drift | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |

### Certificates, ACME, and TLS policies

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Certificate inventory | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| ACME accounts and authorities | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Orders and challenges | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Wildcard certificates | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Renewal | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Import and CSR | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Deployment across services | [Hosting](src/Module/Hosting/README.md) | [Mail](src/Module/Mail/README.md), [Platform](src/Module/Platform/README.md) |
| TLS profiles | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Expiration and rotation | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |
| Post-application verification | [Hosting](src/Module/Hosting/README.md) | [Operations](src/Module/Operations/README.md) |

### PHP, PHP-FPM, and site dependencies

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| PHP versions | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| Per-site assignment | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| PHP-FPM pools | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| PHP settings | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| Effective configuration | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| PHP extensions | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| OPcache and diagnostics | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| Command-line PHP | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| Composer | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| Startup and health | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |
| Version changes | [Hosting](src/Module/Hosting/README.md) | [Platform](src/Module/Platform/README.md) |

### Databases and engine access

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Engine inventory | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Database creation | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Users and credentials | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Privileges | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Remote access | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Data import | [Data](src/Module/Data/README.md) | [Files](src/Module/Files/README.md) |
| Data export | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Backup and restore | [BackupRestore](src/Module/BackupRestore/README.md) | [Data](src/Module/Data/README.md) |
| Connections and locks | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Slow queries | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| PostgreSQL extensions | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Deletion and dependencies | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |
| Panel data separation | [Data](src/Module/Data/README.md) | [Operations](src/Module/Operations/README.md) |

### Mail services and deliverability

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Mail domains | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Mailboxes | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Quotas and sending limits | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Aliases and recipient groups | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Forwarders and catch-all | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Vacation replies | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Sieve filters | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| SMTP, IMAP, and authentication | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Autoconfig and Autodiscover | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Webmail access | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Mail queue | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| SPF, DKIM, and DMARC | [Mail](src/Module/Mail/README.md) | [Hosting](src/Module/Hosting/README.md) |
| DKIM key rotation | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Baseline mail protection | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Outbound abuse and reputation | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| MTA-STS and TLS-RPT | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Deliverability diagnostics | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |
| Mail logs | [Mail](src/Module/Mail/README.md) | [Operations](src/Module/Operations/README.md) |

### File management and web transfers

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| File explorer | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Creation and renaming | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Copy and move | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Uploads | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Downloads | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Text editor | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Edit conflicts | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Archives | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| File permissions | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Deletion and trash | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Search and size calculation | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Path protection | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |
| Operation history | [Files](src/Module/Files/README.md) | [Operations](src/Module/Operations/README.md) |

### SSH, SFTP, and technical access

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Server SSH configuration | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Tenancy](src/Module/Tenancy/README.md) |
| User keys | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md), [Platform](src/Module/Platform/README.md) |
| SFTP accounts | [Tenancy](src/Module/Tenancy/README.md) | [Files](src/Module/Files/README.md), [Platform](src/Module/Platform/README.md) |
| Delegated shell access | [Tenancy](src/Module/Tenancy/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) |
| Access restrictions | [Tenancy](src/Module/Tenancy/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) |
| Administrator lockout prevention | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Tenancy](src/Module/Tenancy/README.md) |
| SSH host keys | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Tenancy](src/Module/Tenancy/README.md) |
| Sessions and logs | [Observability](src/Module/Observability/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) |
| Credential rotation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Tenancy](src/Module/Tenancy/README.md), [Platform](src/Module/Platform/README.md) |

### Local backups, restoration, and retention

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Backup policies | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Per-resource backups | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Recovery points | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Execution and progress | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Data consistency | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Encryption | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Verification | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Retention and pruning | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Targeted restoration | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Restore preparation | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Restore results | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |
| Independent recovery access | [BackupRestore](src/Module/BackupRestore/README.md) | [Operations](src/Module/Operations/README.md) |

### Migration and transfers between instances

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Source inventory | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| Compatibility checks | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| Resource mapping | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| Destination capacity | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| Tracked transfers | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| Final synchronization | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| Cutover preparation | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| Post-transfer validation | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| Recovery and cleanup | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |
| External formats | [Transfers](src/Module/Transfers/README.md) | [Operations](src/Module/Operations/README.md) |

### System, services, packages, and local storage

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Server identity | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| System users and groups | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Services | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Service dependencies | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Processes | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Packages and repositories | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Security updates | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| System scheduling | [Operations](src/Module/Operations/README.md) | [Platform](src/Module/Platform/README.md) |
| Volumes and mounts | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Disk capacity and health | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| System logs | [Observability](src/Module/Observability/README.md) | [Platform](src/Module/Platform/README.md) |
| Maintenance and reboot | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |

### Networking, IP addresses, and firewall

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Interface inventory | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| IPv4 and IPv6 | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| IP pools and reservations | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Routes and gateways | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Resolvers and local naming | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Ports and sockets | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Firewall rules | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Blocks and exceptions | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Network diagnostics | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |
| Sensitive network changes | [Platform](src/Module/Platform/README.md) | [Operations](src/Module/Operations/README.md) |

### Security, secrets, and baseline compliance

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Security center | [Observability](src/Module/Observability/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) |
| Baseline hardening | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md) |
| Sign-in attack prevention | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Platform](src/Module/Platform/README.md), [Observability](src/Module/Observability/README.md) |
| Installed protection integrations | [Platform](src/Module/Platform/README.md) | [Mail](src/Module/Mail/README.md), [Observability](src/Module/Observability/README.md) |
| Application secrets | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Platform](src/Module/Platform/README.md), [Observability](src/Module/Observability/README.md) |
| Sensitive-data redaction | [Observability](src/Module/Observability/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md) |
| Internal keys and certificates | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md) |
| Request validation | [IdentityAccess](src/Module/IdentityAccess/README.md) | [Platform](src/Module/Platform/README.md), [Observability](src/Module/Observability/README.md) |
| Change auditing | [Observability](src/Module/Observability/README.md) | [Operations](src/Module/Operations/README.md) |
| Evidence retention | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |

### Metrics, alerts, incidents, and diagnostics

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Server health | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| System metrics | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| Per-account metrics | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| History and trends | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| Service checks | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| Alert rules | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| Notification center | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| Local alert fallback | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| Incident management | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |
| Diagnostic bundles | [Observability](src/Module/Observability/README.md) | Owning domain and shared contracts |

### Jobs, reconciliation, and long-running operations

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Task center | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Job persistence | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Idempotency | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Locks and concurrency | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Preconditions | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Steps and checkpoints | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Recovery and retries | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Cancellation and compensation | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Timeouts and resource budgets | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |
| Reconciliation | [Operations](src/Module/Operations/README.md) | Owning domain and shared contracts |

### Configuration, APIs, and application exchanges

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Hierarchical configuration | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) |
| Configuration validation | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) |
| Diff and apply | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) |
| REST API | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) |
| Identifiers and correlation | [Operations](src/Module/Operations/README.md) | [Platform](src/Module/Platform/README.md) |
| Tokens and restrictions | [IdentityAccess](src/Module/IdentityAccess/README.md) | Owning domain and shared contracts |
| Outbound webhooks | [Operations](src/Module/Operations/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) |
| Inbound webhooks | [Operations](src/Module/Operations/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Platform](src/Module/Platform/README.md) |
| Structured import and export | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) |
| Capability detection | [Platform](src/Module/Platform/README.md) | [IdentityAccess](src/Module/IdentityAccess/README.md), [Operations](src/Module/Operations/README.md) |

### Installation, updates, and panel autonomy

| README capability | Primary owner | Collaborators |
| :--- | :--- | :--- |
| Prerequisite checks | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| Installation status | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| Initial configuration | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| Update verification | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| Panel migrations | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| Controlled maintenance | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| Version rollback | [Setup](src/Module/Setup/README.md) | [Platform](src/Module/Platform/README.md), [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| License entitlement status | [Tenancy](src/Module/Tenancy/README.md) | [ExtensionManagement](src/Module/ExtensionManagement/README.md) |
| Extension independence | [ExtensionManagement](src/Module/ExtensionManagement/README.md) | [Setup](src/Module/Setup/README.md), [Hosting](src/Module/Hosting/README.md), [Mail](src/Module/Mail/README.md), [Data](src/Module/Data/README.md), [Files](src/Module/Files/README.md), [BackupRestore](src/Module/BackupRestore/README.md) |
| Private-service availability | [ExtensionManagement](src/Module/ExtensionManagement/README.md) | [Setup](src/Module/Setup/README.md) |

## Verification scope

The inventory parser counted 21 capability sections/233 unique domain-and-label pairs and 21 extension families/149 entries, matching the README totals. All mapped module names must exist and relative source links must resolve in the planned repository tree. These structural checks do not execute product workflows or establish production readiness.
