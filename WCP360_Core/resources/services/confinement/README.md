# Host confinement resources

**Status: documentation reservation. No production configuration or new runtime capability is implemented here.**

Owner: [Platform](../../../src/Module/Platform/README.md), with IdentityAccess and affected resource owners.

Future profiles bind admitted executable/service identities to permitted filesystem, IPC, process, device and network behavior. AppArmor or another mechanism must be selected and qualified on the actual target; a profile filename or installed package does not prove enforcing mode.

Core targets remain Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS. This does not establish SELinux/AlmaLinux support. Compose confinement with [systemd](../systemd/README.md), Unix identity, private sockets and bounded agent operations. None replaces tenant authorization or makes shared storage safe automatically.

Policy installation/reload/recovery needs typed accepted operations and disposable-host evidence. Observe enforcement after restart/reboot and verify intended operations and negative paths/peers. Do not weaken a complete profile merely to pass a test.

Real policies belong here when implemented; runtime audit logs and customer paths do not. No arbitrary user-supplied profile is admitted. See the [host register](../../host/README.md).

See the [resource index](../../README.md), [Core structure](../../../STRUCTURE.md) and existing owning use cases before adding real source. This README does not select a package, create a machine-path allowlist or authorize privileged operations.
