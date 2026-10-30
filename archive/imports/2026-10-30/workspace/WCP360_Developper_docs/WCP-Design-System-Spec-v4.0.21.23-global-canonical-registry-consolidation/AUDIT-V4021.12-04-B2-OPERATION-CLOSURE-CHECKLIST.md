# V4.0.21.12 --- B2 Operation Closure Checklist

Chaque Operation ID doit mapper exactement : operation id →
resource/action → HTTP/API contract → capability permission → scope →
entitlement → account/resource lifecycle guard → step-up →
idempotency/concurrency → Job ou NO_JOB → Decision → privileged
Action(s) → audit event → CLI mapping → UI action → error codes →
acceptance row.

## Interdictions

-   opération générique `EXEC`, `SHELL`, `RAW-CONFIG`;
-   wildcard qui accorde implicitement une future permission;
-   mutation système sans Job lorsque la durabilité/retry est
    nécessaire;
-   Job privilégié sans closed Action contract;
-   UI-only security decision.
