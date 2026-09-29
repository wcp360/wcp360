# WCP360 V2.5.3.26 — Users / Roles / Plans / Privileges Functional Depth

## Style lock
V2.5.3.14 remains the active visual baseline. No shell redesign introduced.

## Developed family
- users
- roles
- plans
- feature lists
- effective permission tracing
- demo accounts
- reseller delegation
- quotas

## Authorization model
- plans define capability envelopes and default quotas;
- roles define reusable permission sets;
- feature lists support module-level and action-level control;
- explicit deny overrides inherited allow;
- effective permissions are traceable to source;
- demo is a dedicated restriction profile, not merely a cosmetic role;
- demo permissions can be enabled/disabled per module/action;
- sensitive operations remain blocked in demo even when pages are visible;
- resellers cannot delegate privileges outside their own envelope;
- quotas are independent from permissions;
- account-level overrides are explicit and auditable;
- temporary privileged elevation remains separate from persistent RBAC.

## Next family
Jobs / API / Webhooks / Automation.