# WCP360 V2.5.3.25 — Firewall / Network / Connectivity Functional Depth

## Style lock
V2.5.3.14 remains the active visual baseline. No shell redesign introduced.

## Developed family
- firewall overview
- interfaces
- firewall rules
- NAT
- routes / DNS
- exposed ports
- network diagnostics

## Security / operational principles
- default-deny posture;
- explicit public vs private interface context;
- anti-lockout simulation before firewall apply;
- safe flow: Validate → Diff → Simulate lockout → Apply → Verify → Audit;
- current operator path is checked before rule changes;
- NAT changes surface impacted services;
- active listeners are compared with expected exposure;
- private cluster/storage networks remain separate from public interfaces;
- unexpected listeners become findings;
- diagnostics are contextual and do not bypass firewall policy.

## Next family
Users / Roles / Plans / Privileges.