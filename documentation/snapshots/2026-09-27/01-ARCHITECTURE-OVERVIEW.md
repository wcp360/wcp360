# WCP360 — Architecture Overview

## Product surfaces

`Browser → WCP-USER / WCP-ADMIN → Web Backend / Application Services → PostgreSQL / Jobs → IPC → wcpd → wcp-privd → Linux/services/filesystem`

## Core invariants

1. WCP-USER and WCP-ADMIN are distinct security surfaces.
2. `wcpd` is non-root.
3. `wcp-privd` is the minimal privileged/root boundary.
4. Web/PHP must never call `wcp-privd` directly.
5. No arbitrary privileged command execution.
6. Control-plane DB is distinct from hosted DB engines and tenant DB contents.
7. UI state is not authorization.
8. Correlation/origin context is not authorization context.
9. Audit/logs/metrics are not authorization.
10. Critical system mutation favors validate + bounded execution + verify + reconciliation/compensation as applicable.

## State model

WCP360 distinguishes:
- requested state;
- desired state;
- executing state;
- observed state.

`execution success != verified desired state`

`timeout != failure`

`UNKNOWN_OUTCOME` is a first-class state requiring observation/reconciliation before unsafe retry.
