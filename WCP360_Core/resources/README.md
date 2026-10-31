# Core versioned resources

**Status: documented source layout. This directory supplies no live server configuration.**

[services](services/README.md) is the source location for reviewed Linux-service templates and profiles. Symfony dependency injection, routes and bundle configuration remain in [config](../config/README.md). Browser-facing Twig views remain in [templates](../templates/README.md).

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
- [Service template directory](services/README.md)
- [Installation/build qualification](../../documentation/development/DELIVERY-AND-RELEASES.md)
