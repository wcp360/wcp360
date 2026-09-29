# WCP360 V2.5.3.28 — Application Lifecycle / Runtime / Environment / Deployments Functional Depth

## Style lock
V2.5.3.14 remains the active visual baseline. No shell redesign introduced.

## Developed family
- application lifecycle overview
- Production / Staging / Development
- application runtime selection
- environment variables and secrets
- builds
- releases
- deployments
- health checks
- application maintenance
- rollback

## Application lifecycle principles
- environment context never changes privilege;
- secrets use a dedicated surface, not normal file editing;
- application runtime choices are constrained by WCP-ADMIN policy;
- builds and releases are distinct;
- releases are immutable deployment artifacts;
- production deployments use pre-check → prepare → activate → verify → complete;
- known-good rollback target is identified before production activation;
- database migrations can make rollback conditional or unsafe;
- app maintenance is distinct from server maintenance;
- deployments execute as observable jobs and correlate with audit/incidents.

## Next step
Cross-family completeness audit and page/system dependency matrix before any production implementation.