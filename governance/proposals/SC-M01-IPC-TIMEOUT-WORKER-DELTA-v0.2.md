# WCP360 — IPC Timeout / Worker Path Delta v0.2 — narrowed open items

**Status:** OWNER HARMONIZATION — NON-NORMATIVE  
**Evidence:** governance/evidence/SC-M01-CANONICAL-TIMEOUT-WORKER-EVIDENCE.md

## T01 — I/O timeout semantics

Final:
- I/O timeout value = 5 seconds.

Open:
- exact read/write/frame/progress semantics;
- composition with long operation execution.

No new timeout value is requested.

## T02 — deadline_ms

Final:
- required field exists.

Open:
- representation, clock/budget model, bounds and pre-dispatch expiry result.

## T03 — effective operation budget

Final:
- default = 30s;
- max = 900s;
- per-operation timeouts are already in IPC registry.

Open:
- composition with deadline_ms;
- budget start/end phases;
- inclusion of validation/durable intent/verification/reconciliation.

## T04 — UNKNOWN_OUTCOME

Classification remains:

    OPEN / PARTIALLY DEFINED

Final:
- possible execution + unproven completion = UNKNOWN_OUTCOME;
- reconciliation mandatory;
- no blind retry.

Open:
- exact phase boundary proving no dispatch;
- result-recovery details after response loss.

## T05 — idempotency binding

Classification remains:

    OPEN / PARTIALLY DEFINED

API logical semantics and IPC protection classes exist; durable technical binding remains open/DB-dependent.

## T06 / T07

Remain DB-dependent on #6.

## C04 — worker path

Final:
- worker non-root;
- no control supplementary group by default;
- direct IPC-B forbidden;
- privileged effects must use normal control path.

Recovered owner evidence:
- worker denied control socket by default.

Open:
- exact indirect mechanism connecting durable worker workflow to authorized wcpd control submission;
- producer/consumer/claim/fencing/context details.

Forbidden shortcut:

    add wcp360-worker to wcp360-control merely to make C04 work

## Owner output

API/IPC + DevOps should answer:

    T01 I/O PHASE =
    T02 deadline_ms REPRESENTATION =
    T03 BUDGET COMPOSITION =
    C04 WORKER INDIRECT PATH =

Database must answer only persistence/claim details after #6.
QA maps the resulting cases; no PASS/FAIL exists yet.
