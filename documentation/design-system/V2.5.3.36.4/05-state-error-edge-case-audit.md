# State, Error & Edge-Case Audit

## Required canonical state families
- provisioning / queued / running / validating / ready;
- degraded / warning / blocked / failed;
- suspended / read-only / maintenance;
- deleting / soft-deleted / restoring / recovered;
- draining / drained / failover-ready / failover-blocked;
- expiring / expired / revoked;
- pending-approval / approved / rejected / invalidated / approval-expired.

## Error requirements
Every actionable error should expose, when available:
- what failed;
- impacted resource;
- whether retry is safe;
- correlation ID;
- last known successful step;
- remediation action;
- required privilege;
- link to logs/job/audit/incident;
- support escalation route.

## Edge cases still needing visual validation
- concurrent edits / stale configuration;
- expired approvals before execution;
- privilege revoked while a job is queued;
- partial success in multi-resource operations;
- network loss during destructive confirmation;
- retry after timeout where backend outcome is unknown;
- secret reveal after session re-auth expires;
- storage full during backup/restore;
- failover candidate lag exceeding policy;
- deployment rollback blocked by irreversible migration;
- user account suspended while automation is scheduled;
- reseller loses capability inherited by child plans.

## Audit status
Taxonomy is mature; module-specific visual remediation coverage remains PARTIAL.