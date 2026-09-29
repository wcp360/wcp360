# Security Policy — WCP360 Repository

## Security authority

Security findings belong to:

`WCP360 — SECURITY / HARDENING`

Canonical artifacts:

```text
specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
```

## Repository secrets

Do not commit real passwords, API tokens, private keys, database credentials, session material, MFA seeds, recovery codes or customer data.

## Privileged architecture invariant

```text
backend
→ wcpd (non-root)
→ bounded authenticated IPC
→ wcp-privd (minimal root)
→ explicit privileged primitive
```

Forbidden:

```text
shell.exec
arbitrary command execution
arbitrary executable
generic sudo proxy
generic systemctl proxy
arbitrary filesystem target/write
arbitrary package-manager argv
arbitrary signal
generic root terminal
```

Any change introducing an equivalent generic privileged executor is a blocking security defect.

## Sensitive telemetry

Logs and audit data must never expose passwords, full tokens, private keys, sensitive cookies, database credentials, MFA secrets or raw sensitive IPC payloads.
