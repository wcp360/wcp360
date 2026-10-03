# WCP360 — Canonical Control-Boundary Evidence

**Status:** CURRENT CANONICAL EVIDENCE — NON-NORMATIVE ANALYSIS  
**Reference main:** 19a5e2e62977ea092c349667ab40646eaffbe067  
**Affected decisions:** C03, C06, C07

Purpose: narrow control-boundary decisions using current FINAL Security and DevOps rules without inventing the control wire protocol.

## 1. Already canonical: runtime identities and transport access

DevOps v1.0 fixes:

    WCP-USER runtime = wcp360-user
    supplementary group = wcp360-control

    WCP-ADMIN runtime = wcp360-admin
    supplementary group = wcp360-control

    wcpd = distinct non-root daemon
    wcp360-control != wcp360-priv

Transport access does not replace authentication/authorization/entitlement/ownership/delegation/tenant/policy checks.

Security v1.0 independently fixes:

    WCP-USER != WCP-ADMIN

and preserves origin_surface in the trusted authorization context.

Therefore both runtimes may reach the logical control boundary, but their application surfaces must remain distinguishable.

## 2. C03 — USER/ADMIN surface binding

Already final:
- Unix identities for USER and ADMIN are distinct;
- application surfaces are distinct;
- origin_surface is a trusted-context field;
- transport-group membership is not application authorization;
- unknown permission/action/IPC binding fails closed.

Still open:
- exact normative mapping from peer Unix identity/service context to allowed origin_surface values;
- whether a shared control socket or separate sockets are used after C01 republication;
- what happens if peer identity and origin_surface disagree;
- exact safe error code/projection;
- whether non-web/system peers may use a SYSTEM/service surface on the same control protocol.

Therefore:

    C03 = OPEN / PARTIALLY DEFINED
    SEPARATION = FINAL
    EXACT PEER→SURFACE BINDING TABLE = OWNER DECISION REQUIRED

A shared Unix group must not collapse the USER/ADMIN surface distinction.

## 3. Already canonical: wcpd role at privileged boundary

Security fixes:

    backend
    → wcpd non-root
    → IPC-B
    → wcp-privd root minimal
    → explicit bounded primitive

and:

    wcpd validates origin (permission_id, action_id)
    wcp-privd validates bounded ipc_operation_id and target constraints

DevOps identifies wcpd as:

    role = control/orchestration daemon
    privileged IPC role = client
    generic sudo = forbidden
    generic command execution = forbidden

IPC-B authorizes only wcpd as client.

Therefore wcpd is not a transparent byte-forwarder and wcp-privd is not the application authorization engine.

## 4. C07 — backend vs wcpd responsibility boundary

Already final:
- backend does not call wcp-privd directly;
- wcpd is the control/orchestration daemon;
- wcpd is the sole normal IPC-B client;
- wcpd validates origin permission/action binding before privileged dispatch;
- wcp-privd enforces bounded technical operation/target;
- no generic fallback exists.

Still open:
- exact business-validation split between backend and wcpd;
- which component creates/loads the durable job/intention;
- which component owns retries/reconciliation orchestration for each workflow;
- exact worker-to-authorized-dispatch integration;
- exact control-protocol request/response/job semantics;
- which validation failures are returned synchronously vs persisted as job outcome.

Therefore:

    C07 = OPEN / PARTIALLY DEFINED
    TRUST/PRIVILEGE SPLIT = FINAL
    WORKFLOW RESPONSIBILITY DETAILS = OWNER DECISION REQUIRED

## 5. C06 — control protocol remains genuinely open

Current owner artifacts establish the logical boundary but do not publish a full canonical control wire protocol comparable to IPC-B.

Still needed for SC-M01 components that use it:

    protocol/version identity
    framing
    request envelope
    response envelope
    max sizes
    timeout/deadline behavior
    error registry/projection
    request/job relationship
    idempotency/replay behavior
    authorization_context reference/lookup
    compatibility/deprecation

Do not clone IPC-B implicitly.

Do not create a new protocol namespace merely because C06 is open; API/IPC owner must decide whether an existing recovered/approved source already covers it or whether a new explicit version is needed.

## 6. C01 / C02 / C04 relationships

C01:
- recovered owner consensus exists for /run/wcp360/wcpd.sock, owner wcpd, group wcp360-control, mode 0660;
- current owner republication/confirmation remains required.

C02:
- transport access != application authorization is already defined in DevOps/Security.

C04:
- worker direct IPC-B forbidden;
- no control group by default;
- normal control path required;
- exact indirect mechanism remains open.

These constraints limit C03/C06/C07 solutions.

## 7. QA implications

Already anchorable negative cases:
- USER peer with ADMIN surface/context mismatch must not gain ADMIN authority;
- ADMIN peer with USER context mismatch must not silently switch surface;
- unknown/invalid origin permission/action must fail closed;
- backend/worker cannot bypass wcpd to IPC-B;
- wcpd cannot forward arbitrary operation/command fallback.

Still blocked:
- exact positive control-request fixture;
- exact error code/protocol framing;
- exact async job/response semantics;
- worker positive indirect path.

No PASS/FAIL is created here.

## 8. Result

    C03 = OPEN / PARTIALLY DEFINED
    C06 = OPEN
    C07 = OPEN / PARTIALLY DEFINED

    CONTROL BOUNDARY EXISTENCE = FINAL
    WCP-USER/WCP-ADMIN SEPARATION = FINAL
    WCPD AUTHORIZATION-BINDING ROLE = FINAL
    WCPD IPC-B CLIENT ROLE = FINAL
    CONTROL WIRE PROTOCOL = NOT FULLY PUBLISHED
    WORKFLOW RESPONSIBILITY SPLIT = PARTIAL

    CANONICAL OWNER FILES CHANGED = NO
    APPLICATION TEST EXECUTION = 0
    SC-M01 = PAUSED
