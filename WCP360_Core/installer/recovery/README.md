# Interrupted installation and repair

**Status: implementation location documented; no installation script or host mutation is implemented here.**

State inspection, bounded resume/repair and recovery procedures for incomplete or failed installation/upgrade steps.

Use durable checkpoints and observed host/package state; do not restart the entire install blindly. Define which identity can recover, what data is retained and when manual repair is required. Never claim rollback succeeds for an irreversible migration or unverified backup.

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
