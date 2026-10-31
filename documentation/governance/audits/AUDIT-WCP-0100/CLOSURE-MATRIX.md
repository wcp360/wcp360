# AUDIT-WCP-0100 — Closure Matrix

> **Historical snapshot — October 3, 2026.** Integrated from `audit/closure-matrix-refresh-2026-10-03` at `1fdbe4994f54ddc7682fba03a8b0c37038608b45` during the [October 31 branch reconciliation](../../branches/BRANCH-RECONCILIATION-2026-10-31.md). The body below retains that snapshot's 5/8, paused, missing-owner-artifact and CI statements. Current authority is the [baseline manifest](../../../manifests/WCP360-BM-0001.md): 8/8 accepted inputs, limited inert-daemon resume, CODE-BASELINE NONE. This publication does not verify or close any finding.

**Reference date:** 2026-10-03  
**Main reference:** `7a248e907951789e47c29d95dd3733f8288dc6b6`  
**Audit tracker:** issue #41  
**Implementation gate:** issue #13  
**Status:** HISTORICAL CLOSURE CONTROL — NO FINDING AUTO-CLOSED

This matrix translates the 18 audit findings into explicit closure conditions and evidence.

It does not replace the detailed finding files. It does not allow the author of a remediation to fabricate an independent verification. A finding remains \`OPEN\` until the responsible owner and/or Audit/Architecture Review records the required verification.

## 1. Status model

Two dimensions are tracked separately.

### Finding status

Canonical audit lifecycle remains:

\`\`\`text
OPEN
ACCEPTED
FIX IN PROGRESS
VERIFIED
CLOSED
\`\`\`

This matrix does not silently change those statuses.

### Remediation readiness

The following coordination labels describe current evidence only:

\`\`\`text
NOT STARTED
BLOCKED
PROPOSAL READY FOR OWNER REVIEW
FIX IN REVIEW
PARTIALLY VERIFIED
READY FOR AUDIT/MASTER VERIFICATION
VERIFIED BY INDEPENDENT EVIDENCE
\`\`\`

\`READY FOR AUDIT/MASTER VERIFICATION\` is not \`VERIFIED\`.

## 2. Summary

| Group | Count |
|---|---:|
| Findings tracked | 18 |
| Canonically CLOSED by this matrix | **0** |
| Ready for Audit/MASTER verification | **3** |
| Fix/proposal in open PR | **10** |
| Blocked on owner artifact / external review / admin control | **4** |
| Partial publication/provenance work | **1** |

Candidates currently ready for Audit/MASTER verification:

\`\`\`text
AUDIT-WCP-0101
AUDIT-WCP-0115
AUDIT-WCP-0116
\`\`\`

They are not closed because the same assistant prepared and checked the remediation.

## 3. Per-finding closure matrix

### AUDIT-WCP-0101 — artifact presence vs implementation readiness

**Finding status:** OPEN  
**Remediation readiness:** READY FOR AUDIT/MASTER VERIFICATION  
**SC-M01 relevance:** global gate / governance

Implemented evidence in main:

- \`governance/SC-M01-CURRENT-READINESS-2026-10-02.md\`;
- updated \`governance/PROJECT-STATUS.md\`;
- separate 5/8 artifact publication state from semantic owner decisions, QA execution and CODE-BASELINE;
- identifies PR #42 as repository tooling and PR #43 as non-normative proposals;
- keeps all 18 findings open;
- preserves SC-M01 PAUSED.

**Closure proof required:**

1. Audit/MASTER reviews the current readiness file at an exact SHA;
2. confirms no status such as FINAL/PRESENT is interpreted as test/implementation verification;
3. confirms future status summaries derive from the same current-state source rather than contradictory manual copies;
4. records VERIFIED/CLOSED decision in #41 or an owner-approved audit record.

**Remaining dependency:** independent verification only.

---

### AUDIT-WCP-0102 — missing Database / QA / Engineering owner artifacts

**Finding status:** OPEN  
**Remediation readiness:** BLOCKED  
**SC-M01 relevance:** global 8/8 gate

Current evidence:

- #6 Database: final/source-ready metadata confirmed; full final body existence confirmed historically; substantial recovery evidence is now preserved, but recovery is explicitly exhausted before unsafe reconstruction and the exact final owner artifact is still absent.
- #7 QA: baseline metadata v1.0 approved; no historical complete final artifact was found; a self-contained non-canonical owner candidate and source-derived coverage index now exist, but TESTS / QA has not published the final canonical artifact.
- #9 Engineering: final/source-ready metadata confirmed; exact final body is absent; Phase G/H3-C candidate evidence is preserved separately and explicitly cannot be promoted.

Recovery/preparation has reduced rediscovery but does not change the 5/8 gate.

**Closure proof required:**

- exact complete files in \`specifications/\`;
- owner/version/provenance;
- historical content excluded;
- ready-for-source-code statement;
- cross-check against other owner artifacts;
- QA final publication actually confirmed.

**Remaining dependencies:** #6, #7, #9.

---

### AUDIT-WCP-0103 — API transport / schema / DTO incompleteness

**Finding status:** OPEN  
**Remediation readiness:** PROPOSAL READY FOR OWNER REVIEW  
**SC-M01 relevance:** blocks each HTTP endpoint actually implemented in foundation

Evidence:

- PR #43:
  \`governance/proposals/SC-M01-API-TRANSPORT-DELTA-v0.1.md\`;
- decisions H01–H10;
- HQ01–HQ15 proposed test cases.

The proposal does not invent real routes or methods.

**Closure proof required:**

For each SC-M01 API endpoint:

- method + route;
- parameter locations/types;
- DTO definitions;
- HTTP error map;
- auth/session/token/CSRF rules;
- idempotency source;
- cache/pagination/discriminator rules as applicable;
- owner acceptance/version impact;
- QA mappings.

**Remaining dependency:** API/IPC owner decision; Security for auth transport; QA #7.

---

### AUDIT-WCP-0104 — permission/action/parent/primitive bindings

**Finding status:** OPEN  
**Remediation readiness:** PROPOSAL READY FOR OWNER REVIEW  
**SC-M01 relevance:** blocks authorization/privileged dispatcher

Evidence:

- PR #43 authorization v0.2;
- B01–B05;
- D01–D04;
- origin action vs technical action separated;
- 17 proposed QA cases.

**Closure proof required:**

- exact owner-approved tuples;
- no wildcard/prefix/implicit alias;
- context/action semantics ratified;
- grants.set → apply/revoke relation fixed;
- positive ALLOW and negative substitution tests.

**Remaining dependency:** API/IPC + Security owners; Database for grant-set physical references; QA.

---

### AUDIT-WCP-0105 — backend/worker → wcpd control boundary

**Finding status:** OPEN  
**Remediation readiness:** PROPOSAL READY FOR OWNER REVIEW  
**SC-M01 relevance:** blocks control daemon transport

Evidence:

- merged P1 control-boundary v0.2;
- C01 = recovered H3-CM M1 owner consensus requiring current republication/confirmation;
- C02 = already defined by current DevOps/Security;
- C03–C07 = remaining OPEN semantic decisions;
- CQ01–CQ12 remain PLANNED;
- worker is not silently added to \`wcp360-control\`.

**Closure proof required:**

- current owner republication/confirmation of recovered socket identity;
- peer authentication;
- USER/ADMIN surface binding;
- worker path;
- trusted-context producer/resolver/lifecycle;
- protocol schema/version/errors;
- tests for wrong peer/surface/context/target.

**Remaining dependency:** API/IPC + Security + DevOps; #6 if durable worker path selected; QA.

---

### AUDIT-WCP-0106 — persistent idempotency

**Finding status:** OPEN  
**Remediation readiness:** PROPOSAL + DATABASE BLOCKER  
**SC-M01 relevance:** blocks durable mutations/jobs

Evidence:

- PR #43 timeout/idempotency T05/T06;
- API transport H04;
- recovered DB history names \`wcp_ops.idempotency_records\` but exact physical contract is unavailable.

**Closure proof required:**

- authoritative key source;
- scope/canonical semantic request;
- acquisition concurrency;
- stored logical result;
- conflict semantics;
- cross-tenant prevention;
- expiry/reuse safety;
- crash-before/after-effect behavior;
- exact DB uniqueness/transaction references.

**Remaining dependency:** #6 + API/IPC + Security + QA.

---

### AUDIT-WCP-0107 — IPC timeout/deadline phases

**Finding status:** OPEN  
**Remediation readiness:** PROPOSAL READY FOR OWNER REVIEW  
**SC-M01 relevance:** blocks IPC client/server behavior

Evidence:

- PR #43 timeout/idempotency v0.1;
- phases P0–P6;
- T01–T04;
- TQ01–TQ10.

**Closure proof required:**

- I/O timeout phase meaning;
- \`deadline_ms\` representation;
- effective budget formula/scope;
- before/after dispatch timeout outcomes;
- tests for >5s operations, partial framing, expired deadline and lost response.

**Remaining dependency:** API/IPC + DevOps + QA.

---

### AUDIT-WCP-0108 — durable jobs / concurrency / recovery

**Finding status:** OPEN  
**Remediation readiness:** PROPOSAL + DATABASE BLOCKER  
**SC-M01 relevance:** blocks effectful async execution/recovery

Evidence:

- merged owner-decision pack T06/T07;
- existing durable-intent → dispatch → observe → durable-result invariants;
- recovered final DB evidence now preserves jobs, job_attempts, leases/fencing, outbox, event records, transaction distinctions, audit durability, reconciliation, partitioning-not-required-v1.0 and backup/restore/migration boundaries;
- recovery is still incomplete and the exact canonical DB artifact remains required.

**Closure proof required:**

- durable transaction boundaries;
- claim/lease/fencing or approved equivalent;
- stale executor prevention;
- late result handling;
- reboot recovery;
- audit-unavailable path;
- failure injection before/after dispatch;
- reconciliation proves no blind retry.

**Remaining dependency:** #6, API/IPC, Security, SRE, QA.

---

### AUDIT-WCP-0109 — repository protections and CI enforcement

**Finding status:** OPEN  
**Remediation readiness:** FIX IN REVIEW / ADMIN BLOCKER  
**SC-M01 relevance:** before executable contributions are accepted

Evidence:

- PR #42 contains read-only CI, repository/path checks and targeted secret checking;
- latest synchronized head: `e0766718892bbcd6232e7eb3ed16482e3b38094b`;
- latest run: `37079982904` = SUCCESS;
- both jobs and all steps succeed;
- main still reports branch protection disabled.

**Closure proof required:**

1. independent review of PR #42;
2. merge accepted fix;
3. branch protection/ruleset or equivalent governance compatible with account capabilities;
4. required checks actually enforced;
5. negative proof that failed required check/review prevents merge/push where policy says so;
6. bypass/force-push policy reviewed.

**Remaining dependency:** real reviewer + GitHub administration capability.

---

### AUDIT-WCP-0110 — independent technical review

**Finding status:** OPEN  
**Remediation readiness:** BLOCKED / PROCESS  
**SC-M01 relevance:** first CODE-BASELINE and critical repository changes

Current evidence:

- same assistant/user produced and self-checked PRs;
- reviews labeled COMMENT explicitly state they are not independent;
- no second identity is fabricated.

**Closure proof required:**

- technically separate reviewer or formally accepted mono-maintainer alternative defined by governance;
- review attached to exact final SHA;
- unresolved comments addressed;
- evidence that author self-comment is not counted as independent approval.

**Remaining dependency:** actual review process/person/account outside this assistant-author identity.

---

### AUDIT-WCP-0111 — overbroad .gitignore

**Finding status:** OPEN  
**Remediation readiness:** FIX IN REVIEW  
**SC-M01 relevance:** repository reproducibility/source tracking

Evidence in PR #42:

- before: 41/52 probes satisfy expectations;
- candidate: 52/52;
- negative mutation controls;
- CI has remained green after multiple main synchronizations;
- sources/migrations/test fixtures can be trackable while secrets/dumps remain ignored.

**Closure proof required:**

- Engineering/DevOps review candidate rules;
- merge fix;
- re-run on merged main;
- verify sensitive exclusions and source paths still behave as expected.

**Remaining dependency:** review/merge only.

---

### AUDIT-WCP-0112 — 147-file mirror vs full DOC baseline

**Finding status:** OPEN  
**Remediation readiness:** PARTIALLY VERIFIED  
**SC-M01 relevance:** provenance/documentation; #16 explicitly not current SC-M01 global gate blocker

Evidence:

- publication report in main;
- 147/147 entries match committed mirror manifest;
- mirror and canonical DOC archive explicitly separated;
- Design mirror content not automatically promoted.

Still missing:

- physical exact DOC archive/corpus publication #16.

**Closure proof required:**

- exact canonical DOC source materialized when available;
- preserved paths;
- digest/inventory;
- no substitution by older archive;
- Documentation-targeted validation only, no general re-audit.

**Remaining dependency:** #16.

---

### AUDIT-WCP-0113 — build inputs / toolchain / rights / secrets

**Finding status:** OPEN  
**Remediation readiness:** PROPOSAL READY FOR OWNER REVIEW / ENGINEERING DEPENDENCY  
**SC-M01 relevance:** buildable foundation

Evidence:

- DevOps final fixes Debian 13 amd64, product root and reproducibility principles;
- merged P1 Toolchain/Build v0.1 records F01–F10 and FQ01–FQ15 without choosing versions;
- cancelled Sprint 0 stack is explicitly excluded.

**Closure proof required for SC-M01 toolchain:**

- application/daemon runtime decisions;
- PostgreSQL client/version policy;
- package/dependency managers and locks;
- canonical build/test commands;
- static analysis/quality gates;
- build environment;
- dependency-source policy;
- artifact identity/provenance;
- service/file rights for implemented components;
- successful clean build/test on supported target.

**Remaining dependency:** #9, #6 for DB migration specifics, DevOps/Security/QA decisions.

---

### AUDIT-WCP-0114 — QA assertions and execution evidence

**Finding status:** OPEN  
**Remediation readiness:** BLOCKED ON QA FINAL / IMPLEMENTATION  
**SC-M01 relevance:** validation of every implemented component

Evidence:

- final QA owner artifact remains missing;
- merged self-contained QA owner candidate defines test records, execution evidence, levels/families, dependency handling, status transitions and CODE-BASELINE evidence without creating PASS/FAIL;
- canonical coverage index is source-derived from unchanged API/IPC/Permission blobs;
- independent recount verifies 86/86 API rows with 0 field/test-suffix mismatches, 47/47 IPC rows with 0 field/test-suffix mismatches, and 82/82 active permissions with 0 surface/status/API-binding mismatches;
- merged proposal pack contains 85 local PLANNED scenarios;
- PR #42 has real repository-tool tests, explicitly not product QA.

**Closure proof required:**

- final QA baseline #7;
- requirements → executable assertions;
- exact SHA/environment/result;
- positive/negative/recovery proof;
- no fake PASS/FAIL.

**Remaining dependency:** #7 + implementation candidates.

---

### AUDIT-WCP-0115 — owner-domain authority vs folder precedence

**Finding status:** OPEN  
**Remediation readiness:** READY FOR AUDIT/MASTER VERIFICATION  
**SC-M01 relevance:** governance/conflict resolution

Merged remediation:

- PR #46 → main \`25fd86225cbf0d9df2eba6ee3af5bd07be1e6ca4\`;
- \`AUTHORITY-PRECEDENCE.md\` now separates:
  - manifest/repository-source selection;
  - owner-domain technical authority;
  - cross-domain composition/conflict procedure;
- folder/date/numeric version/Master summary cannot silently override another owner.

**Closure proof required:**

- Audit/MASTER verifies rule against representative same-domain and cross-domain conflict examples;
- confirms no contradiction with BM-0001;
- records VERIFIED/CLOSED.

**Remaining dependency:** independent Audit/MASTER verification only.

---

### AUDIT-WCP-0116 — operational SC-M01 scope

**Finding status:** OPEN  
**Remediation readiness:** READY FOR AUDIT/MASTER VERIFICATION  
**SC-M01 relevance:** exact implementation scope

Merged remediation:

- \`governance/SC-M01-SCOPE.md\`;
- resume checklist links to it;
- keeps global gate 8/8;
- defines first implementation foundation classes;
- broad functional modules and SC-M02 remain separate;
- future-module detail does not automatically block unrelated foundation work;
- component-specific unresolved semantic behavior can still block its component after 8/8.

**Closure proof required:**

- MASTER/Modules/Engineering/QA verify scope does not silently authorize broad implementation;
- accepted mapping from first implementation package to scope;
- records VERIFIED/CLOSED.

**Remaining dependency:** independent owner/Audit verification only.

---

### AUDIT-WCP-0117 — USER/ADMIN cookie topology

**Finding status:** OPEN  
**Remediation readiness:** PARTIALLY DEFINED / OWNER + DEPLOYMENT PROOF REQUIRED  
**SC-M01 relevance:** web/session deployment

Evidence:

- merged Security Web/Authz v0.2 plus canonical-composition evidence;
- direct cross-check against Security v1.0 confirms separate USER/ADMIN sessions/cookies/namespaces/surface claims, synchronizer CSRF + exact Origin, fail-closed handling and current-state revocation;
- SQ01–SQ07 remain PLANNED;
- ports alone are not treated as cookie isolation and no hostname/topology is silently chosen.
- remaining gap is deployment topology/non-exposure proof and detailed wrong-cookie/proxy/WSS behavior, not redesign of the core session/CSRF semantics.

**Closure proof required:**

- Security/DevOps topology decision;
- exact routing/TLS/proxy/cookie behavior;
- browser/proxy/backend observation demonstrating non-exposure criterion;
- wrong-surface session refusal.

**Remaining dependency:** Security + DevOps + QA environment.

---

### AUDIT-WCP-0118 — permission priority vs Security intersection

**Finding status:** OPEN  
**Remediation readiness:** PARTIALLY DEFINED / OWNER HARMONIZATION REQUIRED  
**SC-M01 relevance:** authorization engine

Evidence:

- merged Security Web/Authz v0.2 plus independently cross-checked canonical-composition evidence;
- Security v1.0 already canonically defines final AUTHZ as an intersection;
- Permission Registry already defines explicit DENY > account override > role grants > plan/feature envelope > default policy;
- DEMO hard restriction, reseller effective-envelope ceiling, temporary privilege non-role mutation, approval non-bypass and UI/context non-authority are already final;
- account positive-override ceiling/internal representation and external error projection remain owner decisions;
- SQ08–SQ16 remain PLANNED.

**Closure proof required:**

- Security owner decision/table/algorithm;
- account override positive/negative semantics;
- plan/feature/entitlement relation;
- delegation ceiling/revocation;
- approval/privilege non-bypass;
- QA cases executed.

**Remaining dependency:** Security + Database representation + QA.

## 4. What may be verified next without the missing three artifacts

Candidates:

1. **0101** — current-state reporting/gating;
2. **0115** — owner-domain authority;
3. **0116** — foundation scope.

These are governance findings and now have merged remediation in main.

They should be verified by Audit/MASTER using exact main commit:

\`25fd86225cbf0d9df2eba6ee3af5bd07be1e6ca4\`

No other finding should be marked CLOSED merely from the current evidence.

## 5. What must remain blocked

Global evidence:

\`\`\`text
#6 DATABASE exact owner body
#7 QA final owner artifact
#9 ENGINEERING exact owner body
\`\`\`

Process/admin:

\`\`\`text
0110 independent review
0109 branch protection/enforcement
\`\`\`

Physical data persistence:

\`\`\`text
0106 / 0108 portions depending on #6
\`\`\`

QA execution:

\`\`\`text
0114 and all product-level cases until #7 + candidate implementation
\`\`\`

## 6. Work packages

### PR #42

Repository tooling/fixes.

Current synchronized candidate:

`e0766718892bbcd6232e7eb3ed16482e3b38094b`

Latest verified CI:

`37079982904 = SUCCESS`

Merge-test:

`440cd505da0b94eb11aea8830c4ae692245092a6`

Evidence: 304 files / 1,216,972 bytes, mirror 147/147, audit 4/4, targeted secret findings 0.

Still requires independent review and merge/enforcement proof.

### PR #43

Merged P1 non-normative owner-decision pack in main \`0aabae81fe6509b7e6567ef5ab12ecc1b9e86b1d\`.

Current decision families:

\`\`\`text
Authorization
IPC timeout/idempotency
Control boundary
API transport
Security Web/Authz
Toolchain/Build
\`\`\`

Central matrix tracks 47 decision points: 45 OPEN semantic decisions, C01 recovered/republication, C02 already defined, and 85 proposed QA cases.

No proposal became an owner decision by merge or by this audit.

## 7. Closure rule

A finding may move to \`VERIFIED\` only when its closure criteria are satisfied with evidence attached to exact files/commits/environment as applicable.

A finding may move to \`CLOSED\` only after:

1. verification is accepted by the responsible Audit/MASTER process;
2. remaining dependencies are none for the finding's defined scope;
3. no contradictory active owner artifact remains;
4. required regression/test evidence exists where executable verification is applicable.

This prevents “document exists” from being treated as “behavior verified”.


## 8. Review-evidence policy now in main

Main also contains \`governance/REVIEW-MERGE-POLICY.md\`:

\`\`\`text
E0 = authored
E1 = automated checks
E2 = same-author verification/publication control
E3 = independent technical review
\`\`\`

PR #42 currently has E1 + E2 and remains blocked on E3 under class R1.

The connector available in this workflow does not expose a mutation for branch-protection/ruleset administration. Main still reports \`protected=false\`; no enforcement is claimed.


## 9. 2026-10-03 evidence refresh

### Database recovery stopping rule

Historical recovery is now classified:

```text
SUBSTANTIAL BUT INCOMPLETE
SAFE AUTO-RECONSTRUCTION = NO
OWNER REPUBLICATION / AMENDMENT REQUIRED
```

The recovery evidence explicitly records unrecovered INFRA details, exact retention policy, exact index names/order and any unsurfaced owner clauses. These gaps must remain gaps rather than being filled from architecture intuition.

### QA source-derived coverage

The QA coverage index was independently recomputed against the unchanged canonical source blobs:

```text
API contracts / TEST refs = 86 / 86
API field mismatches = 0
IPC contracts / TEST refs = 47 / 47
IPC field mismatches = 0
active permissions = 82
permissions with exact index rows = 82
permission/API-binding mismatches = 0
```

This is reference coverage, not executable behavioral coverage.

### Security narrowing

Direct canonical-source checks confirm the core semantics underlying 0117/0118 are already final. The remaining work is targeted owner/deployment harmonization and executable proof, not replacement of the Security model.

### Repository guardrails

PR #42 remains E1 + E2 only even though the current-main candidate is green. E3 and merge/enforcement proof remain required by REVIEW-MERGE-POLICY.
