# Upgrade / Migration Policy

## Upgrade phases

`PRECHECK → BACKUP_STATE → MIGRATE → SWITCH → VERIFY → COMPLETE`

## Precheck

- supported source version;
- supported OS;
- free disk;
- package health;
- pending critical jobs;
- schema compatibility.

## Backup

Before upgrade:
- internal WCP state snapshot;
- configuration revision snapshot;
- critical secrets metadata backup according to secure policy.

## Migration

Migrations must be:
- versioned;
- resumable where feasible;
- backward-compatibility classified.

## Rollback

Application rollback is allowed only if:
- state/database migration supports it;
- binaries/config schemas remain compatible.

Otherwise use forward-fix/recovery.

## Zero-downtime claims

Do not claim zero downtime unless measured and architecturally supported.

## OS upgrades

Major distro upgrade remains separate from WCP application upgrade and requires dedicated compatibility workflow.