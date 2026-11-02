# Docker provider resources

**Status: documentation reservation. No production configuration or new runtime capability is implemented here.**

Owner: [Platform](../../../src/Module/Platform/README.md). Hosting owns application/ingress policy; Operations owns durable lifecycle.

The development Docker environment is not a customer workload manager. Future provider profiles describe admitted engine/image identity, private networks/endpoints, confined volumes, UID mapping, secret references, quotas and recovery. Build/rebuild also need approved recipe/context/dependency/plugin revisions, bounded builders and resulting-image provenance. Follow the [application register](../../applications/README.md) and [hosting strategy](../../../src/Module/Hosting/WEB-SERVER-STRATEGY.md).

Never grant PHP, tenants or plugins the Docker socket, privileged group or a generic Docker/Compose executor. Arbitrary mounts, hooks, arguments and privileged mode are not self-service capabilities. Root inside a container, a rootful daemon and privileged mode are separate decisions; no blanket user flag should break qualified initialization.

Rootless/user namespaces need separate evidence. Observe actual CPU/memory/PID and storage enforcement; unsupported flags are not quotas. Keep daemon credentials and machine state outside Git. See [Docker security](https://docs.docker.com/engine/security/) and [rootless limits](https://docs.docker.com/engine/security/rootless/tips/#limiting-resources).

See the [resource index](../../README.md), [Core structure](../../../STRUCTURE.md) and existing owning use cases before adding real source. This README does not select a package, create a machine-path allowlist or authorize privileged operations.
