# V4.0.21.20 --- Application Secrets / Environment Variables

Resources distinguish non-secret environment configuration from
`app_secret`.

Operations: `ENV-SET`, `ENV-DELETE`, `SECRET-SET`, `SECRET-ROTATE`,
`SECRET-DELETE`, `SECRET-BIND`.

Secret values are write-only through normal control-plane APIs after
creation unless an explicitly authorized reveal workflow exists. Audit
stores names/ versions, not values. Job logs redact secrets.

`.env` files, when required by an application, are rendered through a
privileged/ tenant-safe file operation with strict ownership/mode and
never used as the canonical secret store.
