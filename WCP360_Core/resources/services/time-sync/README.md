# Time-synchronization resources

**Status: documentation reservation. No production configuration or new runtime capability is implemented here.**

Owner: [Platform](../../../src/Module/Platform/README.md), with Setup for preflight and Observability for health.

Future profiles describe the admitted time service, approved sources, network policy, synchronization health, drift thresholds and coexistence with operator-managed services. chrony, systemd-timesyncd or another backend requires a target/provider decision; do not enable competing services.

Time affects certificate checks, authentication factors, artifact validity and audit interpretation. Observing a timestamp does not prove synchronization or authorize changing the host clock.

Resolve actual installed providers on Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS before defining templates or unit names. Container time observations are not native host admission.

No arbitrary time-source URL, shell command, kernel setting or privileged clock operation is introduced. Keep inspection separate from authorized changes, preserve recovery access and retain synchronization/failure evidence on disposable native targets. See the [host register](../../host/README.md).

See the [resource index](../../README.md), [Core structure](../../../STRUCTURE.md) and existing owning use cases before adding real source. This README does not select a package, create a machine-path allowlist or authorize privileged operations.
