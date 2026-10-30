# V4.0.21.4.1 — Isolation Commands & Operations

## Candidate Operation IDs
`ISOLATION-PROFILE-LIST`, `ISOLATION-PROFILE-CREATE`, `ISOLATION-PROFILE-UPDATE`, `ISOLATION-PROFILE-ARCHIVE`, `ISOLATION-PROFILE-ASSIGN`, `ISOLATION-EFFECTIVE-READ`, `ISOLATION-CAPABILITY-DETECT`, `ISOLATION-APPLY`, `ISOLATION-RECONCILE`, `ISOLATION-VERIFY`, `ISOLATION-EVENTS-READ`, `QUOTA-USAGE-READ`, `QUOTA-SET`, `QUOTA-RECONCILE`.

## CLI conceptuelle
`wcpctl isolation profile-list`; `wcpctl isolation show --account <id>`; `wcpctl isolation effective --account <id>`; `wcpctl isolation profile-apply --account <id> --profile <id> --dry-run`; `wcpctl isolation verify --account <id>`; `wcpctl isolation events --account <id>`; `wcpctl quota usage --account <id>`; `wcpctl quota set --account <id> ...`.

Mutation : authorize → entitlement/ceiling → backend capability → preflight → plan/diff → Job → closed privileged action → verify observed state → audit.
