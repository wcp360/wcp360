# WCP360 V2.5.3.30 — Cross-Cutting Closure Batch 1

## Style lock
V2.5.3.14 remains the active visual baseline. No global shell redesign was introduced.

## Closed P0 gaps
- Notifications
- Approval Center
- Storage / Volumes
- Processes / Workers

## Notifications
- inbox and grouping;
- preferences;
- routing;
- deduplication;
- cooldowns;
- digests;
- escalation;
- delivery health;
- forced critical channels when policy requires.

## Approvals
- request list;
- request detail;
- requester/approver distinction;
- expiry;
- rejection;
- invalidation after scope change;
- execution-time permission and state re-check;
- self-approval restrictions by policy.

## Storage / Volumes
- volume inventory;
- filesystem/mount state;
- capacity;
- usage warnings;
- expansion workflow;
- provider capacity vs filesystem resize;
- no assumption that shrink/rollback is safe.

## Processes / Workers
- process list;
- process detail;
- worker pools;
- CPU/RAM visibility;
- application/service ownership;
- graceful TERM first;
- KILL as last resort;
- privileged handling and audit for out-of-scope/system processes.

## Remaining P0 gates
- measured WCAG 2.2 AA validation;
- responsive/mobile capability validation.

## Next recommended phase
V2.5.3.31 — Validation & Accessibility Gate:
- WCAG 2.2 AA;
- keyboard navigation;
- screen reader semantics;
- focus;
- contrast;
- reduced motion;
- responsive/mobile behaviors;
- RTL/i18n stress test.