# MariaDB resources

**Status: source reservation. No deployable configuration or service integration is implemented here.**

| Responsibility | Location or rule |
| :--- | :--- |
| Owning domain | [Data](../../../src/Module/Data/README.md) |
| Versioned contents | Hosted-engine profiles, service configuration and engine-specific settings. |
| Typical Debian destination | /etc/mysql/ |
| Adapter code | The owning module's Infrastructure layer; invoke only accepted typed agent operations. |
| Verification | Renderer/escaping tests in tests/Unit; synthetic fixtures in tests/Fixtures; service syntax, failed-apply and observed-health checks in tests/Integration on a disposable target. |

Do not assume MySQL and MariaDB share compatible directives or can occupy the same instance paths and port. Qualify the selected engine/version separately.

When implemented, keep package/version-specific templates and profiles here. Add templates/ or profiles/ only with actual reviewed source. Test fixtures have a single home under tests/Fixtures; application validation stays in the owning domain. A candidate settings renderer is not the production validator for the Linux service.

Paths above are conventional examples, not a deployment allowlist. Resolve the installed package, version, instance, ownership and approved WCP-managed include before applying. Follow the [shared configuration lifecycle](../README.md#configuration-change-lifecycle) and [source/runtime boundary](../../README.md).
