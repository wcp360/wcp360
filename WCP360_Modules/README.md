# WCP360 premium business modules

This directory prepares **44 optional business-module proposals in 16 categories**. Every proposal is **design only**, **PRO**, **non-installable** and has **no selected source or licensing model**. There is no implementation, downloadable release, admitted provider or installation command here.

Read the [module catalogue](CATALOG.md) for the proposed scope and each package's acceptance work. Specific protocol/provider connectors belong in the [premium plugin catalogue](../WCP360_Plugins/premium/README.md); a business module and a connector use the same detached extension mechanism.

## Explicit advanced coverage

| Requested area | Dedicated optional workflow | Retained free Core boundary |
| :--- | :--- | :--- |
| Multi-server hosting and management | [Multi-server Hosting Pro](multi-server-hosting-pro/README.md): qualified Web/database/mail/DNS roles, enrollment/trust, placement, routing, fencing and fleet care. | Single-node hosting and future Core-owned node/provider identity, authority and ordinary resource control; convergence requires per-node evidence. |
| Storage management | [Storage Orchestration Pro](storage-orchestration-pro/README.md): local/GlusterFS/Ceph block, file and object tiers, capacity, encryption, snapshots and safe migration. | Essential native storage, quotas, ownership and independent recovery; Nextcloud is an application consuming storage, not an S3 server. |
| Docker management | [Docker Management Pro](docker-management-pro/README.md): typed admitted Compose plans, images, workloads, volumes, networks, updates and restore; qualified Portainer/Coolify/Dokku integrations select one effect owner. | Native admitted provider foundations and essential hosting/isolation stay open source; no host socket or raw YAML/shell passthrough. |
| Kubernetes management | [Kubernetes Management Pro](kubernetes-management-pro/README.md): namespaces/RBAC, workloads, ingress/TLS, PVs, quotas, upgrades and multi-cluster recovery. | Core resource authority; a namespace alone is not proof of tenant isolation. |
| VPS / VM management | [VPS / VM Management Pro](vps-vm-management-pro/README.md): hypervisors, templates/cloud-init, virtual networks, quotas, snapshots and qualified migrations. | Admitted native VM/provider foundations and baseline network/storage/security remain free; guest-root does not confer host-root. |
| Booster and compression | [Performance Booster Pro](performance-booster-pro/README.md): comparative measurements and advanced qualified optimization profiles/campaigns. | Usual cache/compression, admitted native gzip/Brotli and OPcache stay free; Apache authorization cannot be bypassed. |
| CDN and ingress | [CDN Management Pro](cdn-management-pro/README.md): qualified Cloudflare/provider cache, TLS/origin policies and scoped purge. [Network Traffic Pro](network-traffic-pro/README.md): distinct Traefik/Nginx Proxy Manager ingress and routing workflows. | Domain/DNS/TLS and ordinary networking remain Core-owned; protected files cannot become public at an edge and only one ingress controller owns a resource. |
| Cybersecurity | [Cybersecurity Posture Pro](security-posture-pro/README.md): comparative evidence, exceptions and staged cluster-policy campaigns; [response](threat-response-pro/README.md) and [investigation](malware-investigation-pro/README.md) keep distinct owners. | Essential hardening, firewall, abuse controls, audit and recovery stay free; partial policy convergence is visible and never called instantaneous. |
| DDoS defense | [DDoS Defense Pro](ddos-defense-pro/README.md): upstream L3/L4 coordination and separately qualified local/edge L7 controls. | Baseline protection and recovery; local rules cannot guarantee protection from upstream link saturation. |
| Git, CI/CD and application store | [Git Delivery Pro](git-delivery-pro/README.md) and [AppInstall Pro](app-install-pro/README.md): isolated builds, immutable artifacts and qualified native/Compose-derived recipes through admitted operations. | Core deployment/resource authority and ordinary application hosting remain free; a repository, Compose file or editorial catalogue is not runtime admission. |
| Monitoring and notifications | [Observability Pro](observability-pro/README.md) and [Incident Operations Pro](incident-operations-pro/README.md): measured multi-vantage HTTP/TCP/DNS/ICMP campaigns and advanced coordination. | Ordinary admitted checks, essential alerts and email/SMS/Telegram notification routing remain free Core responsibilities; delivery requires actual channel evidence. |
| Databases and DBaaS | [Database Operations & DBaaS Pro](database-operations-pro/README.md): advanced service/topology campaigns for separately qualified MySQL/MariaDB/PostgreSQL, customer Redis, MongoDB and Galera. | Basic hosted database creation, identities/grants, administration and local recovery stay free; replicas/quorum are not verified backups and no listed provider is implemented here. |
| Agencies and resellers | [Agency Workspace Pro](agency-workspace-pro/README.md): advanced delegated portfolio views, branding and reviewed handoffs. | Core Tenancy keeps reseller packages, template builders, quotas, versioning, assignment, scoped overrides and delegation free. |
| DNS and certificates | [DNS & TLS Orchestration Pro](dns-tls-orchestration-pro/README.md): advanced cross-provider/node import/reconciliation, staged propagation/certificate rollout and portfolio evidence. | Ordinary DNS and PowerDNS/admitted engines, DNSSEC, ACME, wildcard DNS-01, renewal/deployment and basic automation stay free; no second zone/certificate authority. |

All are proposals, not working management products. Named products and protocols identify qualification targets, not supported releases, automatic failover or measured performance. Provider/API versions, identities, network/data isolation, resource budgets and lifecycle/recovery must be qualified before implementation claims. Premium orchestration never receives host sockets, raw root commands or direct Core database access.

## Authoritative planning records

| Record | Responsibility |
| :--- | :--- |
| [CATALOG.json](CATALOG.json) | Authoritative collection of complete planning proposals; package IDs and categories are defined here. |
| [CATALOG.md](CATALOG.md) | Generated readable view of that collection. |
| Each package.proposal.json | Exact materialized copy of its catalogue entry, not a second registry. |
| Each package README and docs/ACCEPTANCE.md | Proposed behavior, retained Core boundary and package-specific acceptance. |
| [Planning schema](schemas/package-proposal.schema.json) | Closed proposal structure for module and plugin records; passing it grants no runtime authority. |
| [Authoring template](_template/README.md) | Future detached source layout, data/identity boundaries and implementation preparation. |

Update CATALOG.json first, then regenerate its Markdown view and identical per-package records/documents. Keep their IDs and fields synchronized. Categories are metadata, not extra package-directory levels. Paths derive from module.<slug> as WCP360_Modules/<slug>/.

The source reference is the unchanged [149-entry design catalogue](../PLUGIN_CATALOG.md). Its keys are documentary references, not package IDs, approvals, grants or compatibility results. The seven engine.* Core adapters are not premium packages and are not targeted by these proposals. The [410 App Deploy editorial references](../WCP360_App_Deploy/README.md) are a separate inventory, not 410 qualified recipes.

## Preserve the free Core

All **233 essential Core capabilities**, the thirteen Core owners and the seven essential engine-adapter entries stay in the open-source Core scope. Premium modules add advanced workflows, specialist orchestration or optional integrations; they do not gate ordinary hosting, login, optional MFA, quotas, files, hosted databases, baseline mail/security, local monitoring, local backup/restore, export or recovery.

Core remains the authority for resources, policy, permissions, jobs and admitted effects. Future Core-owned node/provider identity, routing, fencing and ordinary admitted Docker/VM/storage/network control remain open-source foundations; optional premium workflows cannot become their only control or recovery path. A module runs separately with its own reviewed process, identity, data and eventual dependency graph. It cannot inject PHP/Twig/bundles into Core, write Core tables, receive a privileged socket or run arbitrary root commands.

PRO entitlement, package trust/compatibility and current action permission are separate checks. Expiry, provider outage, disablement or removal must preserve existing workloads and independent Core control/recovery. An optional connector failure cannot create a fallback privilege or silently repeat an uncertain effect.

## Licensing and qualification

The selected Core license is **AGPL-3.0-only**; consult [Core licensing](../WCP360_Core/LICENSING.md) and the [editions/premium strategy](../documentation/development/EDITIONS-AND-PREMIUM.md). This does not choose a premium-package license or establish that a particular linkage/distribution arrangement is legally compatible. Source availability, package terms, dependencies and applicable upstream obligations require explicit review before implementation/release.

No price, subscription duration, license-server endpoint or commercial enforcement protocol is defined here. Each proposal retains source_model: undecided. Dependencies are currently empty because no inter-package dependency is selected; the listed Core contracts and qualified providers are still prerequisites, not already available implementations.

A future package must provide qualified versions, provenance, upstream-license review, current-authorization checks, failure/recovery evidence and explicit exit behavior. AppInstall Pro requires complete independently qualified application recipes; a catalogue row alone must never enable installation.

Only static planning artifacts are supplied. No local runtime/test/compiler execution is implied by these documents.
