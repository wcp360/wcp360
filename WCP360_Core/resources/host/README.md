# Host resource register

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owner: [Platform](../../src/Module/Platform/README.md), coordinating Setup, Tenancy, IdentityAccess and existing resource owners.

| Concern | Resource boundary |
| :--- | :--- |
| Identities, sockets and supervision | [systemd](../services/systemd/README.md); production identities remain contract-defined. |
| Filesystems, mounts, capacity and limits | [Quotas](../services/quotas/README.md); registered devices/resources, not arbitrary host paths. |
| Interfaces, routes and management access | [Firewall](../services/firewall/README.md) and [SSH](../services/ssh/README.md); verified recovery access. |
| Time | [time-sync](../services/time-sync/README.md); observation is not clock-changing authority. |
| Mandatory access controls | [Confinement](../services/confinement/README.md), composed with Unix/systemd/provider restrictions. |
| Containers | [Docker](../services/docker/README.md); filesystem quotas and effective cgroups remain distinct. |

Current read-only candidate entry points are the [ROOT capacity overview](../../src/Module/Platform/README.md#root-server-overview) and [ROOT service observations](../../src/Module/Platform/SERVICES.md). The latter observes nine families through fourteen fixed unit names; it does not cover all 24 service resource families or verify provider installation, health or control. Both views observe the current process namespace and do not establish whole-host admission.

This index prevents host policy being copied into every service. It creates no second adapter layer or live /etc, /run, data, logs or package files in Git.

Core targets are Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS. Recognition/userspace CI do not establish kernel, native service or privileged-host qualification. Changes retain ownership, observed revisions, bounded effects and recovery. No raw device/mount/network/command input is introduced.

See the [resource index](../README.md) and [Core structure](../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.

Use the [working guide](../../WORKING-GUIDE.md) to choose the owning source location and [current status](../../STATUS.md) to distinguish implemented candidates from planned or unqualified behavior.
