# Interrupted installation and repair

**Status: durable recovery rules are implemented for the non-root candidate checkout installer. Production host repair and upgrade recovery remain unimplemented.**

The [candidate installer](../recipes/candidate-install.sh) records checkpoints in private `var/installer/candidate-install.json`. Its lock spans the mutating run; concurrent installation is refused. The journal is bound to the selected checkout sources, configuration and environment so that an old checkpoint cannot silently authorize a changed installation.

1. Stop the failed installer and inspect `installer/recipes/candidate-install.sh --status` from the Core directory. This command does not create state, acquire a mutation lock or require installed Composer dependencies.
2. Retain the journal and determine the failing or interrupted step. Restore the recorded configuration if it changed; do not edit checkpoints to make a different checkout appear compatible.
3. For preparation steps, correct the reported prerequisite and explicitly run `installer/recipes/candidate-install.sh --resume`. Admission and binding checks run again before replay.
4. If `database_migrations` failed or was interrupted, stop automatic recovery. Inspect the configured database and Doctrine migration records, then reconcile them manually or restore a verified backup. Resume does not retry an uncertain migration or infer that a rollback succeeded.
5. After a successful migration checkpoint, resume verifies migration state read-only. It does not execute migrations again merely because the installer was restarted.

There is no reset flag. Deleting or rewriting the journal discards recovery evidence and is not a supported repair procedure. A database error can leave an uncertain outcome even when the process reports failure; the operator must establish the actual state before preparing a clean, verified checkout.

These rules cover candidate dependencies, generated planner/assets and database preparation. They do not qualify operating-system packages, system services, a production rollback or an upgrade between released versions. Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS share checkout admission; privileged disposable provisioning remains subject to its separate Debian 13-specific guards.

Setup business state and the restricted wizard remain in the [Setup module](../../src/Module/Setup/README.md). Service configuration text has one source in [resources/services](../../resources/services/README.md); installer code consumes the accepted boundaries rather than creating a second generic system executor.

- [Installer source map](../README.md)
- [Installation and upgrade verification](../../tests/Installer/README.md)
- [Release and recovery acceptance](../../../documentation/development/DELIVERY-AND-RELEASES.md)
