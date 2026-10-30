# V4.0.21.20 --- Composer / npm / pip / Bundler Supply-Chain Contract

Dependency tools execute as tenant/application identity in bounded Jobs,
never as root daemon. Lockfiles are preferred/required by deployment
policy where reproducibility matters.

Operations: `DEPENDENCY-INSTALL`, `DEPENDENCY-UPDATE-PLAN`,
`DEPENDENCY-AUDIT`, `DEPENDENCY-CACHE-CLEAR`.

Package lifecycle scripts/hooks are treated as application code
execution. Network egress, timeout, CPU/RAM/PID/disk limits and output
limits apply. Credentials for private registries use SecretStore
references and redaction.

WCP may surface vulnerability/audit results from certified
tools/providers but does not silently mutate dependencies unless
explicit policy permits it.
