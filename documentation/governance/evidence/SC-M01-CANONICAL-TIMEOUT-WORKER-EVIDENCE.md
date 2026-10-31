# WCP360 — Canonical Timeout and Worker-Path Evidence

**Status:** CURRENT CANONICAL EVIDENCE + RECOVERED OWNER CONTEXT — NON-NORMATIVE ANALYSIS  
**Reference main:** 70ddd887a6b736950f2d73479e3ed3df827f58b4  
**Affected decisions:** T01, T02, T03, C04

Purpose: stop treating already-final timeout values and worker privilege-path invariants as blank design questions.

No canonical IPC/DevOps file is modified by this evidence note.

## 1. IPC-B timeout values are already FINAL

IPC WCP360-IPC-B/1 fixes:

    CONNECT TIMEOUT = 1 second
    I/O TIMEOUT = 5 seconds
    DEFAULT OPERATION TIMEOUT = 30 seconds
    MAX OPERATION TIMEOUT = 900 seconds

The request envelope also already requires:

    deadline_ms

Therefore T01/T02/T03 are not questions about choosing timeout numbers from scratch.

## 2. T01 narrowed — meaning of I/O TIMEOUT

Already final:
- 5 seconds is the canonical I/O timeout value.
- operation timeouts may be longer, including rows that use values up to the global 900-second maximum.
- malformed/incomplete framing cannot dispatch a privileged effect.

Still open:
- exactly which blocking I/O operations the 5-second limit covers;
- whether it is a per-read/write progress timeout, whole-frame timeout, response-start timeout, or another exact transport definition;
- how it composes with long-running operation execution;
- whether any transport progress/keepalive mechanism exists.

Therefore:

    T01 = OPEN / PARTIALLY DEFINED
    VALUE = FINAL
    PHASE SEMANTICS = OWNER DECISION REQUIRED

The owner must not replace 5 seconds with a new value merely to solve the phase ambiguity without an explicit versioned contract change.

## 3. T02 narrowed — deadline_ms

Already final:
- deadline_ms exists as a required IPC request field.
- timeout is not automatically definite failure.
- execution may have occurred but cannot be proven -> UNKNOWN_OUTCOME -> reconciliation -> no blind retry.

Still genuinely open:
- absolute timestamp vs relative duration/budget representation;
- clock basis;
- validation bounds;
- relationship to per-operation timeout;
- what an already-expired value returns before dispatch;
- how remaining budget is propagated across phases.

Therefore:

    T02 = OPEN
    FIELD EXISTENCE = FINAL
    FIELD SEMANTICS = NOT FULLY DEFINED

No timestamp/budget encoding is inferred here.

## 4. T03 narrowed — effective operation budget

Already final:
- default operation timeout = 30 seconds;
- max operation timeout = 900 seconds;
- individual IPC rows specify operation-specific timeout values;
- deadline_ms also exists.

Still open:
- precedence/composition between request deadline and row timeout;
- whether budget starts at request acceptance, post-validation, durable-intent boundary or dispatch;
- whether verification/reconciliation is inside the same budget;
- exact treatment of time spent in framing/authorization/durable pre-dispatch work.

Therefore:

    T03 = OPEN / PARTIALLY DEFINED
    LIMITS = FINAL
    EFFECTIVE-BUDGET FORMULA/PHASE = OWNER DECISION REQUIRED

## 5. Worker privilege-path invariants are already FINAL

DevOps v1.0 fixes:

    worker identity = wcp360-worker
    supplementary control group = none by default
    direct privileged IPC = forbidden

and states:

    Workers that require privileged effects must use the normal control path rather than direct access to wcp-privd.

IPC-B independently fixes:
- authorized IPC-B client = wcpd only;
- direct backend/WCP-USER/WCP-ADMIN/worker/hosting access to wcp-privd is forbidden.

Therefore C04 is not a choice between “worker may call IPC-B” and “worker may not”.

Direct worker -> IPC-B is already forbidden.

## 6. C04 narrowed — exact normal-control-path mechanism

Already final:
- worker is non-root;
- worker has no supplementary control group by default;
- worker does not access wcp-privd directly;
- privileged effects initiated from worker-related workflows must pass through the normal control path and ultimately wcpd.

Recovered M1 owner evidence also records worker denied direct access to the control socket by default.

Still open:
- how a worker-originated durable job reaches the component allowed to talk to wcpd;
- whether worker writes/claims DB state and a separate authorized dispatcher sends to wcpd;
- whether another typed local transport exists;
- exact producer/consumer/claim/fencing model;
- authorization-context lifecycle for worker-triggered execution.

Therefore:

    C04 = OPEN / PARTIALLY DEFINED
    DIRECT IPC-B = FORBIDDEN / FINAL
    DIRECT CONTROL-SOCKET ACCESS = DENIED BY DEFAULT / RECOVERED OWNER CONSENSUS
    INDIRECT NORMAL-CONTROL-PATH MECHANISM = OWNER DECISION REQUIRED

Do not solve C04 by silently adding wcp360-worker to wcp360-control.

## 7. QA implications

### Timeout cases

Now anchored:
- CONNECT timeout uses canonical 1 second.
- I/O timeout uses canonical 5 seconds.
- default/max operation limits are 30/900 seconds.
- tests must not treat all >5-second operations as automatically invalid merely because I/O timeout is 5 seconds; exact T01 semantics still need owner decision.

Still blocked:
- precise TQ02/TQ05 timing expectation until T01 is ratified;
- precise deadline-expiry result until T02/T03 are ratified.

### Worker cases

Now anchored:
- direct worker -> wcp-privd must fail;
- worker must not be given wcp360-priv;
- worker has no wcp360-control membership by default;
- tests must not “fix” CQ08 by changing groups.

Still blocked:
- positive worker->normal-control-path fixture until C04 mechanism is ratified.

## 8. Result

    IPC TIMEOUT NUMBERS = FINAL
    I/O PHASE SEMANTICS = OPEN
    deadline_ms FIELD = FINAL
    deadline_ms SEMANTICS = OPEN
    OPERATION LIMITS = FINAL
    BUDGET COMPOSITION = OPEN

    WORKER DIRECT IPC-B = FORBIDDEN / FINAL
    WORKER DIRECT CONTROL GROUP = NONE BY DEFAULT / FINAL
    WORKER INDIRECT NORMAL CONTROL PATH = REQUIRED / FINAL
    EXACT INDIRECT MECHANISM = OPEN

    CANONICAL OWNER FILES CHANGED = NO
    APPLICATION TEST EXECUTION = 0
    SC-M01 = PAUSED
