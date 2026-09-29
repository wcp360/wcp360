# WCP360 V2.5.3.23 — Updates / Packages / OS Lifecycle Functional Depth

## Style lock
V2.5.3.14 remains the active visual baseline. No shell redesign introduced.

## Developed family
- updates overview
- package review
- kernel / reboot state
- maintenance windows
- staged batch rollout
- update history
- exclusions / version holds

## Operational principles
- discovery is separated from execution;
- security updates are visually distinct from routine updates;
- installed kernel and running kernel are shown separately;
- reboot-required state is explicit;
- rollout supports pre-check → drain → apply → verify;
- canary health failure stops later batches;
- rollback availability is package-specific and never assumed;
- exclusions require owner/reason/review date;
- no blind “update all” for production fleet.

## Next family
Clusters / Failover / HA.