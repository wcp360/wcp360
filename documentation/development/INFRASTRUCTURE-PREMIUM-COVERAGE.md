# Infrastructure, performance and security coverage

**Planning scope: November 2, 2026.** This register makes nine requested domains explicit in the Community/PRO strategy. It links the authoritative proposal catalogues and defines acceptance boundaries; it provides no runtime, provider admission, qualified infrastructure service or performance guarantee.

There are **43 business-module proposals and 76 connector proposals**, all design-only and non-installable. The [module JSON](../../WCP360_Modules/CATALOG.json) and [connector JSON](../../WCP360_Plugins/premium/CATALOG.json) remain their authorities. Their IDs refer to independently packaged optional workflows, not extra built-in Core modules. The thirteen Core owners, 233 essential capabilities and original 149 design entries retain their existing scope and delivery status.

Read the [edition strategy](EDITIONS-AND-PREMIUM.md), [Core status](../../WCP360_Core/STATUS.md), [Core structure](../../WCP360_Core/STRUCTURE.md) and [AGPL licensing](../../WCP360_Core/LICENSING.md) alongside this document.

## Coverage and ownership

| Requested domain | Optional business workflow | Core responsibility retained |
| :--- | :--- | :--- |
| Multi-server hosting and management | [Multi-server Hosting Pro](../../WCP360_Modules/multi-server-hosting-pro/README.md): node groups, workload placement, fleet maintenance and coordinated resource operations. | Platform identities/capabilities, each resource owner's inventory, current IAM/tenant authority, Operations jobs and fencing; essential standalone operation and recovery. |
| Storage management | [Storage Orchestration Pro](../../WCP360_Modules/storage-orchestration-pro/README.md): advanced pools, distributed backends, tiering, replication, snapshots and migrations. | Platform devices/volumes/mounts, Files/Data ownership, Tenancy quotas, baseline capacity monitoring and BackupRestore recovery. |
| Docker management | [Docker Management Pro](../../WCP360_Modules/docker-management-pro/README.md): application portfolios, reviewed stacks, images, networks, volumes, health and update campaigns. | Any admitted container-provider foundation, Hosting instances/ingress, current authorization, resource limits, durable jobs and essential recovery. |
| Kubernetes management | [Kubernetes Management Pro](../../WCP360_Modules/kubernetes-management-pro/README.md): qualified cluster/namespace workloads, ingress, storage and multi-cluster maintenance workflows. | Core resource bindings, tenant authority, secrets, jobs and recovery; a cluster credential never replaces Core authorization. |
| VPS and VM management | [VPS/VM Management Pro](../../WCP360_Modules/vps-vm-management-pro/README.md): templates, guest lifecycle, resource plans, consoles, backups and qualified migration. | Platform resource/identity ownership, network/storage constraints, quota enforcement, current permissions and admitted effect boundaries. |
| Performance booster and compression | [Performance Booster Pro](../../WCP360_Modules/performance-booster-pro/README.md): measured profiles, cache/compression experiments and controlled tuning campaigns. | Ordinary cache/compression configuration, PHP tuning, hosting policy, safe ingress and essential observations remain open-source Core duties. |
| CDN management | [CDN Management Pro](../../WCP360_Modules/cdn-management-pro/README.md): approved provider profiles, origin binding, purge, rollout and propagation evidence. | Hosting origin/TLS, DNS ownership, tenant permissions, protected-file rules and an independent recovery path. |
| Cybersecurity | [Cybersecurity Posture Pro](../../WCP360_Modules/security-posture-pro/README.md), [Threat Response Pro](../../WCP360_Modules/threat-response-pro/README.md) and [Malware Investigation Pro](../../WCP360_Modules/malware-investigation-pro/README.md): advanced correlation, campaigns and investigation. | Essential IAM, MFA policy, isolation, hardening, patch/security controls, firewall, audit, alerts and recovery remain Core duties. |
| DDoS protection | [DDoS Defense Pro](../../WCP360_Modules/ddos-defense-pro/README.md): upstream/CDN/scrubbing coordination and reviewed response plans. | Baseline local firewall/connection/rate controls, safe management access, observations, audit and recovery. |

These workflows cooperate with existing [High Availability](../../WCP360_Modules/high-availability-pro/README.md), [Network Traffic](../../WCP360_Modules/network-traffic-pro/README.md), [Deployment Orchestration](../../WCP360_Modules/deployment-orchestration-pro/README.md), [Application Fleet](../../WCP360_Modules/application-fleet-pro/README.md) and [Disaster Recovery](../../WCP360_Modules/disaster-recovery-pro/README.md). They do not create competing schedulers, resource inventories, permission evaluators or broad privileged executors.

## Provider connectors and overlap

| Family | Proposal examples and relationship |
| :--- | :--- |
| Fleet automation | [AWX controller](../../WCP360_Plugins/premium/awx-controller-pro/README.md) and the existing cloud connectors provide specific integration profiles; they are not a generic remote-shell channel. |
| Containers and clusters | [Docker management adapter](../../WCP360_Plugins/premium/docker-management-adapter-pro/README.md) and [Kubernetes API](../../WCP360_Plugins/premium/kubernetes-api-pro/README.md) implement only future reviewed boundaries. Existing registry connectors still own image-repository integration. |
| Hypervisors and cloud | [Proxmox VE](../../WCP360_Plugins/premium/proxmox-ve-pro/README.md), [libvirt/KVM](../../WCP360_Plugins/premium/libvirt-kvm-pro/README.md), [Incus](../../WCP360_Plugins/premium/incus-pro/README.md) and [OpenStack](../../WCP360_Plugins/premium/openstack-pro/README.md) require independent provider/version/isolation qualification. |
| Storage backends | [Ceph RGW](../../WCP360_Plugins/premium/ceph-rgw-pro/README.md), [MinIO](../../WCP360_Plugins/premium/minio-storage-pro/README.md) and [TrueNAS/ZFS](../../WCP360_Plugins/premium/truenas-zfs-pro/README.md) complement existing remote-backup/object-storage connectors. Object API access does not grant raw block-device or whole-cluster administration. |
| CDN and caching | Existing [Cloudflare CDN](../../WCP360_Plugins/premium/cloudflare-cdn-pro/README.md) and [Fastly](../../WCP360_Plugins/premium/fastly-edge-pro/README.md) are enriched; [Bunny CDN](../../WCP360_Plugins/premium/bunny-cdn-pro/README.md) and [Varnish cache](../../WCP360_Plugins/premium/varnish-cache-pro/README.md) add distinct provider profiles. |
| Defensive security | [CrowdSec](../../WCP360_Plugins/premium/crowdsec-defense-pro/README.md), [ModSecurity/CRS](../../WCP360_Plugins/premium/modsecurity-crs-pro/README.md), [Coraza](../../WCP360_Plugins/premium/coraza-waf-pro/README.md) and [upstream DDoS scrubbing](../../WCP360_Plugins/premium/upstream-ddos-scrubbing-pro/README.md) expose bounded specialist integration, without charging for essential Core protection. |

The full proposal lists stay in the [business catalogue](../../WCP360_Modules/CATALOG.md) and [connector catalogue](../../WCP360_Plugins/premium/CATALOG.md). A provider-specific connector translates reviewed operations; its business module coordinates the workflow. Neither is proof that a provider, API revision or upstream product license is available on a target server.

## Multi-server hosting and management

Distinguish control-plane, hosting, database, mail, storage, edge and hypervisor roles. Inventory node identity, platform/architecture, provider capabilities, capacity, assigned resources and observation freshness. Placement and transfers must use current quota, ownership and compatibility decisions, with explicit drain, maintenance and retirement plans.

Before effects, admit the exact node/instance identity and transport, bound credentials to the accepted scope, recheck current authority and fence stale workers. Qualify enrollment, revocation, certificate rotation, reboot, partition, conflicting controllers, lost acknowledgements and restart. Unknown/offline is not healthy or empty. A node lease is not tenant permission, and vendor subscription loss must not remove local supported recovery or create a second concurrent writer.

## Storage management

Plan local and distributed pools, block/file/object profiles, capacity/inodes, tiering, encryption/key references, snapshots/clones, replication, expansion and relocation. Keep the live consumers and actual ownership visible; account for UID/GID mapping, mount namespaces and enforced limits.

Admit each provider separately. Refuse detach, unmount, deletion or connector removal while live consumers lack an accepted transfer/recovery path. A snapshot alone is not a backup: verify consistent application data, independent retention and restoration. Test full capacity, degraded replicas, unavailable keys, failed migration and restoration without assuming a code rollback can restore data.

## Docker management

Cover reviewed application stacks, immutable image references, registry provenance, network/port policy, persistent volumes, typed secret references, health/log views, measured CPU/RAM/PID/I/O limits, controlled restart/update and backup/restore. Discourse and other container recipes still require individual [App Deploy qualification](../../WCP360_App_Deploy/docs/QUALIFICATION.md).

No panel tenant or plugin receives a host Docker socket or unrestricted daemon API. Admit constrained operations through the owning Core provider; distinguish rootless and rootful profiles. Reject unapproved host mounts, devices, capabilities, host namespaces and privileged containers. Test cross-tenant access, exhausted resources, image trust changes, interrupted updates and removal while volumes remain recoverable.

## Kubernetes management

Plan scoped cluster registration, namespaces, Deployments/StatefulSets/Jobs, Services, ingress or qualified Gateway profiles, TLS, PV/PVC/storage classes, secrets, quotas, observation and rollout/restore workflows. Cluster installation, upgrade, node maintenance and multi-cluster actions require separate explicit infrastructure authority and acceptance.

A namespace alone does not prove isolation. Restrict service accounts and RBAC; exclude broad cluster-admin/wildcard and escalation/impersonation privileges from tenant profiles. Qualify Pod Security, network isolation, quotas, image policy and admission behavior. Deny privileged/host-namespace/hostPath workloads unless a separately accepted infrastructure profile explicitly owns them. Test revoked credentials, operator/webhook effects, cluster-scope resources, node drains and retained persistent data.

## VPS and VM management

Cover VM/container templates, immutable images, cloud-init customization, CPU/RAM/overcommit plans, disks, NIC/IP assignment, lifecycle, authorized consoles, snapshots, backups, guest observations and migration plans. Determine whether a profile provisions cloud instances, manages a hypervisor or delegates a guest; these are different authorities.

Guest root does not grant host root. Qualify KVM VMs and shared-kernel system containers separately. Confine cloud-init, console credentials, disk paths and any device/passthrough choices to the accepted profile; no guest input becomes a host shell hook. Test host/guest separation, ownership, resource exhaustion, interrupted migration and restoration. Claims of live migration, availability or recovery objectives require measured evidence for the exact topology.

## Performance booster and compression

Core Hosting already owns caching and compression in the [233-capability map](../../WCP360_Core/CAPABILITY-MAP.md). PRO adds optional comparison, policy profiles and coordinated tuning; ordinary gzip/Brotli where supported, safe cache settings and PHP runtime tuning do not become premium-only features.

Respect the adopted [web-server strategy](../../WCP360_Core/src/Module/Hosting/WEB-SERVER-STRATEGY.md): the Nginx → Apache event → PHP-FPM target keeps all file authorization at Apache and disables edge direct-file/response-cache shortcuts by default. An accelerated handoff or cache profile needs explicit admission and protected-file/nested-rule acceptance before use. Compression is separate from permission to cache publicly.

Test sessions, Authorization, cookies, admin paths, carts and private APIs; bind cache keys/purge to tenant, host and representation. Preserve Cache-Control, Set-Cookie and Vary semantics. Exercise encoding negotiation, already-compressed files, double compression, CPU/memory/disk pressure, stampedes and rollback. Review secret-bearing dynamic responses for compression side channels. Publish latency/throughput/resource measurements with versions, workload, cold/warm state and error rates; no universal speed claim follows from a booster switch.

## CDN management

Define zone/origin bindings, DNS and certificate ownership, approved cache/routing policies, scoped purge, propagation observations and safe retirement. Keep origin TLS validation and admitted proxy/client-IP trust explicit. A request-supplied URL, hostname or forwarding header does not select a trusted origin.

Apply the same protected-content and Apache authorization boundary as performance profiles: no transparent CDN cache activation for the default target. Qualify private content, cache poisoning, cross-tenant purge, stale policy, IPv4/IPv6, HTTP/TLS variants, provider outage, partial propagation and rollback. A CDN subscription neither transfers ownership of a domain nor guarantees that the origin is unreachable directly.

## Cybersecurity

Keep basic security in the Core; specialist packages add posture comparisons, advisory/scanner ingestion, WAF policy campaigns, integrity evidence, SIEM correlation and reviewed remediation. Record source, version, timestamp, scope, skipped checks and collection failures; a scanner result is not proof that a host is secure.

Separate detection from authorized remediation or quarantine. Test false positives, malicious log/sample content, ruleset changes, resource pressure and restoration. Remediation must preserve management access and independent recovery. This catalogue authorizes no unsolicited third-party scanning or attack simulation; qualification uses synthetic fixtures and explicitly authorized isolated environments.

## DDoS protection

Separate upstream volumetric L3/L4 mitigation from application-layer L7 controls and local connection/rate limits. Describe actual protected resources, traffic paths, origins, addresses/protocols, provider coverage and observation quality before proposing a change.

A host-local filter cannot guarantee availability of an already saturated upstream link. Qualify upstream/CDN/scrubbing integration, legitimate traffic and false positives, origin exposure, trusted client IP handling, provider outage, emergency policy reversal and continued local administration/recovery. Keep mitigations tenant/resource scoped; inspect existing DNS, TLS and routing authority before changes. No bandwidth absorption, guaranteed protection or SLA is implied by a proposal. Do not generate attack traffic against production/customer infrastructure.

## Delivery sequence and qualification

| Step | Required outcome |
| :--- | :--- |
| Preserve Core foundations | Follow [M1/M2 and later dependencies](../../WCP360_Core/ROADMAP.md): current authority, identities, durable intent, bounded effects, audit, uncertainty and recovery. No premium package bypasses these gates. |
| Settle typed boundaries | Accept node/provider, resource binding, package, entitlement and recipe contracts; keep independent trust, current permission and commercial rights checks. |
| Qualify bounded single-node pilots | Implement admitted Docker/storage/performance profiles with synthetic refusal, lifecycle and recovery evidence. No generic shell, raw device or daemon proxy. |
| Extend orchestration | Qualify node roles and multi-server placement/fencing before claiming fleet effects; qualify each Kubernetes/hypervisor API profile separately. |
| Qualify edge and defensive workflows | Prove protected-content correctness before accelerated caching/CDN; prove scoped security rules, provider outages and safe reversal before mitigation campaigns. |
| Measure and release | Publish exact artifact/provider/version/OS/architecture/isolation support, license review, performance evidence, backup/restore and removal behavior. Admit each package individually. |

Distinguish the panel host OS, application-node OS, external appliance/hypervisor API and guest OS. An integration profile does not establish that the panel can be installed on a Proxmox/TrueNAS appliance or cluster host. Targets remain Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS for the panel. This target list does not establish that every hypervisor, cluster, storage engine or security product runs on every target; each profile needs its own matrix. The existing [web-server roadmap](../../WCP360_Core/src/Module/Hosting/WEB-SERVER-ROADMAP.md) retains its dependency order.

These documents change no capability maturity, completion estimate or production qualification. Local executable tests remain stopped; static catalogue/link checks are distinct from future disposable-runtime acceptance.

## Technical references

The requirements above follow the project's existing boundaries and use primary documentation for technical constraints: [Docker Engine security](https://docs.docker.com/engine/security/), [Kubernetes multi-tenancy](https://kubernetes.io/docs/concepts/security/multi-tenancy/), [Pod Security Standards](https://kubernetes.io/docs/concepts/security/pod-security-standards/), [Nginx gzip and compression caveats](https://nginx.org/en/docs/http/ngx_http_gzip_module.html), and [Cloudflare's distinction between network and application DDoS coverage](https://developers.cloudflare.com/ddos-protection/about/). These references are design inputs, not certification of any WCP360 integration.
