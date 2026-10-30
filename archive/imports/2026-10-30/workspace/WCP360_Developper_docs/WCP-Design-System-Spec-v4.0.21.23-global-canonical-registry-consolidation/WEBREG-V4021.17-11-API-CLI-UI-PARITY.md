# V4.0.21.17 --- Web/PHP/TLS/DNS API/CLI/UI Parity

Domain, site, PHP, certificate and DNS operations use the same canonical
Operation IDs on REST, CLI and UI. UI wizards may compose multiple
operations but cannot bypass policy or privileged-action boundaries.

Dry-run/impact preview is required for destructive domain/site/DNS
operations and backend migration where dependencies exist. Job progress
exposes stages without leaking secrets or raw privileged configuration.
