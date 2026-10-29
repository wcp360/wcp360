# WCP360 platform kernel candidate

This directory is a **framework-independent implementation candidate**, not a production baseline.

It is intentionally limited to contract-safe application concerns that can be implemented without choosing or activating an HTTP framework, database driver, queue worker, privileged daemon path or Linux mutation layer.

Implemented now:

- canonical API error-code enum (23 codes);
- canonical permission registry loader;
- canonical API-contract registry loader;
- request field-name validation derived from canonical contract schemas;
- WCP-USER / WCP-ADMIN / POLICY surface separation;
- fail-closed authorization composition through mandatory scope and policy guards;
- explicit action-handler registry: unknown actions never dynamically dispatch;
- job/audit value objects that preserve canonical outcome/error semantics;
- no shell, command, sudo, systemctl, arbitrary executable or filesystem primitive;
- no database connection, HTTP listener, session cookie implementation or IPC listener.

The generated JSON registries in `contracts/` are derived from the exact canonical Markdown exports in `specifications/`. They are implementation inputs, not new authority.

## Local validation

```sh
php -v
find src tests -name '*.php' -print0 | xargs -0 -n1 php -l
php tests/run.php
```

The current `composer.json` has **zero third-party dependencies**. The PHP 8.4 constraint is a development-candidate compatibility declaration only; it does not silently ratify the blocked full web-stack/toolchain decision.
