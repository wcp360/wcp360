# systemd supervision resources

**Status: three disposable-VM candidate units exist; production units and general native service integration remain unimplemented.**

| Responsibility | Location or rule |
| :--- | :--- |
| Owning domain | [Platform](../../../src/Module/Platform/README.md) |
| Versioned contents | First-party unit/drop-in templates, service identity, restart policy and resource confinement. |
| Typical Debian destination | Packaged units under /usr/lib/systemd/system/; local overrides under /etc/systemd/system/. |
| Adapter code | The owning module's Infrastructure layer; invoke only accepted typed agent operations. |
| Verification | Renderer/escaping tests in tests/Unit; synthetic fixtures in tests/Fixtures; service syntax, failed-apply and observed-health checks in tests/Integration on a disposable target. |

Keep ADMIN, USER, workers, non-root wcpd and minimal-root wcp-privd as separate identities. Names and privileges require the accepted runtime contract.

When implemented, keep package/version-specific templates and profiles here. Add templates/ or profiles/ only with actual reviewed source. Test fixtures have a single home under tests/Fixtures; application validation stays in the owning domain. A candidate settings renderer is not the production validator for the Linux service.

Paths above are conventional examples, not a deployment allowlist. Resolve the installed package, version, instance, ownership and approved WCP-managed include before applying. Follow the [shared configuration lifecycle](../README.md#configuration-change-lifecycle) and [source/runtime boundary](../../README.md).

Production units must bind accepted identities, socket ownership, readiness, restart/drain budgets and effective CPU/memory/PID confinement. Coordinate with the [host register](../../host/README.md), [confinement](../confinement/README.md) and [Core structure](../../../STRUCTURE.md). These references create no service identity or generic systemctl operation.

## Executable disposable-VM candidate

The [candidate units](candidate/) supervise the isolated Go broker/agent and explicitly opted-in VM boot recovery. See the [lifecycle and qualification guide](../../../../documentation/development/SYSTEMD-RECOVERY.md). They use fixed fixture identities and are not production units.
