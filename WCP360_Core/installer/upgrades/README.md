# Host and product upgrades

**Status: a candidate database-migration helper is present. Supported product-release upgrades, native host orchestration and general rollback remain unimplemented and unqualified.**

[candidate-upgrade.sh](candidate-upgrade.sh) accepts a candidate dump under the checkout's `var/backups`, checks it against its adjacent SHA-256 sidecar, then invokes Doctrine migrations in that checkout. A matching digest establishes agreement with that sidecar, not backup authenticity, application-consistent completeness or a successful restore. The helper neither authenticates/switches product releases nor rolls back schema, operating-system packages or external effects; it is not a production update procedure. Failed or interrupted migrations require the existing [recovery inspection](../recovery/README.md), without assuming failure means no effect or replay is safe. This documentation correction does not authorize running the helper.

Version-specific compatibility checks and orchestration for upgrades from explicitly supported installed versions.

Bind the source version, target artifacts, dependency/service compatibility, required downtime and accepted schema migration sequence. Resume safely after interruption. Separate reversible changes from irreversible transitions and reject unsupported version jumps.

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
