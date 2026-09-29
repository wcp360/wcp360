# WCP360 V2.5.3.27 — Jobs / API / Webhooks / Automation Functional Depth

## Style lock
V2.5.3.14 remains the active visual baseline. No shell redesign introduced.

## Developed family
- Job Center
- job detail / dependency graph
- queue isolation
- API access
- signed webhooks
- delivery retries/history
- cron
- automations

## Execution principles
- long-running work executes as observable jobs;
- retries are bounded and idempotency-aware;
- dependencies are explicit;
- queues are isolated by workload class;
- API access is scope-based and versioned;
- webhook secrets are SHOW_ONCE and deliveries are signed;
- webhook retries use bounded backoff and dead-letter states;
- cron is application-scoped;
- automation uses explicit service identities;
- automation never bypasses RBAC/MFA/privilege policy;
- execution events correlate with Audit and Incidents.

## Next family
Application Lifecycle / Runtime / Environment / Deployments.