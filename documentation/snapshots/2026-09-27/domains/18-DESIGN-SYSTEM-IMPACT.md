# WCP360 — Design System Impact

## Active design baseline

`V2.5.3.36.4 — CSS Runtime Normalization`

Historical master archive is audit/history only.

The previously started Phase E Specialized Design Review was cancelled and must not be reused as validated output.

## No redesign

The current task is contract impact validation, not redesign.

UI/UX must eventually cover:
- permission denied;
- entitlement unavailable;
- policy blocked;
- wrong surface;
- reauthentication required;
- MFA required;
- approval required;
- async running;
- retry;
- `UNKNOWN_OUTCOME`;
- reconciliation required;
- partial failure;
- degraded dependency;
- destructive confirmation;
- audit/activity visibility;
- safe error disclosure.

## Security rule

A disabled/hidden button never substitutes backend authorization.

UI labels may differ from backend enum names only with deterministic mapping.
