# WCP360 — Handoffs and Next Steps

## Immediate conflict owners

### C01 family
API/IPC + Security + Modules:
- X001 Permission cleanup;
- X002 process identifiers;
- X003 daemon operation canonical status/count.

### C02 family
Security + DevOps + API/IPC:
- X004 privileged IPC-B;
- X005 Unix identities/groups/ACLs;
- X006 filesystem product root.

### C03 family
Database + Modules + Security + DevOps + SRE:
- X007 state authority;
- X008 Account/Organization/Tenant cardinalities;
- X009 reconciliation ownership.

### C04 family
SRE + Security + Database + API/IPC + Performance:
- X010 audit type/occurrence identity;
- X011 audit durability;
- X012 metric cardinality.

### C05 family
Tests/QA + MASTER:
- ingest detailed C05 return;
- resolve X013;
- reassess B09/B10.

## After conflict closure
1. MASTER final H3-CM consolidation.
2. Verify no critical conflict remains.
3. Authorize H3-V2 only if eligible.
4. H3-V2 verdict: PASS / CONDITIONAL PASS / FAIL.
5. Only PASS with no critical unresolved condition permits Baseline Manifest preparation.
6. Implementation Readiness review.
7. GO/NO-GO.
8. Source Code only after GO.

## Documentation owner recovery
If `WCP360 — Documentation projet détaillée` becomes usable again:
- provide provisional evidence;
- request confirm/correct/supersede/reject;
- do not restart corpus inspection from zero.
