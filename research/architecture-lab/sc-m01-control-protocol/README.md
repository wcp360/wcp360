# WCP360 — Inert control-protocol parser R&D fixture

**Date:** 2026-10-26  
**Status:** RESEARCH / PROOF-OF-CONCEPT ONLY — NOT CANONICAL — NOT SOURCE-CODE CONSUMABLE  
**Owner for proposed contract review:** API / IPC / CONTRACTS + SECURITY / HARDENING + DEVOPS  
**Decisions:** C03, C06, C07; dependencies C01, C04, T02/T03/T04, DB #6, QA #7.

The exploratory `inert_codec.py` checks candidate frame parsing and synthetic assertion consistency only. It does not open a Unix socket, read Linux `SO_PEERCRED`, validate real service/cgroups, authenticate any live user, authorize an API principal, send IPC-B, execute a system command, interact with PostgreSQL, dispatch a job, or persist audit evidence.

The only candidate operations in this fixture are `status.read → applications.read` and `job.intent.submit → applications.create`. They are *intentionally synthetic and NOT registered WCP360 operations*. Their positive parser tests are not proof of authorized business behavior.

Run with Python 3.9+ standard library only:

```sh
python3 -m unittest discover -s research/architecture-lab/sc-m01-control-protocol -p 'test_inert_codec.py' -v
```

The synthetic trust fixture supplied to `decode_candidate_frame()` is a test input, **not a real trust mechanism**. Production controls require kernel peer verification, owner-approved trusted-context lifecycle/revocation, real RBAC/entitlement/tenant checks, DB durable intent and outbox, explicit IPC-B allowlist, state/revision/fencing and independent security review.

The recommended proposal is at:

`governance/proposals/SC-M01-CONTROL-BOUNDARY-C03-C06-C07-OWNER-OPTIONS-v0.1.md`

Tests cover parse/length/version/duplicate-key/UTF-8/NaN errors, unknown operations, forbidden caller-controlled privilege fields, surface mismatch, expired/revoked synthetic contexts, action binding, resource binding, read vs submit fields, and idempotency-key format.

**No privileged operation is implemented or authorized here.**

The candidate 32768-byte size limit, mock peer mappings and protocol version are not approved final values. A test PASS means only these selected parser assertions passed on an inert fixture.

```text
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```
