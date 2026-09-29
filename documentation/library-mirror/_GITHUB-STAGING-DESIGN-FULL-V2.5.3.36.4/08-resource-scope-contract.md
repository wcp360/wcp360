# Resource Identity & Scope Contract

## Resource hierarchy

### WCP-USER
`Account / Organization`
→ `Project / Application`
→ attached resources:
- domains;
- databases;
- mail;
- files;
- backups;
- certificates;
- jobs;
- activity.

### WCP-ADMIN
`Infrastructure`
→ `Cluster`
→ `Server`
→ services / storage / network / runtime / jobs / incidents.

## Identity requirements

Every resource must have:
- stable internal ID;
- human display name;
- resource type;
- owning scope;
- lifecycle state;
- created/updated timestamps;
- labels/tags where supported;
- audit correlation references.

## Scope invariants

- moving UI context does not elevate rights;
- API tokens are scope-bound;
- reseller children remain under delegated envelope;
- impersonation changes effective view/actor context but not the real actor identity;
- server resources cannot be reached from WCP-USER unless explicitly projected as app-level capabilities.

## Soft deletion

User-facing resources should prefer recoverable deletion where feasible.

Infrastructure resources may require hard delete if the underlying system cannot safely support recovery.

The UI must never promise recovery when backend semantics are destructive.