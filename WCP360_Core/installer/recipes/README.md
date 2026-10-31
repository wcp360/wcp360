# Installation recipes

**Status: implementation location documented; no installation script or host mutation is implemented here.**

Reviewed orchestration for an approved installation plan and selected compatible components.

Select pinned qualified artifacts and the canonical resources/services templates. Preserve explicit identities and package/file ownership, durable progress and operation boundaries. Do not duplicate service defaults, invent privileged primitives or resolve arbitrary dependencies on a production host.

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
