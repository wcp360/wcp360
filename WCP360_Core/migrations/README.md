# Control-plane migrations

Reviewed migrations matching the exact accepted database owner model. Separate USER/ADMIN runtime grants, Core schemas and hosted customer databases. Plugin migrations own isolated data and cannot mutate Core tables.

This is an implementation reservation. Follow the [Core status](../README.md) and [development/acceptance guide](../../documentation/development/CORE-ENGINE.md) before adding runtime code.
