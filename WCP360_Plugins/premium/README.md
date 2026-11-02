# Premium connector proposals

This folder contains **97 design proposals in 19 categories** for optional WCP360 connectors. Every proposal is `design`, `installable: false` and `source_model: undecided`. These documents provide an authoring and review inventory; they do not provide implemented connectors, admitted manifests, signed releases, working credentials or an installer.

The Core licensing choice is **AGPL-3.0-only**. Read [Core licensing](../../WCP360_Core/LICENSING.md) and [editions and premium boundaries](../../documentation/development/EDITIONS-AND-PREMIUM.md). No premium package license, source distribution model, price, entitlement key or vendor endpoint is selected here. A separate process or API does not by itself establish legal independence or permission to distribute a proprietary derivative; the actual design and distribution need licensing review.

## Catalogue authority and files

- [CATALOG.json](CATALOG.json) is the authoritative catalogue: `wcp360.premium-package-catalogue/1`, `count: 97`, and complete proposal objects in `packages`.
- [CATALOG.md](CATALOG.md) is its readable view, with one link per proposal.
- Each `<slug>/package.proposal.json` is an exact data mirror of its catalogue entry. Edit the authority and synchronize the mirrors and Markdown view together.
- The shared [proposal schema](../../WCP360_Modules/schemas/package-proposal.schema.json) defines the 15 closed fields. Valid JSON or schema validation does not establish publisher trust, package admission, permissions, license compatibility or installability.
- [_template](_template/README.md) is a separate documentation example, excluded from the 97-entry count. It does not replace the existing [plugin authoring template](../_template/README.md).

Each proposed package has three useful documents:

```text
<slug>/
├── README.md                  # Intent, Core boundary and references
├── package.proposal.json      # Synchronized proposal metadata
└── docs/
    └── ACCEPTANCE.md          # Evidence required before implementation/admission
```

There are no placeholder executable sources, production configuration files, lifecycle hooks, generated artifacts or production manifests in this inventory.

## Categories

| Category | Proposals | Scope |
| :--- | ---: | :--- |
| [Cloud infrastructure](CATALOG.md#cloud-infrastructure) | 6 | Selected external accounts, projects, regions and node resources. |
| [DNS, CDN and domains](CATALOG.md#dns-cdn-domains) | 9 | DNS providers, DNS-01, registrars and explicitly admitted CDN/performance policies. |
| [Backup and object storage](CATALOG.md#backup-object-storage) | 7 | Optional remote backup destinations and application media stores. |
| [Observability and SIEM](CATALOG.md#observability) | 6 | Scoped metrics, traces, logs, incidents and security exports. |
| [Identity and secrets](CATALOG.md#identity-secrets) | 6 | Federation, external directories and secret/key references. |
| [Billing and business systems](CATALOG.md#billing-business) | 6 | Payment events, optional billing systems and CRM mappings. |
| [Collaboration and support](CATALOG.md#collaboration-support) | 9 | Redacted collaboration, Telegram/SMS/transactional email and incident destinations. |
| [Git, CI and artifact registries](CATALOG.md#git-ci-registries) | 7 | Source-provider events and immutable artifact references. |
| [AI and MCP](CATALOG.md#ai-mcp) | 4 | Optional redacted assistance, selected models and admitted MCP HTTP. |
| [Migration and data transfer](CATALOG.md#migration-data) | 5 | Source-specific portfolio/mail/database migration and read-scoped Enhance interoperability. |
| [Docker, Kubernetes and virtualization](CATALOG.md#compute-platforms) | 7 | Core-mediated Docker/virtualization profiles and separately owned Portainer environments. |
| [Storage platforms](CATALOG.md#storage-platforms) | 6 | Distinct RGW objects, CephFS files, RBD blocks, GlusterFS and dataset/share provider contracts. |
| [Cybersecurity and DDoS defense](CATALOG.md#security-defense) | 7 | Scoped provider evidence, defensive decisions and provenance-qualified single-writer interoperability. |
| [Multi-server automation](CATALOG.md#multi-server-automation) | 1 | Admitted controller inventories and bounded reviewed multi-server job templates. |
| [Performance and HTTP caching](CATALOG.md#performance-cache) | 1 | Qualified external-cache profiles; native compression and default Apache routing stay Core. |
| [Application platform interoperability](CATALOG.md#application-platforms) | 3 | Coolify/Dokku external application profiles and authenticated Nextcloud/WebDAV clients. |
| [Proxy platform interoperability](CATALOG.md#proxy-platforms) | 2 | Traefik observations/provider plans and NPM export/import without ingress takeover. |
| [External and tenant data services](CATALOG.md#database-services) | 4 | Tenant-owned external MySQL/MariaDB, Galera, Redis and MongoDB profiles. |
| [Additional certificate authorities](CATALOG.md#certificate-providers) | 1 | Additional ACME account/authority policy; baseline TLS, renewal and wildcard stay Core. |

## Relationship to Core and existing catalogues

The [149-entry integration catalogue](../../PLUGIN_CATALOG.md) remains the source of design keys. A proposal can reference several existing keys; 97 connector proposals do not mean 97 additional essential Core capabilities or 97 implemented integrations. The seven `engine.*` adapters and the **233 essential hosting capabilities** remain in free Core scope. Native web/DNS/mail/firewall/isolation control, local authentication, tenant permissions, basic transfers and recovery are not moved behind a premium connector.

The 44 optional [business module proposals](../../WCP360_Modules/CATALOG.md) describe operator workflows; these 97 plugin proposals describe optional provider/protocol integrations. Links between compatible ideas do not establish runtime dependencies. All package `dependencies` arrays are currently empty. Core owners express required admission boundaries, not installable package IDs.

The actual Core APIs, SDK, detached process contracts and provider adapters still require implementation and qualification. Check [Core status](../../WCP360_Core/STATUS.md), [the public SDK](../../WCP360_SDK/README.md) and [plugin lifecycle requirements](../../documentation/development/PLUGIN-LIFECYCLE.md).

## Explicit infrastructure, performance and security boundaries

The inventory includes optional Docker fleet, Kubernetes, Proxmox VE, libvirt/KVM, Incus, OpenStack and AWX connectors. Native container/VM control, node ownership, quotas, concurrency and host recovery remain in Core. Tenant workloads and guests never inherit hypervisor, cluster-admin, daemon-socket or privileged-broker authority.

Storage proposals distinguish backup destinations from Ceph RGW/MinIO object gateways and TrueNAS/ZFS dataset/share integrations. Disconnecting a service or removing a connector is distinct from deleting objects, volumes, datasets or retained backups.

Cloudflare and Fastly are enriched in place, with Bunny and a Varnish-compatible cache profile added. **Compression is distinct from public caching.** The target Nginx-to-Apache all-traffic profile keeps direct-file and response caching off so that Apache and nested `.htaccess` rules remain authoritative. Cache/CDN handoff needs a separately admitted profile and protected-file, authorization-revocation, private-response, cookie, purge, origin/TLS and client-IP evidence. No blanket speedup is promised.

CrowdSec, ModSecurity CRS and Coraza proposals cover defensive observations and reviewed policy workflows. Upstream scrubbing concerns **L3/L4 mitigation** and is separate from local **L7 WAF/rate-limit/firewall** controls. Local filtering cannot guarantee protection from an already saturated uplink. Provider coverage, routing rollback, origin bypass and IPv4/IPv6/UDP/HTTP3 behavior remain qualification work; no live customer attack or guaranteed attack-absorption result is authorized.

## Vendor interoperability and ownership modes

Each new vendor profile declares observation-only, import or externally managed ownership. An external manager must not compete with Core for the same host, container, ingress, certificate, storage or database. Import and cutover require reviewed inventory, current authorization and a recovery plan; no connector silently takes over a live Core resource.

Portainer/Coolify APIs, Dokku SSH/local scheduler behavior, Traefik GET observations and NPM proxy/export concepts are different contracts. A vendor UI does not prove a safe generic API. Native Docker/proxy controls remain Core-owned, and plugins receive no daemon socket, free-form Compose, host shell or privileged broker access.

GlusterFS, CephFS, RBD and RGW preserve separate quorum, file/block/object, client-capability and recovery constraints. Nextcloud is an application/WebDAV boundary; its optional S3 primary backend does not make it a MinIO or Ceph replacement.

BitNinja provider API evidence, local Imunify360 CLI observations and CSF/lfd peer evidence have separate admission paths. CSF fork provenance and maintenance must be checked; no fork is selected or claimed supported by WCP360. There is no assumed separate official ClusterCSF product. Detection, policy admission and effective enforcement are tracked per node with delay, partition, expiry, exception and rollback evidence; collaborative defense is not instantaneous consensus.

External/tenant database profiles never expose the Core datastore or Redis job transport. Notifications keep verified destinations, consent, redaction and delivery uncertainty explicit. **Local email and essential alerts, DNS/TLS, ACME renewal and wildcard DNS-01 remain free Core capabilities**; Brevo, Twilio, Telegram and an additional ACME authority are optional provider integrations.

Enhance's published API justifies a scoped inventory/migration proposal, alongside its architectural comparison value. It is not imported as a WCP360 engine or allowed to remain a second active controller after a resource handoff.

## Qualification before any release

1. Select a concrete provider/interface profile, compatibility matrix and immutable package artifact. Record supported revisions and platform/architecture requirements only after evidence exists.
2. Review source distribution, dependency provenance, license compatibility and the publisher trust chain. Commercial entitlement and installation authorization remain distinct.
3. Implement against a reviewed Core contract with current actor, tenant, ownership and capability checks. Use secret references; reject arbitrary commands, host paths, browser-selected egress destinations and container sockets.
4. Complete the package-specific acceptance gates with synthetic fixtures and an isolated qualified target. Record failures, unavailable prerequisites and unexecuted checks separately.
5. Qualify install-disabled, activation, update, disable/drain, uninstall and recovery. Retain owned data by default; purge requires a separate authorized decision.
6. Prove that provider outage, connector removal and commercial entitlement loss preserve essential Core access, existing hosted workloads, local audit and recovery.

External product names identify proposed integration targets only. No vendor affiliation, API compatibility, broad operating-system support or production availability is asserted.
