# Redis resources

**Status: source reservation. No deployable configuration or service integration is implemented here.**

| Responsibility | Location or rule |
| :--- | :--- |
| Owning domain | [Operations](../../../src/Module/Operations/README.md) |
| Versioned contents | Optional async-transport/cache service profiles, connection isolation and persistence policy. |
| Typical Debian destination | /etc/redis/ |
| Adapter code | The owning module's Infrastructure layer; invoke only accepted typed agent operations. |
| Verification | Renderer/escaping tests in tests/Unit; synthetic fixtures in tests/Fixtures; service syntax, failed-apply and observed-health checks in tests/Integration on a disposable target. |

Redis is used only where selected. Keep queues, sessions and caches separated by approved credentials/instances; do not expose the service publicly.

When implemented, keep package/version-specific templates and profiles here. Add templates/ or profiles/ only with actual reviewed source. Test fixtures have a single home under tests/Fixtures; application validation stays in the owning domain. A candidate settings renderer is not the production validator for the Linux service.

Paths above are conventional examples, not a deployment allowlist. Resolve the installed package, version, instance, ownership and approved WCP-managed include before applying. Follow the [shared configuration lifecycle](../README.md#configuration-change-lifecycle) and [source/runtime boundary](../../README.md).
