# Platform integrations and hosting workflow coverage

**Design register: November 2, 2026.** This document reconciles eleven requested areas with Core ownership, optional business packages and provider-specific proposals. It adds no executable integration, accepted production protocol or qualified provider.

The [module catalogue](../../WCP360_Modules/CATALOG.json) and [connector catalogue](../../WCP360_Plugins/premium/CATALOG.json) remain authoritative: **44 business modules and 97 connectors, 141 proposals in total**, all design-only and non-installable. These counts are independent of the preserved 233 Core capabilities, 149 original design entries and 410 App Deploy editorial references. Read the [edition strategy](EDITIONS-AND-PREMIUM.md), [infrastructure coverage](INFRASTRUCTURE-PREMIUM-COVERAGE.md), [Core status](../../WCP360_Core/STATUS.md) and [Core licensing](../../WCP360_Core/LICENSING.md).

**Scope amendment, November 3, 2026:** the [owner's reseller decision](../governance/decisions/OWNER-DECISION-COMMUNITY-RESELLER-2026-11-03.md) supersedes this register's earlier reseller-in-free-Core inclusion. Preserve the eleven-area design and inventories; reseller management/delegation is outside Community, optional future commercial placement remains unconfirmed, and essential tenant policy/IAM/ordinary quotas/isolation/recovery remain Core.

## Coverage matrix

| Requested area | Core duty retained | Optional workflow and integration examples |
| :--- | :--- | :--- |
| 1. Multi-server and cluster management | Platform node identity/capabilities; each owner's resources; current IAM and Operations fencing/reconciliation. | [Multi-server Hosting](../../WCP360_Modules/multi-server-hosting-pro/README.md), [Enhance interop](../../WCP360_Plugins/premium/enhance-interop-pro/README.md), [CSF/lfd profile](../../WCP360_Plugins/premium/csf-lfd-policy-pro/README.md). |
| 2. Docker and application orchestration | Admitted runtime foundations, Hosting instances/ingress, quotas, data, secrets and durable jobs. | [Docker Management](../../WCP360_Modules/docker-management-pro/README.md), [Portainer](../../WCP360_Plugins/premium/portainer-management-pro/README.md), [Coolify](../../WCP360_Plugins/premium/coolify-applications-pro/README.md), [Dokku](../../WCP360_Plugins/premium/dokku-applications-pro/README.md). |
| 3. Storage and replication | Platform volumes/mounts; Files/Data ownership, quotas and independent recovery. | [Storage Orchestration](../../WCP360_Modules/storage-orchestration-pro/README.md), [GlusterFS](../../WCP360_Plugins/premium/glusterfs-storage-pro/README.md), [CephFS](../../WCP360_Plugins/premium/cephfs-storage-pro/README.md), [Ceph RBD](../../WCP360_Plugins/premium/ceph-rbd-storage-pro/README.md), [Nextcloud WebDAV](../../WCP360_Plugins/premium/nextcloud-webdav-pro/README.md). |
| 4. CDN, reverse proxy and edge | Hosting origin/routes/TLS, DNS authority, protected content and single ingress ownership. | [CDN Management](../../WCP360_Modules/cdn-management-pro/README.md), existing Cloudflare/Fastly/Bunny profiles, [Traefik](../../WCP360_Plugins/premium/traefik-routing-pro/README.md), [Nginx Proxy Manager](../../WCP360_Plugins/premium/nginx-proxy-manager-pro/README.md). |
| 5. Collaborative defense and availability | Essential hardening, access/firewall policy, incident handling, audit and recovery. | [Cybersecurity Posture](../../WCP360_Modules/security-posture-pro/README.md), [Threat Response](../../WCP360_Modules/threat-response-pro/README.md), [BitNinja](../../WCP360_Plugins/premium/bitninja-defense-pro/README.md), [Imunify360](../../WCP360_Plugins/premium/imunify360-defense-pro/README.md), existing CrowdSec profile. |
| 6. Reseller packages | Essential account/tenant policy, ordinary hosting resource plans/quotas, IAM, isolation and recovery remain Core. Reseller-specific management/delegation is outside Community. | [Retained reseller design](../../WCP360_Core/src/Module/Tenancy/RESELLER-PACKAGES.md); optional commercial placement, including any PRO inclusion, is unconfirmed and unavailable. [Agency Workspace](../../WCP360_Modules/agency-workspace-pro/README.md) remains a separate design-only proposal. |
| 7. Firewall, WAF, DDoS and integrity | Essential local security, network policy, abuse protection, ownership, observations and recovery. | Existing ModSecurity/CRS, Coraza and upstream DDoS proposals; [Malware Investigation](../../WCP360_Modules/malware-investigation-pro/README.md) coordinates scoped evidence/remediation. |
| 8. Git delivery and App Install | Resource lifecycle, current authorization, admitted providers/jobs, data retention and recovery. | [Git Delivery](../../WCP360_Modules/git-delivery-pro/README.md), existing GitHub/GitLab connectors and [App Install](../../WCP360_Modules/app-install-pro/README.md) with individually qualified recipes. |
| 9. Observability and alerts | Baseline measurements, local alerts, diagnostics and supported incident/recovery access. | Existing Prometheus/Grafana profiles, [Observability](../../WCP360_Modules/observability-pro/README.md), [Incident Operations](../../WCP360_Modules/incident-operations-pro/README.md), Telegram/SMS/email provider proposals. |
| 10. Database-as-a-Service | Data resource ownership, separate hosted identities, quotas, supported database lifecycle and baseline backup/restore. | [Database Operations and DBaaS](../../WCP360_Modules/database-operations-pro/README.md), MySQL/MariaDB, Galera, Redis and MongoDB provider profiles. |
| 11. DNS and certificate automation | Basic/global DNS, zone changes, DNSSEC, ACME, wildcard issuance/renewal/deployment and secret ownership remain Core scope. | [DNS/TLS Orchestration](../../WCP360_Modules/dns-tls-orchestration-pro/README.md), existing PowerDNS/Cloudflare profiles and an [external ACME authority](../../WCP360_Plugins/premium/external-acme-authority-pro/README.md) profile. |

The table is an ownership index, not another catalogue or feature-completion register. A commercial subscription never creates tenant permission, publisher trust, capacity or provider availability.

## 1. Multi-server and cluster management

Plan explicit web/application, database, mail, DNS, storage, edge and control-plane roles, with resource placement and dependencies visible per node. The Core keeps authoritative identities/inventory and durable effect boundaries. Qualification must cover stale observations, trust rotation, partitions, fencing, node retirement, capacity and supported recovery.

Enhance is a separate hosting panel with a published [API](https://apidocs.enhance.com/). Its interop proposal starts with scoped inventory/export/migration; an API key does not import its engine or transfer resource control to WCP360.

Treat the requested ClusterCSF behavior as CSF/lfd policy integration, not a verified independent product or the WCP agent transport. The original ConfigServer business closed, while cPanel publishes its [own CSF fork](https://github.com/cpanel/cpanel-csf) and [maintenance announcement](https://support.cpanel.net/hc/en-us/articles/37654028162071-cPanel-will-provide-its-own-fork-of-CSF-starting-Feb-25th-2026). Select provenance/version, platform compatibility and transport explicitly. Begin with observation/evidence; neither a legacy cluster channel nor a maintained fork is already admitted for WCP360.

## 2. Docker and application orchestration

[Portainer](https://docs.portainer.io/), [Coolify](https://coolify.io/docs/) and [Dokku](https://dokku.com/docs/) are independent management systems. Integration must begin with a declared read/import/migration boundary and one effective controller per workload, volume, secret, route and certificate.

Dokku's built-in [docker-local scheduler](https://dokku.com/docs/deployment/schedulers/scheduler-management/) runs on the local host; multi-host behavior requires a separately selected profile. Do not describe every PaaS as a native multi-node scheduler.

A future handover must fence the old writer, reconcile actual state, admit the new owner and define return/recovery behavior. Do not expose raw Docker sockets, unrestricted remote APIs, deployment scripts, SSH/root hooks or arbitrary Compose through the Core. A foreign UI is not embedded into the Core origin merely because its service is reachable.

## 3. Storage and replication

Separate distributed file storage, block devices and object APIs. GlusterFS and CephFS provide file-oriented profiles; Ceph RBD and Ceph RGW have different data and privilege boundaries. Reuse existing RGW/MinIO/S3 proposals without calling every backend an interchangeable shared folder.

Nextcloud is a file/collaboration application that can [use S3-compatible primary storage](https://docs.nextcloud.com/server/latest/admin_manual/configuration_files/primary_storage.html); this is distinct from being an S3 object-store server. Its [WebDAV API](https://docs.nextcloud.com/server/latest/developer_manual/client_apis/WebDAV/basic.html) is a separate connector surface. Keep application metadata/database and object contents consistent during restore.

Replication does not establish backup, zero data loss or universal performance. Qualify failure domains, quorum/consistency, live consumers, key availability, quotas, snapshot/backup semantics and restoration. Connector removal must retain the backend and data until supported transfer or recovery exists.

## 4. CDN, reverse proxy and edge

Keep the existing Cloudflare DNS/CDN proposals distinct but coordinated: zone/account authority, origin binding, cache policy, purge, TLS and propagation need individual results. A proxied hostname does not guarantee complete origin anonymity or prevent direct access through another address/service. Origin access policy and trusted client-IP handling require qualification.

Traefik and Nginx Proxy Manager are external routing/proxy profiles. The [NPM guide](https://nginxproxymanager.com/guide/) documents proxy hosts, forwarding/streams, TLS and access lists; it does not establish an already qualified WCP multi-server load-balancer or stable integration API.

One controller owns each address/protocol/port, route and certificate. Preserve the [target Apache authorization boundary](../../WCP360_Core/src/Module/Hosting/WEB-SERVER-STRATEGY.md); do not enable edge cache/direct-file shortcuts for that profile until protected/private content has passed acceptance. Alternative stacks have their own admitted policy.

## 5. Collaborative defense and availability

BitNinja, Imunify360 and CrowdSec expose different observations, decisions, scopes and enforcement mechanisms. Do not translate vendor descriptions into a guarantee that every detection is immediately blocked on every node.

Track detected, proposed, admitted, applied, expired and reconciled outcomes as observations/workflow requirements, not new canonical state identifiers. Require current Core authority and explicit node/resource scope; qualify false positives, allowlists, expiration, delayed/offline peers, conflicting rules, rollback and emergency administration.

A security feed is untrusted input. Imunify's [local CLI](https://docs.imunify360.com/command_line_interface/) and its privileged functions are not generic plugin APIs or permission to hand out a root-capable UI token. A future local adapter needs accepted fixed operations and ownership; no optional package obtains a privileged command channel. Security synchronization is distinct from the [High Availability](../../WCP360_Modules/high-availability-pro/README.md) and [DDoS](../../WCP360_Modules/ddos-defense-pro/README.md) workflows.

## 6. Reseller packages

The [retained reseller-package guide](../../WCP360_Core/src/Module/Tenancy/RESELLER-PACKAGES.md) specifies template building, immutable plan versions, resource units, delegated ceilings, aggregate reservations, assignments, overrides, provisioning outcomes and safe downgrade. The [November 3 scope amendment](../governance/decisions/OWNER-DECISION-COMMUNITY-RESELLER-2026-11-03.md) supersedes its earlier free-Core reseller inclusion: reseller-specific workflows are outside Community, with no available commercial package or confirmed PRO inclusion. Preserve these specifications without treating a design reservation as implementation.

Starter, Pro and Enterprise are possible hosting-plan labels; they do not imply a WCP360 edition license. Accounts, bandwidth, CPU/RAM, storage and API budgets need independent measurement/enforcement evidence. A one-click assignment records intent; its infrastructure effects may remain pending, partial or uncertain. Essential customer workspaces, tenant ownership, ordinary resource-plan assignment/quotas, IAM, isolation, audit and recovery stay Core even though reseller portfolios, child-package sales and reseller-specific delegation are outside Community. No AGPL text, canonical role/permission identifier, catalogue count or runtime grant is changed by this scope amendment.

## 7. Firewall, WAF, DDoS and integrity

Keep essential firewall/security administration in the Core. Select a qualified network-policy backend and a single writer; NFTables/UFW/CSF labels do not authorize concurrent independent rule engines. Model typed protocols, ports, addresses/scopes, precedence and safe reversal. GeoIP data needs provenance/freshness and is not proof of identity.

ModSecurity with CRS and Coraza are candidate WAF integrations. The requested ModulWAF label does not select a publisher, API, artifact or license; the functional requirements map to these explicit proposals until an additional product/profile is qualified. Rules may mitigate attack patterns; WAF inspection does not replace Core authentication/authorization or guarantee coverage of every OWASP Top 10 weakness.

Baseline credential-attempt limits and local rate controls remain Core duties. Upstream L3/L4 DDoS mitigation, L7 rules and credential-stuffing protection are separate controls. Use the [DDoS coverage requirements](INFRASTRUCTURE-PREMIUM-COVERAGE.md#ddos-protection), preserving management access during policy errors or provider outage.

ClamAV/LMD-style scanning and integrity evidence require scoped paths, signatures/version/freshness, malicious-name/archive handling, resource limits and current Files/Hosting authority. Separate detection from quarantine or repair, and qualify false positives and restoration. A completed scan is not proof of a secure host.

## 8. Git delivery and App Install

Reuse the GitHub/GitLab connector proposals and Git Delivery workflow. Validate authenticated webhook origin, replay/idempotency, repository/revision identity and current destination authority. Builds/dependencies are untrusted work, isolated from Core secrets; an artifact still needs admission before deployment.

App Install coordinates [App Deploy recipes](../../WCP360_App_Deploy/README.md), not raw Docker Compose submissions. WordPress, Nextcloud and Ghost are applications; Node.js/Python are runtime families and require suitable application recipes. Each recipe needs admitted artifacts/providers, typed inputs, ingress/data ownership, health evidence and tested install/configure/update/backup/restore/remove behavior.

A reviewed Compose-derived recipe may become a controlled provider input only under an accepted contract. A one-click interface cannot install arbitrary images, host mounts, devices, hooks or a second ingress controller. No general installer is delivered by the current catalogues.

## 9. Observability, incidents and notifications

Core metrics, observations, local alerts and diagnostics remain available without optional telemetry vendors. Prometheus/Grafana profiles add qualified collection/federation/dashboards; an external graph does not prove source freshness or service health.

Define HTTP/TCP/DNS and, where admitted, bounded ICMP probe profiles with target authority, egress restrictions, budgets and an explicit interval. Do not expose a generic scan/ping/root channel. Cover timeouts, missing samples, label/cardinality limits, noisy alerts, deduplication and retained incident history.

Add [Telegram](../../WCP360_Plugins/premium/telegram-notifications-pro/README.md), [Twilio SMS](../../WCP360_Plugins/premium/twilio-sms-pro/README.md) and [Brevo email](../../WCP360_Plugins/premium/brevo-email-pro/README.md) as provider profiles, alongside existing Discord/webhook/on-call proposals. Core email/alerts are not paywalled. Scope recipients, redact content, protect secret references and bound retries/costs; provider acceptance is not proof of delivery or human acknowledgement. Public status publication requires separate reviewed redaction and authority.

## 10. Database-as-a-Service and backup

[External MySQL/MariaDB](../../WCP360_Plugins/premium/external-mysql-mariadb-pro/README.md), [Galera](../../WCP360_Plugins/premium/galera-cluster-pro/README.md), [Redis](../../WCP360_Plugins/premium/redis-service-pro/README.md) and [MongoDB](../../WCP360_Plugins/premium/mongodb-service-pro/README.md) are distinct profiles alongside PostgreSQL. Do not present primary/replica replication, Galera quorum, Redis Cluster/Sentinel and MongoDB replica sets as interchangeable.

Keep hosted database identities separate from control-plane PostgreSQL and the panel's optional Redis job transport. Admission must bind the exact provider/version/topology, database ownership, credential scope, effective quota and supported operations. Qualify failover/fencing, replication lag, split-brain risks, maintenance and lost acknowledgements before availability claims.

Baseline backup/restore remains Core scope; optional advanced orchestration reuses existing S3/GCS/SFTP/B2 profiles. Require application-consistent capture, encryption/key custody, retention, remote artifact identity and restore evidence. Deleting a provider/management connector does not delete databases, snapshots or the independent recovery path.

## 11. DNS and certificate automation

Core Hosting owns domains/zones, DNS changes, DNSSEC, certificate identity and basic ACME/wildcard lifecycle. Record types include A, AAAA, **CNAME**, MX and TXT; CMX is not the intended CNAME record type.

The [DNS/TLS orchestration module](../../WCP360_Modules/dns-tls-orchestration-pro/README.md) adds advanced multi-provider/node campaigns and rollout evidence without making ordinary DNS or certificate automation premium-only. Existing PowerDNS/Cloudflare adapters retain their bounds.

[Let's Encrypt DNS-01](https://letsencrypt.org/docs/challenge-types/) supports wildcard validation through scoped TXT updates. Handle propagation, concurrent challenges, narrowly scoped credentials, renewal state and cleanup of only owned challenge values. Persist issuer/account/key/certificate state; do not create a new issuance on every restart. Deploy only to authorized targets with verified name/key/chain identity and rollback/recovery evidence.

External issuers such as ZeroSSL have their own [ACME registration requirements](https://zerossl.com/documentation/acme), including EAB where applicable, terms and operating limits. A provider name does not grant credentials, unlimited service or guaranteed renewal. Keys and DNS proofs remain outside ordinary logs, package metadata and source.

## Admission, implementation and evidence

For every external management system, start with bounded observation/import/migration. A future management mode must explicitly define resource ownership, accepted actions, credential scopes, version support, conflict detection, old-writer fencing, recovery and removal. Do not activate an external controller over resources still actively owned by WCP360.

Follow [Core milestone order](../../WCP360_Core/ROADMAP.md), [package admission](../../WCP360_Core/src/Module/ExtensionManagement/design/packages/README.md) and [application qualification](../../WCP360_App_Deploy/docs/QUALIFICATION.md). Review third-party license/provenance and exact platform/API/architecture/isolation compatibility for each artifact. The panel's five OS targets do not qualify every external appliance or guest.

The sources linked here verify design distinctions; they do not certify an implemented WCP integration. All new packages remain proposals, local executable tests remain stopped, and the Core completion estimate and production qualification remain unchanged.
