# WCP360 — Recovered Owner Evidence — Control Boundary M1

Date of recovery: 2026-10-02
Historical decision date: 2026-09-27
Historical conversation title: WCP360 — H3-CM FINAL RATIFICATION — M1
Reference: H3CM-X004 / H3CM-X005
Historical status: CONSENSUS REACHED

This file preserves recovered project-conversation evidence. It is not a replacement canonical owner artifact and does not itself modify DevOps, Security or API/IPC baselines.

## 1. Historical ratifiers

Recovered final consensus identifies these WCP360 authorities:

- MASTER / Project Control
- SECURITY / HARDENING
- DEVOPS / BUILD / RELEASE
- API / IPC / CONTRACTS

## 2. Recovered final control-boundary decision

Historical final consensus recorded:

    control socket path = /run/wcp360/wcpd.sock
    owner = wcpd
    group = wcp360-control
    mode = 0660

Authorized/default clients:

    wcp360-user  = transport access through wcp360-control
    wcp360-admin = transport access through wcp360-control

Denied by default from this control socket:

    wcp360-worker
    hosting identities

Peer Unix identity/context remains distinguishable and transport membership does not replace application authorization.

## 3. Privileged boundary remained separate

The same final M1 consensus separately fixed IPC-B:

    /run/wcp360/priv/wcp-privd.sock
    owner = root
    group = wcp360-priv
    mode = 0660
    client = wcpd only

with the already-published WCP360-IPC-B/1 framing/authentication constraints.

The control group and privileged group are therefore distinct:

    wcp360-control != wcp360-priv

WCP-USER/WCP-ADMIN transport access to wcpd never implies access to wcp-privd.

## 4. Historical contradiction resolved

Earlier/provisional material used names such as:

    wcp360-ctl
    generic wcp360 group

Recovered M1 final consensus uses:

    wcp360-control

The current DevOps/Security exports also use wcp360-control for WCP-USER/WCP-ADMIN, which is consistent with this recovered consensus.

Historical variants must not be reintroduced.

## 5. What this evidence does NOT recover

The recovered M1 decision does not, by itself, fully define:

- the complete request/response framing of the control IPC;
- its protocol/version identifier;
- all request/response DTOs;
- the exact trusted authorization-context lookup/resolution mechanism;
- the indirect normal-control-path mechanism for wcp360-worker;
- retries/idempotency semantics of the control IPC;
- the full systemd/socket lifecycle;
- all error mappings.

Those remain owner work unless another exact owner-approved source is recovered.

## 6. Republication requirement

Because the current final DevOps export describes the logical control boundary and wcp360-control but does not reproduce all M1 physical control-socket fields, this recovered evidence is classified as:

    RECOVERED OWNER CONSENSUS
    CANONICAL REPUBLICATION / OWNER CONFIRMATION REQUIRED

An owner may resolve the point by:

1. pointing to an already-canonical exact source that contains the M1 fields; or
2. republishing the historical consensus as an explicit content-preserving normative clause; or
3. explicitly amending it with version impact and rationale.

MASTER/proposal documents must not silently promote this recovery note into owner authority.

## 7. Effect on PR #43 decision work

The following are no longer blank-design questions:

- candidate path /run/wcp360/wcpd.sock;
- owner wcpd;
- group wcp360-control;
- mode 0660;
- USER/ADMIN transport clients;
- worker/hosting denied by default.

They are recovered owner-consensus evidence awaiting republication/confirmation.

Still-open questions include:

- detailed USER/ADMIN surface/context binding inside the control protocol;
- worker indirect control path;
- trusted context lifecycle;
- control IPC wire protocol;
- exact wcpd responsibility boundary.

No SOURCE CODE implementation is authorized from this evidence file alone.
