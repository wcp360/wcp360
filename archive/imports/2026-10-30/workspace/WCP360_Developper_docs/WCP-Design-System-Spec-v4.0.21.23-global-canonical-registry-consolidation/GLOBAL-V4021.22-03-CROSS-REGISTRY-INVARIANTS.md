# V4.0.21.22 --- Global Cross-Registry Invariants

1.  WCP-USER never supplies arbitrary root paths, shell commands,
    privileged SQL, service names or raw system configuration to the
    daemon.
2.  Tenant ownership/scope is checked before mutation and before
    sensitive reads.
3.  Platform security floor dominates reseller/plan/account overrides.
4.  Capability absence returns a reason-coded blocked/unavailable state.
5.  Suspend preserves data unless a separate retention/delete operation
    says otherwise.
6.  Destructive/high-risk operations use impact/confirmation/step-up
    where specified.
7.  Long/system-backed mutations are durable Jobs and idempotent.
8.  Success requires observed verification, not process exit alone.
9.  Secrets are references/write-only values and prohibited from normal
    audit/log payloads.
10. Provider adapters cannot weaken canonical authorization/isolation
    contracts.
11. DEMO simulation never creates privileged system side effects.
12. Recovery/installer/update paths do not become authorization
    bypasses.
