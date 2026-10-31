# WCP360 — SC-M01 historical PostgreSQL table-fragment inventory

**Date:** 2026-10-27  
**Classification:** G0 DERIVED HISTORICAL EVIDENCE — **NON-CANONICAL / INCOMPLETE**  
**Owner:** DATABASE / DATA MODEL (ultimate authority, NOT approving this index)  
**Issue:** [#6](https://github.com/yyogas/wcp360/issues/6)  
**Master:** [#13](https://github.com/yyogas/wcp360/issues/13)  
**Existing owner proposal:** `governance/proposals/SC-M01-DB-PHYSICAL-CONTRACT-OWNER-DECISION-DRAFT-2026-10-26.md` (#84 already preserved in main)  
**Source:** `governance/recovery/SC-M01-RECOVERED-DB-FINAL-ARTIFACT-EVIDENCE.md`  
**Source Git blob SHA:** `7e06ce38c2dd742cfec9669884d7f13b4b611f39`

## Objective

Provide one mechanically extracted, **reviewable and line-referenced** inventory of the table snippets present in the existing *incomplete historical recovery*. This is a derived index, **not a database schema contract**, a migration design, an executable DDL artifact, or proof that all v1.0 tables were recovered.

The companion JSON preserves exact source section text verbatim, source line ranges and the first historical fenced declaration/inventory block. It does **not** fill missing columns or constraints with plausible guesses.

Companion file: `governance/evidence/SC-M01-DB-RECOVERED-TABLE-FRAGMENTS-2026-10-27.json`.

## Observed inventory — based strictly on the recovered source

| Schema | Named table fragments | Partial typed declaration blocks |
|---|---:|---:|
| `wcp_core` | 9 | 9 |
| `wcp_iam` | 2 | 2 |
| `wcp_infra` | 1 | 1 |
| `wcp_hosting` | 10 | 10 |
| `wcp_ops` | 6 | 5 |
| `wcp_commercial` | 4 | 4 |
| `wcp_audit` | 1 | 1 |
| `wcp_backup` | 3 | 1 |
| **TOTAL** | **36** | **33** |

### The three especially incomplete table fragments

1. `wcp_ops.job_attempts`: table name is recovered, but there is **no usable physical column declaration block** in the historical evidence.
2. `wcp_backup.backup_objects`: first fenced text block lists conceptual field names/metadata, without exact types/nullability/constraints.
3. `wcp_backup.restore_operations`: first fenced text block lists conceptual restore fields and correlation state, without exact physical definitions.

**Important:** none of the other 33 typed first-block fragments proves a complete table. Even for an observed `UUID PK`, missing indexes, FKs, CHECK/UNIQUE, state enums, migration boundaries, delete exceptions and retention still require Database owner review. The recovered inventory may be missing entire final tables because the full original v1.0 body is not available.

## Validation performed on the recovery evidence

- Extracted each source section beginning with a `###` heading containing an existing `wcp_<schema>.<table>` name, bounded by its next Markdown section heading.
- Confirmed **36 distinct table names** across the **8 recovered schemas**; no name duplicates.
- Observed **33** first fenced text blocks containing a recognized physical type, **2** untyped first-block inventories and **1** name-only fragment.
- Preserved source lines without synthesizing executable SQL, missing FK targets, enum values, index predicates or transaction clauses.

This is a syntactic census of the historical recovery file. It does not establish an official completeness metric for the v1.0 model.

## Prioritized DB owner decisions (not filled by this index)

The existing DB-R01..DB-R15 conformance matrix remains decisive:

- **First:** table-by-table exact physical definitions for all eight schemas, especially missing IAM/resource, job_attempts and backup/restore columns; exact PK/FK/tenant composite constraints and ownership.
- **Next:** indexes/uniqueness/nullability and exceptions to RESTRICT; durable idempotency key scopes, operations/outbox/audit transaction boundaries, worker leases/monotonic fencing, stale-worker and UNKNOWN_OUTCOME recovery.
- **Then:** operational retention and backup/restore cross-model boundaries, control-plane vs hosted data distinction, approved migration authority and compatibility classification.
- **For each decision:** label `RETAINED ORIGINAL FINAL CONTENT` vs `NEW DATABASE OWNER DECISION`. New owner decisions require honest content/version impact and cross-review by API/IPC, Security, QA, DevOps, SRE where applicable.

Do **not** mark any DB-R01..DB-R15 `APPROVED` from this index alone. Do not infer a `COMPLETE 36 TABLE MODEL` or treat the 33 partial typed fragments as migration-ready.

## Required publication action

Database #6 must provide **one self-contained, owner-final, verified physical contract**, not this evidence bundle:

`specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md`

If retained complete original source can be located, publish exact body with provenance and `CONTENT CHANGE = NONE`. If unavailable, Database explicitly decides all unrecovered content and publishes an **owner-amended/reconstructed** complete contract with real delta, explicit version impact and owner/cross-domain approval. The master 8/8 gate may advance only when that owner content is physically present, reviewed and source-code-consumable.

Current status:

```text
DB OWNER FINAL ARTIFACT = NOT MATERIALIZED
DB-R01..DB-R15 = OWNER DECISION REQUIRED
SC-M01 OWNER ARTIFACT GATE = 5 / 8
SC-M01 = PAUSED — TARGETED EVIDENCE
CODE-BASELINE = NONE
```
