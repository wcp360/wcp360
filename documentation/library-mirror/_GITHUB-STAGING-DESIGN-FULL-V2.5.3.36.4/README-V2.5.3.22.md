# WCP360 V2.5.3.22 — Servers / Services / Runtime Functional Depth

## Style lock
V2.5.3.14 remains the active visual baseline. No shell redesign was introduced.

## Developed family
- server inventory
- server overview
- system services
- runtime stack
- resource pressure
- system logs
- maintenance
- privileged server actions
- service detail

## Operational / security principles
- server-wide actions belong to WCP-ADMIN;
- application context never grants server privilege;
- reload is preferred over restart when safe and supported;
- restart/reboot actions show impact before confirmation;
- service config changes follow Validate → Diff → Save → Reload → Result;
- maintenance is more than a toggle and can include drain/capacity verification;
- runtime package installation/removal remains WCP-ADMIN;
- application users can select only runtimes allowed by policy;
- server actions require explicit temporary privilege where appropriate.

## Next family
Updates / Packages / OS lifecycle.