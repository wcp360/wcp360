# Filesystem quota resources

**Status: documentation reservation. No production configuration or new runtime capability is implemented here.**

Owner: [Tenancy](../../../src/Module/Tenancy/README.md), with [Platform](../../../src/Module/Platform/README.md) for registered filesystems and host operations.

Future profiles map an authorized account/resource to an admitted quota identity and space/inode limits. Specify filesystem/backend support, soft/hard limits and grace where available, mount prerequisites, ownership mapping, usage observation and restore behavior. Project quotas, XFS or another backend require an explicit decision.

Filesystem quotas, container writable layers/volumes, database/mail limits and CPU/memory/PID cgroups are separate mechanisms. A displayed hosting-plan limit is not enforcement. Admission needs observed effective limits and exhaustion tests on the selected provider.

Use registered resources and reviewed operations, never browser-selected devices, mount options, paths or quota-command arguments. Account for concurrent writes and current usage; lowering a limit must not silently delete data.

Keep customer data, mounts and usage state outside source. See the [host register](../../host/README.md) and [service lifecycle](../README.md#configuration-change-lifecycle).

See the [resource index](../../README.md), [Core structure](../../../STRUCTURE.md) and existing owning use cases before adding real source. This README does not select a package, create a machine-path allowlist or authorize privileged operations.
