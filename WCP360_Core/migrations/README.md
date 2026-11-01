# Control-plane migrations

Reviewed migrations matching the exact accepted database owner model. Separate USER/ADMIN runtime grants, Core schemas and hosted customer databases. Plugin migrations own isolated data and cannot mutate Core tables.

This is an implementation reservation. Follow the [Core status](../README.md) and [development/acceptance guide](../../documentation/development/CORE-ENGINE.md) before adding runtime code.

The executable candidate additionally requires [Version20261102000100](Version20261102000100.php) for nullable hosting-plan/audit request references. It preserves existing rows and append-only audit, constrains newly supplied metadata and does not widen runtime grants. As with other candidate migrations, run it through the existing setup maintenance procedure before using the matching source; local execution remains stopped by user instruction.

Hosted-resource movement between instances is coordinated by [Transfers](../src/Module/Transfers/README.md). This directory remains the source location for accepted Core database-schema migrations; it does not hold customer migration archives, arbitrary import scripts or external-panel transfer engines.
