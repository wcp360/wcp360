# WCP360 V2.5.3.21 — Monitoring / Metrics / Incidents Functional Depth

## Style lock
V2.5.3.14 remains the active visual baseline. No shell redesign was introduced.

## Developed family
- monitoring overview
- metrics
- alerts
- SLO / error budget
- capacity
- incidents
- incident detail
- correlation
- WCP-ADMIN infrastructure monitoring

## Operational principles
- alerts should be actionable and low-noise;
- metrics are contextual, not standalone charts;
- incidents correlate metrics, alerts, jobs and audit;
- correlation is evidence and must not be presented as automatic root cause;
- SLO and error budget inform operational decisions;
- capacity forecasting is surfaced before exhaustion;
- WCP-USER sees application-level health and incidents;
- WCP-ADMIN sees fleet-level health, capacity and operator runbooks;
- privileged mitigation remains a separate explicit security workflow.

## Next family
Servers / Services / Runtime.