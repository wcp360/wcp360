# Premium connector proposals

This folder contains **76 design proposals in 15 categories** for optional WCP360 connectors. Every proposal is `design`, `installable: false` and `source_model: undecided`. These documents provide an authoring and review inventory; they do not provide implemented connectors, admitted manifests, signed releases, working credentials or an installer.

The Core licensing choice is **AGPL-3.0-only**. Read [Core licensing](../../WCP360_Core/LICENSING.md) and [editions and premium boundaries](../../documentation/development/EDITIONS-AND-PREMIUM.md). No premium package license, source distribution model, price, entitlement key or vendor endpoint is selected here. A separate process or API does not by itself establish legal independence or permission to distribute a proprietary derivative; the actual design and distribution need licensing review.

## Catalogue authority and files

- [CATALOG.json](CATALOG.json) is the authoritative catalogue: `wcp360.premium-package-catalogue/1`, `count: 76`, and complete proposal objects in `packages`.
- [CATALOG.md](CATALOG.md) is its readable view, with one link per proposal.
- Each `<slug>/package.proposal.json` is an exact data mirror of its catalogue entry. Edit the authority and synchronize the mirrors and Markdown view together.
- The shared [proposal schema](../../WCP360_Modules/schemas/package-proposal.schema.json) defines the 15 closed fields. Valid JSON or schema validation does not establish publisher trust, package admission, permissions, license compatibility or installability.
- [_template](_template/README.md) is a separate documentation example, excluded from the 76-entry count. It does not replace the existing [plugin authoring template](../_template/README.md).

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
| [Collaboration and support](CATALOG.md#collaboration-support) | 6 | Redacted notifications, support records and reviewed ChatOps. |
| [Git, CI and artifact registries](CATALOG.md#git-ci-registries) | 7 | Source-provider events and immutable artifact references. |
| [AI and MCP](CATALOG.md#ai-mcp) | 4 | Optional redacted assistance, selected models and admitted MCP HTTP. |
| [Migration and data transfer](CATALOG.md#migration-data) | 4 | Source-specific portfolio, mailbox and PostgreSQL migration profiles. |
| [Docker, Kubernetes and virtualization](CATALOG.md#compute-platforms) | 6 | Core-mediated Docker, namespace-scoped Kubernetes and distinct hypervisor/guest profiles. |
| [Storage platforms](CATALOG.md#storage-platforms) | 3 | Object gateways, admitted dataset/share/snapshot APIs and retained-data decisions. |
| [Cybersecurity and DDoS defense](CATALOG.md#security-defense) | 4 | Reviewed defensive decisions, WAF policy integration and distinct upstream L3/L4 mitigation. |
| [Multi-server automation](CATALOG.md#multi-server-automation) | 1 | Admitted controller inventories and bounded reviewed multi-server job templates. |
| [Performance and HTTP caching](CATALOG.md#performance-cache) | 1 | Qualified external-cache profiles; native compression and target Apache routing stay Core. |

## Relationship to Core and existing catalogues

The [149-entry integration catalogue](../../PLUGIN_CATALOG.md) remains the source of design keys. A proposal can reference several existing keys; 76 connector proposals do not mean 76 additional essential Core capabilities or 76 implemented integrations. The seven `engine.*` adapters and the **233 essential hosting capabilities** remain in free Core scope. Native web/DNS/mail/firewall/isolation control, local authentication, tenant permissions, basic transfers and recovery are not moved behind a premium connector.

The 43 optional [business module proposals](../../WCP360_Modules/CATALOG.md) describe operator workflows; these 76 plugin proposals describe optional provider/protocol integrations. Links between compatible ideas do not establish runtime dependencies. All package `dependencies` arrays are currently empty. Core owners express required admission boundaries, not installable package IDs.

The actual Core APIs, SDK, detached process contracts and provider adapters still require implementation and qualification. Check [Core status](../../WCP360_Core/STATUS.md), [the public SDK](../../WCP360_SDK/README.md) and [plugin lifecycle requirements](../../documentation/development/PLUGIN-LIFECYCLE.md).

## Explicit infrastructure, performance and security boundaries

The inventory includes optional Docker fleet, Kubernetes, Proxmox VE, libvirt/KVM, Incus, OpenStack and AWX connectors. Native container/VM control, node ownership, quotas, concurrency and host recovery remain in Core. Tenant workloads and guests never inherit hypervisor, cluster-admin, daemon-socket or privileged-broker authority.

Storage proposals distinguish backup destinations from Ceph RGW/MinIO object gateways and TrueNAS/ZFS dataset/share integrations. Disconnecting a service or removing a connector is distinct from deleting objects, volumes, datasets or retained backups.

Cloudflare and Fastly are enriched in place, with Bunny and a Varnish-compatible cache profile added. **Compression is distinct from public caching.** The target Nginx-to-Apache all-traffic profile keeps direct-file and response caching off so that Apache and nested `.htaccess` rules remain authoritative. Cache/CDN handoff needs a separately admitted profile and protected-file, authorization-revocation, private-response, cookie, purge, origin/TLS and client-IP evidence. No blanket speedup is promised.

CrowdSec, ModSecurity CRS and Coraza proposals cover defensive observations and reviewed policy workflows. Upstream scrubbing concerns **L3/L4 mitigation** and is separate from local **L7 WAF/rate-limit/firewall** controls. Local filtering cannot guarantee protection from an already saturated uplink. Provider coverage, routing rollback, origin bypass and IPv4/IPv6/UDP/HTTP3 behavior remain qualification work; no live customer attack or guaranteed attack-absorption result is authorized.

## Qualification before any release

1. Select a concrete provider/interface profile, compatibility matrix and immutable package artifact. Record supported revisions and platform/architecture requirements only after evidence exists.
2. Review source distribution, dependency provenance, license compatibility and the publisher trust chain. Commercial entitlement and installation authorization remain distinct.
3. Implement against a reviewed Core contract with current actor, tenant, ownership and capability checks. Use secret references; reject arbitrary commands, host paths, browser-selected egress destinations and container sockets.
4. Complete the package-specific acceptance gates with synthetic fixtures and an isolated qualified target. Record failures, unavailable prerequisites and unexecuted checks separately.
5. Qualify install-disabled, activation, update, disable/drain, uninstall and recovery. Retain owned data by default; purge requires a separate authorized decision.
6. Prove that provider outage, connector removal and commercial entitlement loss preserve essential Core access, existing hosted workloads, local audit and recovery.

External product names identify proposed integration targets only. No vendor affiliation, API compatibility, broad operating-system support or production availability is asserted.
