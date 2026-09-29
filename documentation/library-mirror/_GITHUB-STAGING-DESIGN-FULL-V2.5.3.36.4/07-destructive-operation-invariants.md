# Destructive Operation Invariants

## General

A destructive action must never be a single blind click.

Required concepts:
- resource identity;
- scope;
- impact;
- recoverability;
- dependent resources;
- backup/recovery availability;
- explicit confirmation;
- authorization re-check;
- audit event.

## High-impact examples

### Database delete
Must show:
- DB name;
- application;
- size;
- users;
- backups;
- restore possibilities.

### Full production restore
Must show:
- target environment;
- selected restore point;
- expected overwritten data;
- pre-restore snapshot policy;
- estimated service impact;
- approval state if required.

### Server shutdown
Must show:
- workloads;
- HA status;
- recovery path;
- out-of-band access expectations.

### Firewall broad rule
Must show:
- changed exposure;
- operator lockout risk;
- currently active admin path.

### Break-glass
Must require:
- reason;
- incident/reference;
- hard expiry;
- post-use review.

## Typed confirmation

Typed confirmation may be required for selected irreversible/high-impact actions, but should not be used indiscriminately.

It is an additional friction mechanism, not authorization.