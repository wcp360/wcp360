# WCP360 — SC-M01 Tests / QA: 26 security negatives + 40 browser-case proposed mappings

**Publication date:** 2026-10-27  
**Status:** P1 OWNER REVIEW CANDIDATE — **NON-CANONICAL / NOT EXECUTED**  
**Owner:** WCP360 — TESTS / QA; Security / Hardening validates security expectations.  
**Provenance:** `specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` (Git blob `6d74f2f363524511424f839ca7262143c2af95ab`).  
**QA baseline version:** v1.0 metadata only; QA owner final implementation export missing (#7).  
**Companion JSON:** `governance/proposals/SC-M01-QA-66-SECURITY-TEST-ANNEX-OWNER-CANDIDATE-v0.1.json`.

## What actually exists in the FINAL Security source

Section 14 publishes the `N01..N26` catalog as 26 existing **labels** with short security failure themes. **Do not create or reserve new canonical TEST IDs** from those labels alone.

The same source publishes the `TEST-SEC-WEB-001 → TEST-SEC-WEB-040` **range**, plus family coverage topics (surface/cookie, CSRF/Origin, CSP/XSS, SSRF, redirects, uploads/downloads, secrets/cache, MFA and reseller/rate-limit). **It does not publish exact one-to-one mapping between every numbered WEB test and a specific browser assertion.** Therefore the 40 individual WEB scenario assignments in this P1 JSON are newly proposed for QA review, **not recovered canonical mappings**.

Security source §13 does already fix several expected invariant outcomes, including a `403` response for state-changing browser requests with missing/invalid CSRF or exact Origin, no mutation and security evidence; explicit WCP-USER/WCP-ADMIN session separation; typed SSRF restrictions; no generic unsafe `innerHTML`, `eval`, etc.; and no sensitive output in logs/errors. We preserve that precision only where the source supports it.

## Source-anchored case inventory (proposals, not product PASS)

| Existing security reference kind | Source range | Case records | Proposed owner result |
|---|---|---:|---|
| Privileged negative source labels | N01–N26 | 26 | Per-label rejection mechanism, no-effect assertion, safe error category candidate and security evidence |
| Browser security existing ID range | TEST-SEC-WEB-001–040 | 40 | **NEW PROPOSED** one-to-one test-scenario mapping to canonical §13 invariants |
| **Total** | **Existing labels / ID range only** | **66** | **0 approved, 0 executed, 0 PASS/FAIL** |

Each JSON entry specifies its `source`, `status = BLOCKED`, `execution_state = NOT_EXECUTED`, `owner_approval=false`, scenario and forbidden side effect, planned evidence, and exact dependencies. Browser mappings should be amended/reordered as desired by Tests/QA; the ID range is source-approved but the proposed mapping is not.

## How this supplements the other QA preparatory work

- 86 existing TEST-API + 47 existing TEST-IPC **reference** records are reviewed in `governance/proposals/SC-M01-QA-133-TEST-RECORDS-OWNER-CANDIDATE-v0.1.json` (PR #98). All 133 are BLOCKED / NOT_EXECUTED.
- The 26 existing N labels and 40 WEB references provided here are different families: together there are **199 reviewed reference/case records** in the combined proposed corpus, **not 199 executed tests**. They may overlap semantically; Tests/QA must choose inheritance, deduplication, activation and exact QA TEST IDs.
- Existing `governance/proposals/SC-M01-QA-A01-A20-OWNER-DECISION-PACKET-v0.1.md` is still the **owner decision matrix**, including provenance choice P1 exact retained final / P2 first canonical materialization / P3 version change and QI01..QI07 execution integrity.
- Other SC-M01 DB, concurrency, release/rollback, state transitions and recovery test families in the 2026-10-03 owner candidate are **not automatically covered by these 199 records**.

## Mandatory specialized owner steps (do not skip)

1. **TESTS / QA** must ratify A01–A20 and the 7 QI integrity clauses; choose provenance P1/P2/P3 honestly (earlier complete QA final v1.0 body is not evidenced); define actual canonical lifecycle and inherited record schema.
2. For **N01..N26**, approve exact rejection authority/component, positive and negative fixture, expected stable API/IPC error per activated behavior, forbiddance of any external effect, audit/log evidence and failure injection.
3. For **WEB001..040**, explicitly **ACCEPT/AMEND/REPLACE** proposed one-to-one ID mapping. Record exact browser/ingress/proxy/backend/accepted-credential observations where cookies and topology are affected; never fabricate unapproved routes or HTTP codes.
4. Preserve source-approved security rules and fail closed for any wrong peer/surface/tenant/resource/parent grant. Security/API #91 parent binding tuples remain undecided, as do some C03/C06/C07 choices.
5. DB #6 final and Engineering #9 final are still required for physical fixture and build-quality tests. No tests were executed against a WCP360 application.
6. Publish one complete owner-final self-contained Test Baseline under `specifications/tests/`, with source SHA, honest content/version change, independent cross-domain reviews and QA owner approval. Only then could #7 be counted in the 8/8 global gate.

## Exact state

```text
SOURCE-REFERENCED API + IPC CANDIDATES = 133
SOURCE-REFERENCED SECURITY NEGATIVE + WEB CASE CANDIDATES = 66
TOTAL REFERENCE/CASE RECORDS PREPARED = 199
PRODUCT TESTS EXECUTED = 0
QA OWNER TEST BASELINE FINAL APPROVAL = MISSING
SC-M01 CANONICAL IMPLEMENTATION ARTIFACTS = 5/8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

This is P1 archival review material, not an app-test run, official QA TEST ID publication, audit finding closure or new implementation authority.
