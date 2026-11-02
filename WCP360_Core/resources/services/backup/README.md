# Backup tooling resources

**Status: source reservation. No deployable configuration or service integration is implemented here.**

| Responsibility | Location or rule |
| :--- | :--- |
| Owning domain | [BackupRestore](../../../src/Module/BackupRestore/README.md) |
| Versioned contents | Local backup/restore tool profiles, scheduling inputs, retention and restore-verification resources. |
| Typical Debian destination | Approved tool-specific configuration; panel references under /etc/wcp360/. |
| Adapter code | The owning module's Infrastructure layer; invoke only accepted typed agent operations. |
| Verification | Renderer/escaping tests in tests/Unit; synthetic fixtures in tests/Fixtures; service syntax, failed-apply and observed-health checks in tests/Integration on a disposable target. |

Baseline local recovery must not depend on an optional vendor service. Keep archives, encryption keys and remote credentials out of Git.

## Existing candidate recovery tools

The checkout already has a [candidate PostgreSQL backup helper](../../../installer/recovery/candidate-backup.sh) and an [isolated restore rehearsal](../../../installer/recovery/candidate-restore-test.sh) under installer/recovery. The backup accepts only `wcp360_workbench` or `wcp360_test`; restore accepts only the empty `wcp360_restore_test` target and a checked candidate dump. See the [candidate recovery guide](../../../installer/recovery/README.md).

These helpers are not a production backup provider, scheduler, encrypted-retention service or complete tenant filesystem/database/mail/container recovery path. Keep their existing implementation in installer/recovery rather than creating another installer/backup tree or copying the scripts here. Future BackupRestore use cases must define their own accepted lifecycle and recovery evidence.

## Application and provider recovery

Coordinate consistent filesystem/database/mail/application snapshots with their owners, including [Docker/OCI workloads](../docker/README.md) and their persistent volumes. A file copy is not automatically an application-consistent backup. Record source versions, required configuration/plugin references, encryption-key references and the recovery point.

Define local/remote destinations, authorization, retention, credential rotation, integrity checks and bounded resource use. Test restoration on a fresh disposable target and compare application state, uploads and ownership. Schema/data compatibility governs recovery; reverting a binary/image alone cannot undo an irreversible migration. Preserve uncertain outcomes explicitly.

Neither archives, real encryption keys nor remote credentials belong here. Tool choice remains open; this reservation selects no backup vendor. See the [observability register](../../observability/README.md), [applications](../../applications/README.md) and [Core structure](../../../STRUCTURE.md).

When implemented, keep package/version-specific templates and profiles here. Add templates/ or profiles/ only with actual reviewed source. Test fixtures have a single home under tests/Fixtures; application validation stays in the owning domain. A candidate settings renderer is not the production validator for the Linux service.

Paths above are conventional examples, not a deployment allowlist. Resolve the installed package, version, instance, ownership and approved WCP-managed include before applying. Follow the [shared configuration lifecycle](../README.md#configuration-change-lifecycle) and [source/runtime boundary](../../README.md).

Use the [working guide](../../../WORKING-GUIDE.md) to choose the owning source location and [current status](../../../STATUS.md) to distinguish implemented candidates from planned or unqualified behavior.
