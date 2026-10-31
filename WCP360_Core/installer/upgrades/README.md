# Host and product upgrades

**Status: implementation location documented; no installation script or host mutation is implemented here.**

Version-specific compatibility checks and orchestration for upgrades from explicitly supported installed versions.

Bind the source version, target artifacts, dependency/service compatibility, required downtime and accepted schema migration sequence. Resume safely after interruption. Separate reversible changes from irreversible transitions and reject unsupported version jumps.

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
