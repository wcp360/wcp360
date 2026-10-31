# Specialized diagnostics

Non-root Python diagnostics and automation behind reviewed typed contracts, bounded resources and redacted evidence. These tools do not own Core business rules, bypass tenant authorization or provide an alternate privileged executor.

This is an implementation reservation. Follow the [Core status](../README.md) and [development/acceptance guide](../../documentation/development/CORE-ENGINE.md) before adding runtime code.

Add a specifically named tool directory only with real reviewed source; a catch-all collectors or scripts folder is not required in advance. Keep dependency ownership tool-local and use the [Core test map](../tests/README.md) and shared [synthetic fixtures](../tests/Fixtures/README.md) instead of copying real support bundles into source.
