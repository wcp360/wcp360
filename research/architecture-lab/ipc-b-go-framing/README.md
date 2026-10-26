# WCP360 — IPC-B/1 Go framing experiment (non-production)

**Date:** 2026-10-27  
**Status:** RESEARCH / POC — NON-CANONICAL — NOT SOURCE-CODE-CONSUMABLE  
**Workstream:** TECHNICAL R&D / ARCHITECTURE LAB  
**Owner review:** API / IPC / CONTRACTS; SECURITY / HARDENING; ENGINEERING / CODE REVIEW; TESTS / QA; DEVOPS / BUILD / RELEASE  
**Baseline:** WCP360-BM-0001 FINAL (DOC v4.0.21.23, DESIGN V2.5.3.36.4, CODE NONE)  
**Global implementation gate:** #13; SC-M01 PAUSED; SC-M02 NOT AUTHORIZED.

## Why this exists

The final `WCP360-IPC-B/1` privileged-IPC specification already defines:

- `uint32` big-endian frame length followed by strict UTF-8 JSON;
- maximum request payload **65,536 bytes**, response **262,144 bytes**;
- canonical request fields `protocol_version,op_version,request_id,correlation_id,job_id?,action_id,ipc_operation_id,resource_type,resource_id,authorization_context_ref,deadline_ms,payload`;
- strict schema validation: no malformed JSON, no duplicate keys, no unknown operation, no generic execution fallback;
- only `wcpd` as an authenticated client of the root-minimal `wcp-privd`, with kernel `SO_PEERCRED` and expected service/cgroup verification;
- exactly **47** allowlisted privileged IPC operation IDs.

The experiment checks *some* syntactic invariants in isolation, before any official Go/compiler/runtime/framework choice is ratified. It adds no daemon, socket, process, OS mutation, deployment, privileged adapter or root code.

## Files

`codec.go`: pure in-memory framing/JSON parser + typed-ID allowlist **without a dispatcher**. Its return value is merely untrusted parsed data.

`operations.json`: noncanonical snapshot of all 47 operation IDs and accompanying metadata **derived exactly from**:

`specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md`

Blob SHA at generation: `fec9c74e8413db822662e47c76c40486e282334f`. The manifest has its own source-SHA field; Go tests re-extract all 47 IDs and verify this SHA from the current source Markdown.

`codec_test.go`: negative framing/parser tests and a seeded fuzz function. No test performs privileged execution.

`go.mod`: stand-alone research module with Go standard library only. Its `go 1.23` compatibility declaration is **not** WCP360's final toolchain decision. See F01–F10 proposals/ENG #9.

## Test commands (R&D only)

```sh
cd research/architecture-lab/ipc-b-go-framing
go version
GOTOOLCHAIN=local GOPROXY=off GOSUMDB=off go test -count=1 -v ./...
GOTOOLCHAIN=local GOPROXY=off GOSUMDB=off go vet ./...
```

The GitHub Actions workflow uses an ephemeral runner and logs the Go version. A future official build must pin and verify the compiler and toolchain provenance by Engineering/DevOps owner approval.

## Explicitly NOT validated

- Kernel peer identity: NO `SO_PEERCRED` implementation. A valid frame from an unauthorized peer is still **DENY** by the real contract.
- `authorization_context_ref` is NOT a bearer credential; this POC does not resolve, authenticate or revalidate it.
- A known `ipc_operation_id` does NOT authorize its use. Permission/action/job/tenant/resource, MFA, approval, delegation and parent bindings remain separate mandatory checks.
- Twelve Security matrix operations still require exact approved parent/provisioning/OR→typed tuple resolution in issue #91.
- Per-operation `payload` fields/types/target registry and revision checks are NOT validated here. The 47 source rows have detailed schemas requiring a separate owner-approved implementation.
- `op_version` and `deadline_ms` are carried as syntactically present *opaque values* to avoid inventing the still-open T02/T03 semantics.
- Research-only parser depth/token limits are defensive hypotheses and NOT canonical protocol constraints.
- No persistent idempotency/jobs/audit, no OS side effect, no IPC-B socket lifecycle, no `UNKNOWN_OUTCOME` reconciliation.
- No developer/compiler version from this experiment becomes the production stack. No test PASS is claimed for WCP360 product QA.

## Safety implications

A returned `Parsed` object means only that a bounded, source-known syntactic request was parsed. It MUST NOT be wired to an executable primitive, a DB mutation, or a privileged operation without the full accepted contracts and reviews. Unknown operation IDs are rejected without dynamic fallback. The library imports no `net`, `syscall`, `os/exec` or filesystem functions; test imports alone read the canonical contract to verify source integrity.

A positive research parser test is NOT an accepted `TEST-IPC-####` result. All implementation-dependent QA tests remain PLANNED/READY/BLOCKED.

**No implicit Sprint 0 reuse, no CODE-BASELINE, and no SC-M01 resume.**
