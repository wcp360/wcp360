# WCP360 — Recovered IPC Timeout / Outcome Final Evidence

**Status:** HISTORICAL OWNER-FINAL RECOVERY EVIDENCE — NON-CANONICAL
**Recovery date:** 2026-10-04
**Target domains:** API / IPC / CONTRACTS, DevOps, Security
**Related decisions:** T01, T04
**Related findings:** AUDIT-WCP-0107, AUDIT-WCP-0108

This file records final/ratified historical WCP360 decisions recovered from prior owner/MASTER conversations.

It is not a new contract and is not directly consumable by SOURCE CODE. The relevant owners should republish/harmonize the recovered decisions into current normative artifacts where needed.

## 1. H3CM-X004 final ratification recovered

Recovered final response around 2026-09-27 12:37 UTC was explicitly identified as:

    H3CM-X004 — FINAL RATIFICATION RESPONSE
    STATUS = CONSENSUS REACHED
    MASTER closure authority unchanged

Recovered physical IPC-B values:

    SOCKET = /run/wcp360/priv/wcp-privd.sock
    OWNER/GROUP = root:wcp360-priv
    MODE = 0660
    AUTHORIZED CLIENT = wcpd only
    PEER AUTH = SO_PEERCRED exact wcpd identity / expected service context
    PROTOCOL = WCP360-IPC-B/1
    FRAMING = 4-byte big-endian length + strict UTF-8 JSON
    MAX REQUEST = 65,536 bytes
    MAX RESPONSE = 262,144 bytes

Recovered timeout wording:

    CONNECT TIMEOUT = 1 second
    I/O TIMEOUT = 5 seconds per complete frame read/write
    DEFAULT OPERATION TIMEOUT = 30 seconds
    MAX OPERATION TIMEOUT = 900 seconds

The key recovered semantic point is that the 5-second I/O timeout applies to the framed read/write operation; it is not the total execution time of an operation. The operation timeout is a separate budget family.

Later H3-V2/MASTER closure evidence states H3CM-X001→X013 closed and preserves this IPC-B physical contract.

## 2. Consequence for T01

T01 currently asks whether:

- 5 seconds means total wait for an operation; or
- 5 seconds means frame I/O/progress separate from operation execution.

Historical final evidence resolves that ambiguity:

    T01 = RECOVERED OWNER-FINAL
    I/O TIMEOUT 5s = complete frame read/write
    operation timeout = separate

Therefore T01 should no longer be treated as a new semantic design choice.

Current owner action:

    REPUBLISH / HARMONIZE recovered final wording
    do not redesign

The remaining implementation detail is how the runtime enforces read/write deadlines for the selected IPC library without changing the recovered semantics.

## 3. Recovered UNKNOWN_OUTCOME invariant

Final/MASTER-closed readiness evidence also preserves:

    timeout != definite failure
    retry != reconciliation
    execution success != verified success

When privileged/external execution may have occurred but completion cannot be proven:

    UNKNOWN_OUTCOME
    → observe / reconcile
    → no blind retry

Recovered final/ratified wording also requires durable pre-dispatch intent/evidence before privileged dispatch and terminal result/audit afterward.

A timeout or connection loss after possible dispatch is not evidence of cancellation and must not be converted automatically to FAILED.

## 4. Consequence for T04

T04 is not fully open.

Recovered final core:

    possible post-dispatch external effect + unprovable completion
    → UNKNOWN_OUTCOME
    → reconciliation
    → no blind retry

Current open/harmonization work is narrower:

- exact phase marker proving dispatch did or did not occur;
- deterministic error/result projection before dispatch;
- retrieval of a durable result when response transmission is lost after final persistence;
- HTTP/control-transport mapping around the canonical outcome;
- exact persistence references after Database #6.

Recommended coordination status:

    T04 = RECOVERED FINAL CORE / HARMONIZATION OPEN

Do not reopen the existence of UNKNOWN_OUTCOME or allow blind destructive retry.

## 5. What remains genuinely open

### T02 — deadline_ms

Targeted recovery found no FINAL / MASTER-closed / ratified decision defining whether deadline_ms is:

- absolute timestamp;
- relative duration;
- monotonic budget;
- another representation.

No final evidence was recovered for clock source, propagation or encoding.

Therefore:

    T02 = OPEN

### T03 — effective operation budget

The following are final:

    default = 30s
    maximum = 900s
    operation-specific timeouts exist in the 47-operation registry

But targeted recovery did not prove a final rule for:

- when the operation budget starts/stops;
- exact composition with deadline_ms;
- whether validation time consumes the per-operation budget.

Therefore:

    T03 = OPEN / PARTIALLY DEFINED

### T05–T07

Idempotency/recovery invariants are partially final, but exact durable technical binding and persistence/claim rules remain dependent on API/IPC and Database #6 as already tracked.

## 6. QA impact

TQ02 and TQ05 can now be narrowed:

- TQ02: partial/incomplete frame with no completed frame within the I/O timeout must fail transport-side without privileged dispatch.
- TQ05: a legitimate long-running operation must not fail merely because no frame bytes are exchanged for more than 5 seconds while the separate operation budget remains valid.

These remain PLANNED until TESTS / QA makes them canonical and implementation exists.

Tests around deadline_ms remain BLOCKED on T02/T03.

## 7. Publication / authority rule

This recovery evidence is not a replacement for the canonical IPC artifact.

Required owner action:

    API / IPC / CONTRACTS
    → confirm/republish the recovered H3CM-X004 T01 semantics
    → preserve T04 final UNKNOWN_OUTCOME invariant
    → decide T02/T03 remaining gaps
    → version any real semantic amendment honestly

No new IPC version is implied merely by recovering the already-final T01/T04 semantics.

## 8. Current state

    T01 = RECOVERED OWNER-FINAL — REPUBLISH/HARMONIZE
    T02 = OPEN
    T03 = OPEN / PARTIALLY DEFINED
    T04 = RECOVERED FINAL CORE — HARMONIZATION OPEN
    APPLICATION IMPLEMENTATION = NONE
    APPLICATION TEST EXECUTION = 0
    SC-M01 = PAUSED — TARGETED EVIDENCE
    CODE-BASELINE = NONE
