# Core versioned resources

**Status: documented source layout. This directory supplies no live server configuration.**

[services](services/README.md) is the source location for reviewed Linux-service templates and profiles. Delivery order, missing families and acceptance for this layer are in the [resources roadmap](ROADMAP.md) and the [per-service register](services/ROADMAP.md). Symfony dependency injection, routes and bundle configuration remain in [config](../config/README.md). Browser-facing Twig views remain in [templates](../templates/README.md).

## Resource families

These are reviewed documentation reservations for the requested preparation. A README records scope and ownership; it supplies no implementation, selected provider or permission. Add actual templates/profiles only with the consuming reviewed implementation.

| Family | Responsibility and owner |
| :--- | :--- |
| [24 Linux services](services/README.md) | Existing service owners; selected service configuration sources and external candidate pointers. |
| [Applications](applications/README.md) | Hosting: PHP/native/container recipes and their provider/data/ingress qualification. |
| [Host resources](host/README.md) | Platform with Setup/Tenancy: identities, storage, network, quotas, time and confinement. |
| [Observability](observability/README.md) | Observability: signal resources, privacy, retention, budgets and local health/alerts. |
| [Toolbox](toolbox/README.md) | Observability: bounded diagnostic descriptions; code remains in modules/diagnostics. |
| [Extension contributions](extensions/README.md) | ExtensionManagement: approved resource contributions, compatibility and owned lifecycle. |
| [Optional AI integrations](ai/README.md) | Platform/ExtensionManagement/Operations: [providers](ai/providers/README.md), [models](ai/models/README.md), [assistant missions](ai/agents/README.md) and [MCP](ai/mcp/README.md), without privileged shortcuts. |

Caddy and Docker have explicit service reservations; neither is installed by this index. Caddy embedded in FrankenPHP does not imply another daemon. Application/container state, extension packages, AI weights/caches and toolbox executables do not belong in these documentation indexes. See the [Core structure](../STRUCTURE.md).

## Source and runtime boundaries

| Kind of file | Source or generated location |
| :--- | :--- |
| Service template/profile | resources/services/<service>/ |
| PHP policy, validation and adapter | src/Module/<Owner>/{Domain,Application,Infrastructure}/ |
| Rendering and configuration fixtures | tests/Fixtures/; create with actual synthetic test inputs |
| Applied system configuration | Installed package's approved /etc location, outside the repository |
| Machine-specific WCP settings and secret references | Intended /etc/wcp360 boundary, with per-identity permissions determined by the accepted package contract |
| Durable panel state | Intended /var/lib/wcp360 boundary, outside source; customer/service data keeps its separately approved locations |
| Service sockets and process state | Intended /run/wcp360 boundary; ephemeral and recreated through the approved supervisor |
| Operational logs | Intended /var/log/wcp360 boundary; retention/redaction is owned by Observability |
| Disposable build/staging output | WCP360_Build/build/; not versioned source or the authoritative production configuration |

The WCP paths are target conventions subject to accepted packaging/security contracts. They do not relocate PostgreSQL data, customer website roots, mail stores, operator-managed configuration or Debian package defaults. Keep configuration, durable state and ephemeral runtime separate.

Never copy the host's /etc tree into Git or store production credentials in sample resources. A configuration change uses validated typed settings and a bounded apply operation; PHP, a template and a plugin cannot independently write arbitrary system paths.

- [Core navigation](../NAVIGATION.md)
- [Resources roadmap](ROADMAP.md)
- [Service template directory](services/README.md)
- [Per-service roadmap](services/ROADMAP.md)
- [Installation/build qualification](../../documentation/development/DELIVERY-AND-RELEASES.md)
