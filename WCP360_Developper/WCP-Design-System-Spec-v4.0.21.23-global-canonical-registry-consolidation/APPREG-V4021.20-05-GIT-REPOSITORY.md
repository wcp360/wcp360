# V4.0.21.20 --- Git Repository Contract

Operations: `GIT-REPOSITORY-CREATE`, `GIT-CLONE`, `GIT-PULL`,
`GIT-REMOTE-SET`, `GIT-DEPLOY-KEY-ROTATE`, `GIT-DELETE`.

Remote credentials/deploy keys are SecretStore-backed. Host-key
verification is required; disabling it is not a tenant convenience
switch. URLs/protocols are validated against SSRF/local-network policy
where server-side network access occurs.

Repository hooks are untrusted application execution, never privileged
daemon extensions. Repository path remains under managed root and
ownership/isolation.
