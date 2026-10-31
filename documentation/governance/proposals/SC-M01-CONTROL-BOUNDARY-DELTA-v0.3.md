# WCP360 — Control Boundary Delta v0.3 — narrowed open items

**Status:** OWNER HARMONIZATION — NON-NORMATIVE  
**Evidence:** governance/evidence/SC-M01-CANONICAL-CONTROL-BOUNDARY-EVIDENCE.md

This version narrows C03/C07 and leaves C06 genuinely open.

## C03 — surface binding

Final:
- WCP-USER and WCP-ADMIN are distinct security surfaces;
- runtime Unix identities are distinct;
- both have wcp360-control transport access;
- origin_surface is preserved in trusted context;
- transport access is not application authorization.

Open:
- exact peer/service identity → allowed origin_surface mapping;
- mismatch error;
- system/service-surface use;
- shared-vs-separate control-socket implication.

No fallback from one surface to the other is allowed.

## C06 — control protocol

Still open:
- protocol/version;
- framing;
- envelopes;
- sizes;
- timeout/deadline;
- errors;
- request/job semantics;
- idempotency;
- context-reference handling;
- compatibility.

Do not silently copy IPC-B.

## C07 — responsibility split

Final:
- backend cannot bypass wcpd for privileged IPC;
- wcpd is non-root control/orchestration daemon and sole IPC-B client;
- wcpd validates origin permission/action binding;
- wcp-privd enforces bounded technical operation/target;
- generic privileged forwarding is forbidden.

Open:
- backend vs wcpd business-validation split;
- durable job/intention producer;
- retry/reconciliation orchestrator;
- worker integration;
- synchronous vs async error/outcome split.

## Owner response

API/IPC + Security + DevOps should publish:

    C03 PEER→SURFACE BINDING =
    C06 CONTROL PROTOCOL =
    C07 BACKEND/WCPD RESPONSIBILITY =
    VERSION IMPACT =
    QA CASES =
    DEPENDENCIES =

No SOURCE CODE implementation is authorized by this delta.
