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
- exactly **47** allowlisted privileged IPC operation IDs; plus the canonical response envelope and 12 IPC error codes.

The experiment checks *some* syntactic invariants in isolation, before any official Go/compiler/runtime/framework choice is ratified. It adds no daemon, socket, process, OS mutation, deployment, privileged adapter or root code.

## Files

`codec.go`: pure in-memory framing/JSON parser for **both requests and responses**, with exact IPC-B request/response size bounds, canonical 12-code error registry, typed-ID allowlist and **no dispatcher**. Both returned objects are merely untrusted parsed data.

`operations.json`: noncanonical snapshot of all 47 operation IDs and accompanying metadata **derived exactly from**:

`specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md`

Blob SHA at generation: `fec9c74e8413db822662e47c76c40486e282334f`. The manifest has its own source-SHA field; Go tests re-extract all 47 operation IDs, **their distinct action IDs and resource identifier categories**, and verify this SHA from the current source Markdown. The parser now rejects any mismatched `action_id` ↔ `ipc_operation_id` pair even if both names look plausible; 19 of 47 canonical pairs intentionally use distinct strings. It also checks `resource_type` against the source row's **`resource identifier`** category for all 47 operations. **This equality is an R&D interpretation requiring explicit API/IPC owner ratification:** the source's column does not itself provide a complete field-by-field resource-type mapping contract. It must not be silently copied into a production IPC validator. Neither check proves resource existence, tenant ownership, authorization, canonical target resolution or actual permission grants.

`codec_test.go`, `response_test.go`, `payload_test.go`, and `correlation_test.go`: negative framing/parser tests, **47/47 IPC-B operation field-name tests** and action→operation binding tests, explicit UNKNOWN_OUTCOME→reconciliation rejection, response-to-request ID mismatch tests, and two seeded fuzz functions. No test performs privileged execution.

`go.mod`: stand-alone research module with Go standard library only. Its `go 1.23` compatibility declaration is **not** WCP360's final toolchain decision. See F01–F10 proposals/ENG #9.

## Test commands (R&D only)

```sh
cd research/architecture-lab/ipc-b-go-framing
go version
GOTOOLCHAIN=local GOPROXY=off GOSUMDB=off go test -count=1 -v ./...
GOTOOLCHAIN=local GOPROXY=off GOSUMDB=off go vet ./...
GOTOOLCHAIN=local GOPROXY=off GOSUMDB=off go test -race -count=1 ./...
```

The GitHub Actions workflow uses an ephemeral runner and logs the Go version. A future official build must pin and verify the compiler and toolchain provenance by Engineering/DevOps owner approval.

## Explicitly NOT validated

- Kernel peer identity: NO `SO_PEERCRED` implementation. A valid frame from an unauthorized peer is still **DENY** by the real contract.
- `authorization_context_ref` is NOT a bearer credential; this POC does not resolve, authenticate or revalidate it.
- A known `ipc_operation_id` does NOT authorize its use. The inert parser now checks **the exact canonical `action_id` associated with that operation ID** (19/47 pairs intentionally use different strings) and rejects a wrong pair; this is *source-reference consistency*, NOT a security grant. Live permission/action/job/tenant/resource/MFA/delegation/parent-binding authorization remains a separate mandatory check.
- Twelve Security matrix operations still require exact approved parent/provisioning/OR→typed tuple resolution in issue #91.
- **Per-operation `payload` REQUIRED/OPTIONAL FIELD NAMES are now checked** against flat `validated_input_schema` declarations for all 47 source operations (46 schemas with all-required fields; 1 schema with an optional `config_revision_id?`). Missing/unknown field names fail. **Value types, canonicalization, target registry/revision checks, resource resolution and real authorization are NOT validated.** The `TestNoProposedPayloadTypeCanAuthorizeAnOperation` test explicitly demonstrates an integer can still pass a field-name-only check for an ID field. An owner-approved typed implementation must reject invalid values before any effect.
- The published IPC-B/1 **§8 fixes `op_version` to integer `1`**; the parser now rejects strings, other integers, floating aliases, booleans and null. Only `deadline_ms` remains opaque at this research stage because actual T02/T03 clock/budget composition has unresolved owner semantics.
- Research-only parser depth/token limits are defensive hypotheses and NOT canonical protocol constraints.
- The new in-memory `MatchRequestResponseIdentifiers` helper compares `request_id`, `correlation_id`, and `ipc_operation_id` across parsed request and response. Wrong/crossed replies fail closed, but matching values do **not** authenticate the sender, bind a job in PostgreSQL, prove delivery or verify that a claimed `SUCCEEDED` outcome occurred.
- No persistent idempotency/jobs/audit, no OS side effect, no IPC-B socket lifecycle or actual `UNKNOWN_OUTCOME` reconciliation. The response parser **rejects** a syntactic UNKNOWN_OUTCOME that falsely sets `reconciliation_required = false`; it cannot prove the real reconciliation happened.
- No developer/compiler version from this experiment becomes the production stack. A response with `outcome: SUCCEEDED` is unverified parsed data, **not** proof of a real successful privileged effect. No test PASS is claimed for WCP360 product QA.

## Safety implications

A returned `Parsed` object means only that a bounded, source-known syntactic request was parsed. It MUST NOT be wired to an executable primitive, a DB mutation, or a privileged operation without the full accepted contracts and reviews. Unknown operation IDs are rejected without dynamic fallback. The library imports no `net`, `syscall`, `os/exec` or filesystem functions; test imports alone read the canonical contract to verify source integrity.

A positive research parser test is NOT an accepted `TEST-IPC-####` result. All implementation-dependent QA tests remain PLANNED/READY/BLOCKED.

**No implicit Sprint 0 reuse, no CODE-BASELINE, and no SC-M01 resume.**
