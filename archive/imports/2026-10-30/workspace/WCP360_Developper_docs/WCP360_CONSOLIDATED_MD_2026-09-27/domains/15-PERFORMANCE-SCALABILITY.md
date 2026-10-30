# WCP360 — Performance / Scalability

## Rule

No important optimization without:
- measurement;
- benchmark;
- profile;
- real problem;
- or explicit capacity objective.

Never trade security or data integrity for performance.

## Areas
- API latency/throughput;
- IPC;
- PostgreSQL;
- queues/workers;
- filesystem;
- DNS;
- mail;
- backups;
- reconciliation;
- bulk operations;
- multi-server growth.

## Current status

Architecture/performance interactions are identified, but production SLOs/capacity numbers are not yet justified.

Open:
- metric cardinality policy (`H3CM-X012`);
- queue saturation targets;
- DB load tests;
- IPC throughput;
- reconciliation load;
- bulk behavior;
- backup impact.
