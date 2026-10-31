# Installation preflight

**Status: implementation location documented; no installation script or host mutation is implemented here.**

Read-only discovery and validation before selecting or applying an installation plan.

Supported OS/architecture, installed package/engine versions, capacity, ownership, existing listeners, hostname/DNS/time and incompatible service topology. Report failed or unknown checks explicitly; do not silently repair the host or treat UI fixtures as real observations.

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
