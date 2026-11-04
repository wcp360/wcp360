# Contributing to WCP360

WCP360 uses owner-domain governance and pull-request traceability.

## Core rule

No contributor or workstream may silently redefine a contract owned by another WCP360 authority.

Cross-domain impact requires a **WCP360 HANDOFF**.

Contradictions require a **WCP360 CONFLICT** routed to MASTER / Project Control and, when needed, AUDIT / ARCHITECTURE REVIEW.

## Repository locations

Use [documentation/README.md](documentation/README.md) for specifications, contracts, governance and manifests. Follow the [development strategy](documentation/development/README.md): Core application/modules/agents in WCP360_Core, detached packages in WCP360_Plugins, the public client boundary in WCP360_SDK, construction/qualification in WCP360_Build and qualified records in WCP360_Releases. Runtime implementation and acceptance remain explicitly bounded. The [path migration map](documentation/PATH-MIGRATION.json) translates earlier root locators; update active consumers and rerun relevant checks before merging an older branch. Keep immutable citations and historical evidence intact.

## Canonical artifact publication

An owner artifact may be promoted to `documentation/specifications/` only when its owner publication is identifiable and final.

Do not reconstruct missing canonical artifacts from older audits, staging files or general knowledge.

## Secrets

Never commit passwords, API tokens, private keys, production credentials, database dumps, MFA seeds, recovery codes, sensitive production logs or customer data.

## Current implementation gate — November 4, 2026

```text
CANONICAL SPECIFICATION INPUTS = 8/8 ACCEPTED
SC-M01 = LIMITED RESUME — INERT DAEMON FOUNDATION ONLY
CORE M1-S1 = SEPARATE BOUNDED DEVELOPMENT/TEST SLICE (#130)
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

The [baseline manifest](documentation/manifests/WCP360-BM-0001.md) preserves the limited inert-daemon authorization and historical publication decisions. The earlier paused publication-stage passage does not override its current gate or the subsequent explicit Core M1-S1 decision. An unrelated roadmap item, WS1 harness, privileged dispatcher or production operation is not authorized by either slice.

The [consolidation record](documentation/governance/decisions/MASTER-CONSOLIDATION-2026-11-04.md) reconciles the existing documentation branch without changing frozen contracts. Repository tooling and targeted documentation maintenance may continue. Preserve the separate source-bound decision before each integration.

## Review and merge evidence

The historical [review policy](https://github.com/wcp360/wcp360/blob/da265278c20e230d5ba4614647a8271f4b0d7506/documentation/governance/REVIEW-MERGE-POLICY.md) is qualified for current candidate work by the owner's [single-maintainer decision](documentation/governance/decisions/D-GOV-SOLO-0001.md).

Automated checks and same-author assessment are not independent review. The owner's alternate accounts do not supply independent E3. Current candidate integration instead requires an actual technical/security assessment, exact source/base, applicable terminal tests, contract-impact and rollback records, and explicit MASTER disposition. Concrete unresolved defects remain blocking. No fake reviewer, team or sub-agent approval is permitted; CODEOWNERS assignment is not evidence of review.

Branch-protection checks may be required only after they reliably exist on the specific target branch. Proposed templates are not installed protection. Never create a new branch or push directly/force-push to a permanent branch. All new code and technical documentation are English.

Candidate integration does not establish a CODE-BASELINE, release or production acceptance. Those gates require their own explicit ratification; no independent qualification is claimed by this document.
