# Container-runtime provider preparation

**Status: design reservation. No Docker daemon, socket, image or workload is installed or exposed.**

Platform owns the admitted container provider and node-level resources; Hosting owns the application instance, ingress and dependencies. Operations owns durable lifecycle mechanics, Tenancy limits, IdentityAccess authority, Data/Files persistence and BackupRestore recovery. Start with the [Core structure](../../../../../STRUCTURE.md), [web-server strategy](../../../Hosting/WEB-SERVER-STRATEGY.md) and [Docker resource reservation](../../../../../resources/services/docker/README.md).

## Source boundaries

| Concern | Implementation location when reviewed |
| :--- | :--- |
| Provider capability and admission policies | Platform/Domain and Application typed ports. |
| Docker/OCI adapter | Platform/Infrastructure, behind admitted operations; never a generic proxy to the daemon API. |
| Application desired/observed state and ingress | Hosting; one resource identity and application inventory. |
| Build/run/rebuild jobs, fencing and reconciliation | Operations with provider-specific outcome inspection. |
| Engine service configuration | resources/services/docker; one canonical template source. |
| Versioned application recipes | resources/applications and approved detached contributions. |

A Docker API credential or socket can confer host authority. PHP, tenants and plugins receive no raw engine access or Docker group membership. The broker/provider admits complete typed specifications rather than tenant-selected shell commands, hooks, host paths or unbounded Compose input.

## Provider contract to implement

- Engine/version/target/architecture identity, trust/provenance and observed capabilities; rootless and rootful are separate qualified profiles.
- Owned workload/network/volume IDs, approved image digest and pinned recipe/build inputs, platform and plugin revisions.
- Private ingress references, port reservation and explicit egress; deny control-plane, metadata and other-tenant reachability.
- Volume/mount confinement, UID/GID mapping, read/write policy, secrets by reference and deletion/retention behavior.
- Build, run and rebuild budgets: CPU/RAM/PIDs, disk/inodes, logs, volumes and concurrency; verify actual enforcement.
- Readiness/health, start/stop/drain/restart/update/remove, timeouts, idempotency/fencing and an observable uncertain outcome.
- Compatible data migrations, consistent app/database/upload backups, clean-target restoration and recovery objectives.

Do not require every upstream image to start as a non-root container user if its qualified initialization needs container root. That does not admit privileged containers or extra host authority. Workloads that cannot satisfy the selected isolation profile need a separate node/VM or refusal.

[Docker's rootless guidance](https://docs.docker.com/engine/security/rootless/tips/#limiting-resources) explains the prerequisites for effective resource limits. A requested flag is not enforcement evidence.

## Delivery and acceptance

Start with registered private HTTP backends, then one fixed admitted container recipe on a disposable target. Integrate Discourse through the upstream application-specific recipe; do not replace its Ruby runtime with PHP or reuse the panel database. See [Hosting's application design](../../../Hosting/design/applications/README.md).

Test foreign resources/volumes/networks, untrusted images/build inputs, privileged options, escaped mounts, exposed ports, missing controller limits, disk exhaustion, crashes, reboot, failed upgrade and verified restoration. Keep daemon secrets and output out of the browser/audit. Native provider support is qualified independently on Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS.
