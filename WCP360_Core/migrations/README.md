# Control-plane migrations

Reviewed migrations matching the exact accepted database owner model. Separate USER/ADMIN runtime grants, Core schemas and hosted customer databases. Plugin migrations own isolated data and cannot mutate Core tables.

This is an implementation reservation. Follow the [Core status](../README.md) and [development/acceptance guide](../../documentation/development/CORE-ENGINE.md) before adding runtime code.

The executable candidate additionally requires [Version20261102000100](Version20261102000100.php) for nullable hosting-plan/audit request references. It preserves existing rows and append-only audit, constrains newly supplied metadata and does not widen runtime grants. As with other candidate migrations, run it through the existing setup maintenance procedure before using the matching source; local execution remains stopped by user instruction.

## Candidate site runtime profiles

[Version20261102000200](Version20261102000200.php) is the next additive candidate migration. It adds `web_stack` and `stack_revision` to `wcp_candidate_sites`, backfilling `php_fpm` as `classic` and `frankenphp_worker` as `advanced` at profile revision 1. Existing site IDs/lifecycle revisions, jobs, result bytes, fingerprints and audit history are preserved. Engine enum/global settings and public projections retain their contracts; no additional table, dependency or grant is introduced.

The [schema owner](../src/Module/Hosting/Infrastructure/SiteRuntimeProfileSchema.php) requires non-null, revision-1 metadata and matching engine/topology pairs. PostgreSQL uses constraints and SQLite uses insert/update guards without disabling foreign keys or rebuilding referenced tables. A final SQLite update validates backfilled rows against those guards; PostgreSQL validates them while applying constraints. New writes require an explicit profile, without a Classic fallback. Modern/Legacy durable selection, stack switching and profile-revision increments remain unimplemented.

Use the existing setup maintenance process: stop writers and rendering workers, retain a verified candidate backup with a reviewed recovery path, apply tracked Doctrine migrations, and only then run the matching application/worker source. This document does not run or authorize a local migration. A direct repeat of the schema upgrade is refused; normal migration tracking owns whether it is pending. The down migration refuses lossy removal of profile metadata. Recover through a reviewed backup/recovery procedure rather than dropping columns or disabling guards.

[Typed profiles](../src/Module/Hosting/Domain/SiteRuntimeProfile.php) validate stored metadata before rendering, export decoding and PHP-FPM activation. The worker supplies its captured profile to the optional queue publication guard; this preserves current authority and refuses changed-profile output through the existing failed transition and correlated audit. It does not create immutable per-job topology history or a site migration workflow.

[Profile unit definitions](../tests/Unit/SiteRuntimeProfileTest.php), [schema regressions](../tests/Integration/SiteRuntimeProfileSchemaTest.php), [consumer regressions](../tests/Integration/SiteRuntimeProfileWorkflowTest.php) and [restricted-role checks](../tests/Integration/CandidateDatabaseRolesTest.php) define preservation, refusal, repeated-upgrade and retained-permission behavior. Test definitions are not execution evidence. Exact-source CI outcomes belong to [draft PR #116](https://github.com/yyogas/wcp360/pull/116); local executable tests and migration application remain stopped.

Hosted-resource movement between instances is coordinated by [Transfers](../src/Module/Transfers/README.md). This directory remains the source location for accepted Core database-schema migrations; it does not hold customer migration archives, arbitrary import scripts or external-panel transfer engines.
